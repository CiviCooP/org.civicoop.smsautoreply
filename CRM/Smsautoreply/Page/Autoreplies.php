<?php

require_once 'CRM/Core/Page.php';

class CRM_Smsautoreply_Page_Autoreplies extends CRM_Core_Page_Basic {
  
  /**
   * The action links that we need to display for the browse screen
   *
   * @var array
   * @static
   */
  static $_links = NULL;
  
  function getBAOName() {
    return 'CRM_Smsautoreply_BAO_SmsAutoreply';
  }
  
  /**
   * Get action Links
   *
   * @return array (reference) of action links
   */
  function &links() {
    if (!(self::$_links)) {
      self::$_links = array(
        CRM_Core_Action::UPDATE => array(
          'name' => ts('Edit'),
          'url' => 'civicrm/admin/sms/autoreply',
          'qs' => 'action=update&id=%%id%%&reset=1',
          'title' => ts('Edit Autoreply'),
        ),
        CRM_Core_Action::DELETE => array(
          'name' => ts('Delete'),
          'url' => 'civicrm/admin/sms/autoreply',
          'qs' => 'action=delete&id=%%id%%',
          'title' => ts('Delete Autoreply'),
        ),
        CRM_Core_Action::ENABLE => array(
          'name' => ts('Enable'),
          'extra' => 'onclick = "enableDisable( %%id%%,\'' . 'CRM_Smsautoreply_BAO_SmsAutoreply' . '\',\'' . 'disable-enable' . '\' );"',
          'ref' => 'enable-action',
          'title' => ts('Enable Autoreply'),
        ),
        CRM_Core_Action::DISABLE => array(
          'name' => ts('Disable'),
          'extra' => 'onclick = "enableDisable( %%id%%,\'' . 'CRM_Smsautoreply_BAO_SmsAutoreply' . '\',\'' . 'enable-disable' . '\' );"',
          'ref' => 'disable-action',
          'title' => ts('Disable Autoreply'),
        ),
      );
    }
    return self::$_links;
  }

  /**
   * Run the page.
   *
   * This method is called after the page is created. It checks for the
   * type of action and executes that action.
   * Finally it calls the parent's run method.
   *
   * @return void
   * @access public
   *
   */
  function run() {
    // set title and breadcrumb
    CRM_Utils_System::setTitle(ts('Settings - SMS Autoreplies'));
    $breadCrumb = array(array('title' => ts('SMS Autoreplies'),
        'url' => CRM_Utils_System::url('civicrm/admin/sms/autoreply',
          'reset=1'
        ),
      ));
    CRM_Utils_System::appendBreadCrumb($breadCrumb);

    $this->_id = CRM_Utils_Request::retrieve('id', 'String',
      $this, FALSE, 0
    );
    $this->_action = CRM_Utils_Request::retrieve('action', 'String',
      $this, FALSE, 0
    );

    return parent::run();
  }

  /**
   * Browse all Providers.
   *
   * @return void
   * @access public
   * @static
   */
  function browse($action = NULL) {
    $replies = CRM_Smsautoreply_BAO_SmsAutoreply::getAutoreplies();
    $rows = array();
    foreach ($replies as $reply) {
      $action = array_sum(array_keys($this->links()));
      // update enable/disable links.
      if ($reply['is_active']) {
        $action -= CRM_Core_Action::ENABLE;
      }
      else {
        $action -= CRM_Core_Action::DISABLE;
      }

      $financialTypes = $this->getFinancialTypes();
      $reply['financial_type_id'] = $financialTypes[$reply['financial_type_id']];
      
      $providers = $this->getProviders();
      $reply['provider_id'] = $providers[$reply['provider_id']];

      $reply['action'] = CRM_Core_Action::formLink(self::links(), $action,
        array('id' => $reply['id'])
      );
      $rows[] = $reply;
    }
    $this->assign('rows', $rows);
  }

  /**
   * Get name of edit form
   *
   * @return string Classname of edit form.
   */
  function editForm() {
    return 'CRM_Smsautoreply_Form_Autoreplies';
  }

  /**
   * Get edit form name
   *
   * @return string name of this page.
   */
  function editName() {
    return 'SMS Autoreply';
  }

  /**
   * Get user context.
   *
   * @return string user context.
   */
  function userContext($mode = NULL) {
    return 'civicrm/admin/sms/autoreply';
  }
  
  protected function getFinancialTypes() {
    $financial_types = new CRM_Financial_DAO_FinancialType();
    $financial_types->is_active = 1;
    $financial_types->find(FALSE);
    $return = array();
    $return[] = '';
    while($financial_types->fetch()) {
      $return[$financial_types->id] = $financial_types->name;
    }
    return $return;
  }
  
  protected function getProviders() {
    $providers = CRM_SMS_BAO_Provider::getProviders(NULL, NULL, TRUE, 'id');
    $return = array();
    foreach($providers as $provider) {
      $return[$provider['id']] = $provider['title'];
    }
    return $return;
  }
}
