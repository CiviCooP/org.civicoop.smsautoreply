<?php

class CRM_Smsautoreply_DAO_SmsAutoreply extends CRM_Core_DAO {

  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;

  /**
   * empty definition for virtual function
   */
  static function getTableName() {
    return 'civicrm_sms_autoreply';
  }

  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields() {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ),
        'subject' => array(
          'name' => 'subject',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'keyword' => array(
          'name' => 'keyword',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 160,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'reply' => array(
          'name' => 'reply',
          'type' => CRM_Utils_Type::T_STRING,
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'is_active' => array(
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'required' => false,
          'default' => '1',
        ),
        'provider_id' => array(
          'name' => 'provider_id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
        ),
        'charge' => array(
          'name' => 'charge',
          'type' => CRM_Utils_Type::T_INT,
        ),
        'financial_type_id' => array(
          'name' => 'financial_type_id',
          'type' => CRM_Utils_Type::T_INT,
        ),
      );
    }
    return self::$_fields;
  }

  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys() {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'subject' => 'subject',
        'keyword' => 'keyword',
        'reply' => 'reply',
        'is_active' => 'is_active',
        'provider_id' => 'provider_id',
        'charge' => 'charge',
        'financial_type_id' => 'financial_type_id'
      );
    }
    return self::$_fieldKeys;
  }

}
