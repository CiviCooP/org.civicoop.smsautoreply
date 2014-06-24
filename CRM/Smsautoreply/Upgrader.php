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
  
}
