<?php

class CRM_Smsautoreply_Reply {

  protected static $instance;

  protected static $is_kid_extension_installed = -1;

  protected $validSmsActivities = array();
  protected $incomingSmsSubjects = array();

  protected static $enabled = true;

  protected function __construct() {
    $activityTypeID = CRM_Core_OptionGroup::getValue('activity_type', 'Inbound SMS', 'name');
    $this->validSmsActivities[] = $activityTypeID;

    $this->incomingSmsSubjects[] = 'SMS Received';
  }

  public static function disable() {
    self::$enabled = false;
  }

  public static function enable() {
    self::$enabled = true;
  }

  /**
   * Singlegon patter
   * 
   * @return CRM_Smsautoreply_Reply
   */
  public static function singleton() {
    if (!self::$instance) {
      self::$instance = new CRM_Smsautoreply_Reply();
    }
    return self::$instance;
  }

  /**
   * Delegation of post hook
   * 
   * Check if an incoming SMS activity is created
   * 
   * @param type $op
   * @param type $objectName
   * @param type $objectId
   * @param type $objectRef
   */
  public function post($op, $objectName, $objectId, &$objectRef) {
    if ($op == 'create' && $objectName == 'Activity' && self::$enabled) {
      //check if subject is valid
      if ($this->isValidActivity($objectRef)) {
        //ok this is an incoming sms
        $target_contact_ids = CRM_Smsautoreply_Reply::retrieveTargetIdsByActivityId($objectRef->id);
        if (count($target_contact_ids)) {
            $source_contact_id = CRM_Activity_BAO_Activity::getActivityContact($objectRef->id, 2); //activity source contact
            $this->process($objectRef->details, $objectRef->phone_number, $target_contact_ids, $source_contact_id);
        }        
      }
    }
  }

/**
   * function to retrieve id of target contact by activity_id
   *
   * @param int    $id  ID of the activity
   *
   * @return mixed
   *
   * @access public
   *
   */
  public static function retrieveTargetIdsByActivityId($activity_id) {
    $targetArray = array();
    if (!CRM_Utils_Rule::positiveInteger($activity_id)) {
      return $targetArray;
    }

    $sql = '
            SELECT contact_id
            FROM civicrm_activity_contact
            JOIN civicrm_contact ON contact_id = civicrm_contact.id
            WHERE activity_id = %1 and `record_type_id` = 3
        ';
    $target = CRM_Core_DAO::executeQuery($sql, array(1 => array($activity_id, 'Integer')));
    while ($target->fetch()) {
      $targetArray[] = $target->contact_id;
    }
    return $targetArray;
  }
  
  protected function isValidActivity($activity) {
    if (in_array($activity->activity_type_id, $this->validSmsActivities)) {
      return true;
    }
    
    return false;;
  }

  /**
   * Find auto ies based on this incoming SMS 
   * 
   * @param type $message
   * @param type $from_phone
   * @param type $from_contact_id
   * @param type $to_contact_id
   */
  protected function process($message, $from_phone, $from_contact_ids, $to_contact_id) {    
    $sql = 'SELECT * FROM `civicrm_sms_autoreply` WHERE %1 LIKE CONCAT(`keyword`, "%") AND `is_active` = "1" ORDER BY `weight`, `id` LIMIT 0,1';
    $replies = CRM_Core_DAO::executeQuery($sql, array(
          1 => array(html_entity_decode($message), 'String'),
    ), TRUE, 'CRM_Smsautoreply_DAO_SmsAutoreply');

    while ($replies->fetch()) {
      $this->reply($replies, $from_phone, $from_contact_ids, $to_contact_id);
    }
  }

  /**
   * Send a reply
   * 
   * @param type $reply
   * @param type $to_phone
   * @param type $to_contact_ids
   * @param type $from_contact_id
   */
  protected function reply($reply, $to_phone, $to_contact_ids, $from_contact_id) { //, $provider_id, $from_contact_id, $charge, $financial_type_id, $subject) {
    $data['reply'] = $reply->reply;
    $data['subject'] = $reply->subject;
    $data['provider_id'] = $reply->provider_id;
    $data['to_phone'] = $to_phone;
    $data['to_contact_ids'] = $to_contact_ids;
    $data['from_contact_id'] = $from_contact_id;
    $data['charge'] = $reply->charge;
    $data['financial_type_id'] = $reply->financial_type_id;

    $strData = serialize($data);
    CRM_Core_DAO::executeQuery("INSERT INTO `civicrm_sms_autoreply_queue` (date, data) VALUES(NOW(), %1)", array(1 => array($strData, 'String')));
  }

