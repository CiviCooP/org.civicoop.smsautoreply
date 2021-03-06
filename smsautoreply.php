<?php

require_once 'smsautoreply.civix.php';

/**
 * Implementation of hook_civicrm_navigationMenu
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function smsautoreply_civicrm_navigationMenu( &$params ) {  
  $item = array (
    "name"=> ts('SMS Autoreplies'),
    "url"=> "civicrm/admin/sms/autoreply",
    "permission" => "administer CiviCRM",
  );
  _smsautoreply_civix_insert_navigation_menu($params, "Administer/Communications", $item);
}

/**
 * Implementation of hook_civicrm_post
 * 
 * Check if is in incoming SMS 
 * 
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_post
 * @param type $op
 * @param type $objectName
 * @param type $objectId
 * @param type $objectRef
 */
function smsautoreply_civicrm_post($op, $objectName, $objectId, &$objectRef ) {
  //delegate the checking of an incoming sms
  $autoreply = CRM_Smsautoreply_Reply::singleton();
  $autoreply->post($op, $objectName, $objectId, $objectRef);
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function smsautoreply_civicrm_config(&$config) {
  _smsautoreply_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function smsautoreply_civicrm_xmlMenu(&$files) {
  _smsautoreply_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function smsautoreply_civicrm_install() {
  return _smsautoreply_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function smsautoreply_civicrm_uninstall() {
  return _smsautoreply_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function smsautoreply_civicrm_enable() {
  return _smsautoreply_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function smsautoreply_civicrm_disable() {
  return _smsautoreply_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function smsautoreply_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _smsautoreply_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function smsautoreply_civicrm_managed(&$entities) {
  return _smsautoreply_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function smsautoreply_civicrm_caseTypes(&$caseTypes) {
  _smsautoreply_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function smsautoreply_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _smsautoreply_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
