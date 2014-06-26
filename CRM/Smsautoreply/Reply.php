<?php

class CRM_Smsautoreply_Reply {

  protected static $instance;
  protected $validSmsActivities = array();
  protected $incomingSmsSubjects = array();

  protected function __construct() {
    $activityTypeID = CRM_Core_OptionGroup::getValue('activity_type', 'SMS', 'name');
    $this->validSmsActivities[] = $activityTypeID;

    $this->incomingSmsSubjects[] = 'SMS Received';
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
    if ($op == 'create' && $objectName == 'Activity') {
      //check if subject is valid
      if ($this->isValidActivity($objectRef)) {
        //ok this is an incoming sms
        $target_contact_ids = CRM_Activity_BAO_ActivityTarget::retrieveTargetIdsByActivityId($objectRef->id);
        if (count($target_contact_ids)) {
          $this->process($objectRef->details, $objectRef->phone_number, $target_contact_ids, $objectRef->source_contact_id);
        }        
      }
    }
  }
  
  protected function isValidActivity($activity) {
    if (!in_array($activity->activity_type_id, $this->validSmsActivities)) {
      return false;
    }
    
    foreach($this->incomingSmsSubjects as $subject) {
      if ($subject == $activity->subject) {
        return true;
      }
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
    $sql = 'SELECT * FROM `civicrm_sms_autoreply` WHERE %1 LIKE CONCAT(`keyword`, "%") AND `is_active` = "1"';
    $replies = CRM_Core_DAO::executeQuery($sql, array(
          1 => array($message, 'String'),
    ), TRUE, 'CRM_Smsautoreply_DAO_SmsAutoreply');

    while ($replies->fetch()) {
      $this->reply($replies, $from_phone, $from_contact_ids, $to_contact_id);
    }
  }

  /**
   * Send a reply
   * 
   * @param type $body
   * @param type $to_phone
   * @param type $to_contact_id
   * @param type $provider_id
   * @param type $from_contact_id
   * @param type $charge
   * @param type $financial_type_id
   */
  protected function reply($reply, $to_phone, $to_contact_ids, $from_contact_id) { //, $provider_id, $from_contact_id, $charge, $financial_type_id, $subject) {
    CRM_Core_Error::debug_log_message('Send reply '.$reply->subject.' to '.$to_phone .' with body '.$reply->reply);
    $contactDetails = $this->getContactDetails($to_contact_ids, $to_phone);
    $activityParams['text_message'] = $reply->reply;
    $activityParams['activity_subject'] = $reply->subject;
    $smsParams['provider_id'] = $reply->provider_id;
    if ($reply->charge) {
      $smsParams['charge'] = $reply->charge;
    }
    if ($reply->financial_type_id) {
      $smsParams['financial_type_id'] = $reply->financial_type_id;
    }

    $return = CRM_Activity_BAO_Activity::sendSMS($contactDetails, $activityParams, $smsParams, $to_contact_ids, $from_contact_id);
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