  public function processQueue($limit) {
    $dao = CRM_Core_DAO::executeQuery("SELECT * FROM `civicrm_sms_autoreply_queue` ORDER BY `date` ASC LIMIT %1", array(1=>array($limit, 'Integer')));
    $processed = array();
    while($dao->fetch()) {
      $processed[] = $dao->id;
      try {
        $data = unserialize($dao->data);

        $contactDetails = $this->getContactDetails($data['to_contact_ids'], $data['to_phone']);
        $activityParams['text_message'] = $data['reply'];
        $activityParams['activity_subject'] = $data['subject'];
        $smsParams['provider_id'] = $data['provider_id'];
        if ($data['charge']) {
          $smsParams['charge'] = $data['charge'];
        }
        if ($data['financial_type_id']) {
          $smsParams['financial_type_id'] = $data['financial_type_id'];
        }
        CRM_Activity_BAO_Activity::sendSMS($contactDetails, $activityParams, $smsParams, $data['to_contact_ids'], $data['from_contact_id']);
      } catch (Exception $e) {
        CRM_Core_Error::debug_log_message('Error in processing sending autoreply: ' . $e->getMessage() . "\r\n\r\n" . $e->getTraceAsString());
      }
    }
    if (count($processed) > 0) {
      CRM_Core_DAO::executeQuery("DELETE FROM `civicrm_sms_autoreply_queue` WHERE id IN (" . implode(", ", $processed) . ")");
    }
  }

  protected function getContactDetails($contactIds, $phone) {
    $returnProperties = array(
      'sort_name' => 1,
      'phone' => 1,
      'do_not_sms' => 1,
      'is_deceased' => 1,
      'display_name' => 1,
    );
    
    list($contactDetails) = CRM_Utils_Token::getTokenDetails($contactIds, $returnProperties, FALSE, FALSE);
    foreach($contactIds as $contact_id) {
      //to check if the phone type is "Mobile"
      $phoneTypes = CRM_Core_OptionGroup::values('phone_type', TRUE, FALSE, FALSE, NULL, 'name');

      $formatFrom = $this->formatPhone($this->stripPhone($phone), $like, "like");
      $escapedFrom = CRM_Utils_Type::escape($formatFrom, 'String');
      $contactDetails[$contact_id]['phone_id'] = CRM_Core_DAO::singleValueQuery('SELECT id FROM civicrm_phone WHERE phone LIKE "' . $escapedFrom . '"');
      $contactDetails[$contact_id]['phone'] = $phone;
      $contactDetails[$contact_id]['phone_type_id'] = CRM_Utils_Array::value('Mobile', $phoneTypes);
    }
    return $contactDetails;
  }
  
  private function stripPhone($phone) {
    $newphone = preg_replace('/[^0-9x]/', '', $phone);
    while (substr($newphone, 0, 1) == "1") {
      $newphone = substr($newphone, 1);
    }
    while (strpos($newphone, "xx") !== FALSE) {
      $newphone = str_replace("xx", "x", $newphone);
    }
    while (substr($newphone, -1) == "x") {
      $newphone = substr($newphone, 0, -1);
    }
    return $newphone;
  }

  private function formatPhone($phone, &$kind, $format = "dash") {
    $phoneA = explode("x", $phone);
    switch (strlen($phoneA[0])) {
      case 0:
        $kind = "XOnly";
        $area = "";
        $exch = "";
        $uniq = "";
        $ext  = $phoneA[1];
        break;

      case 7:
        $kind = $phoneA[1] ? "LocalX" : "Local";
        $area = "";
        $exch = substr($phone, 0, 3);
        $uniq = substr($phone, 3, 4);
        $ext  = $phoneA[1];
        break;

      case 10:
        $kind = $phoneA[1] ? "LongX" : "Long";
        $area = substr($phone, 0, 3);
        $exch = substr($phone, 3, 3);
        $uniq = substr($phone, 6, 4);
        $ext  = $phoneA[1];
        break;

      default:
        $kind = "Unknown";
        return $phone;
    }

    switch ($format) {
      case "like":
        $newphone = '%' . $area . '%' . $exch . '%' . $uniq . '%' . $ext . '%';
        $newphone = str_replace('%%', '%', $newphone);
        $newphone = str_replace('%%', '%', $newphone);
        return $newphone;

      case "dash":
        $newphone = $area . "-" . $exch . "-" . $uniq . " x" . $ext;
        $newphone = trim(trim(trim($newphone, "x"), "-"));
        return $newphone;

      case "bare":
        $newphone = $area . $exch . $uniq . "x" . $ext;
        $newphone = trim(trim(trim($newphone, "x"), "-"));
        return $newphone;

      case "area":
        return $area;

      default:
        return $phone;
    }
  }

}
