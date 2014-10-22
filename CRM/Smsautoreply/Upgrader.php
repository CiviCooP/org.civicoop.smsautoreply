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
  
}
