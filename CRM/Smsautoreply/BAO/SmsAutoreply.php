<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id: $
 *
 */
class CRM_Smsautoreply_BAO_SmsAutoreply extends CRM_Smsautoreply_DAO_SmsAutoreply {

  function __construct() {
    parent::__construct();
  }

  /*
   * Retrieves the list of autoreplies from the database
   * 
   * @access public
   * $selectArr array of coloumns to fetch
   * $getActive boolean to get active providers
   */
  static function getAutoreplies($selectArr = NULL, $filter = NULL, $getActive = TRUE, $orderBy = 'id') {
    $replies = array();
    $temp      = array();
    $dao       = new CRM_Smsautoreply_DAO_SmsAutoreply();
    if ($filter && !array_key_exists('is_active', $filter) && $getActive) {
      $dao->is_active = 1;
    }
    if ($filter && is_array($filter)) {
      foreach ($filter as $key => $value) {
        $dao->$key = $value;
      }
    }
    if ($selectArr && is_array($selectArr)) {
      $select = implode(',', $selectArr);
      $dao->selectAdd($select);
    }
    $dao->orderBy($orderBy);
    $dao->find();
    while ($dao->fetch()) {
      CRM_Core_DAO::storeValues($dao, $temp);
      $replies[] = $temp;
    }
    return $replies;
  }

  static function saveRecord($values) {
    $dao = new CRM_Smsautoreply_BAO_SmsAutoreply();
    $dao->copyValues($values);
    $dao->save();
  }

  static function updateRecord($values, $id) {
    $dao = new CRM_Smsautoreply_DAO_SmsAutoreply();
    $dao->id = $id;
    if ($dao->find(TRUE)) {
      $dao->copyValues($values);
      $dao->save();
    }
  }

  static function setIsActive($id, $is_active) {
    return CRM_Core_DAO::setFieldValue('CRM_Smsautoreply_DAO_SmsAutoreply', $id, 'is_active', $is_active);
  }

  static function del($id) {
    if (!$id) {
      CRM_Core_Error::fatal(ts('Invalid value passed to delete function'));
    }

    $dao = new CRM_Smsautoreply_DAO_SmsAutoreply();
    $dao->id = $id;
    if (!$dao->find(TRUE)) {
      return NULL;
    }
    $dao->delete();
  }
  
}


