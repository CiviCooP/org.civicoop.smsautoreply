<?php

/**
 * Collection of upgrade steps
 */
class CRM_Smsautoreply_Upgrader extends CRM_Smsautoreply_Upgrader_Base {

  public function install() {
    $this->executeSqlFile('sql/install.sql');
  }

  public function uninstall() {
   $this->executeSqlFile('sql/uninstall.sql');
  }
  
  public function upgrade_1001() {
    CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_sms_autoreply MODIFY `reply` text NULL;");
    return true;
  }

  public function upgrade_1002() {
    CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_sms_autoreply ADD `aksjon_id` varchar(255) NOT NULL default '' AFTER `financial_type_id`;");
    CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_sms_autoreply ADD `earmarking` varchar(255) NOT NULL default '' AFTER `financial_type_id`;");
    return true;
  }

  public function upgrade_1003() {
    CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_sms_autoreply MODIFY `aksjon_id` varchar(255) NULL default '';");
    CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_sms_autoreply MODIFY `earmarking` varchar(255) NULL default '';");
    return true;
  }
  
}
