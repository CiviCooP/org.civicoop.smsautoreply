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

/**
 *
 */
class CRM_Smsautoreply_Form_Autoreplies extends CRM_Core_Form {

  protected $_id = NULL;

  function preProcess() {

    $this->_id = $this->get('id');

    CRM_Utils_System::setTitle(ts('Manage - SMS Autoreply'));

    if ($this->_id) {
      $refreshURL = CRM_Utils_System::url('civicrm/admin/sms/autoreply', "reset=1&action=update&id={$this->_id}", FALSE, NULL, FALSE
      );
    } else {
      $refreshURL = CRM_Utils_System::url('civicrm/admin/sms/autoreply', "reset=1&action=add", FALSE, NULL, FALSE
      );
    }

    $this->assign('refreshURL', $refreshURL);
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    if ($this->_action & CRM_Core_Action::DELETE) {
      $this->addButtons(array(
        array(
          'type' => 'next',
          'name' => ts('Delete'),
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'cancel',
          'name' => ts('Cancel'),
        ),
          )
      );
      return;
    } else {
      $this->addButtons(array(
        array(
          'type' => 'next',
          'name' => ts('Save'),
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'cancel',
          'name' => ts('Cancel'),
        ),
          )
      );
    }

    $attributes = CRM_Core_DAO::getAttribute('CRM_Smsautoreply_DAO_SmsAutoreply');

    $providers = $this->getProviders();
    $financialTypes = $this->getFinancialTypes();

    $this->add('text', 'subject', ts('Subject'), $attributes['subject'], TRUE
    );

    $this->add('text', 'keyword', ts('Keyword'), $attributes['keyword'], TRUE
    );

    $this->add('textarea', 'text_message', ts('Plain-text format'),
      array(
        'cols' => '80', 'rows' => '8',
        'onkeyup' => "return verify(this)",
      ), 
     TRUE
    );
    
    $this->add('select', 'provider_id', ts('Provider'), $providers, TRUE);

    $this->add('checkbox', 'is_active', ts('Is this autoreply active?'));
    
    $this->add('select', 'weight', ts('Weight'), $this->getWeights(), TRUE);
    
    $this->add('text', 'charge', ts('Charge receiver'), $attributes['charge'], FALSE);
    
    $this->add('select', 'financial_type_id', ts('Financial Type'), $financialTypes, FALSE);

    $this->add('text', 'aksjon_id', ts('Aksjon ID'), $attributes['aksjon_id'], FALSE);

    $this->add('text', 'earmarking', ts('Earmarking'), $attributes['earmarking'], FALSE);
    
    $tokens = CRM_Core_SelectValues::contactTokens();
    
    //sorted in ascending order tokens by ignoring word case
    natcasesort($tokens);
    $this->assign('tokens', json_encode($tokens));
    $this->add('select', 'token1', ts('Insert Tokens'),
      $tokens, FALSE,
      array(
        'size' => "5",
        'multiple' => TRUE,
        'onclick' => "return tokenReplText(this);",
      )
    );

    $this->assign('max_sms_length', CRM_SMS_Provider::MAX_SMS_CHAR);
  }

  function setDefaultValues() {
    $defaults = array();

    if (!$this->_id) {
      $defaults['is_active'] = 1;
      $defaults['weight'] = 0;
      return $defaults;
    }

    $dao = new CRM_Smsautoreply_DAO_SmsAutoreply();
    $dao->id = $this->_id;
    
    if (!$dao->find(TRUE)) {
      return $defaults;
    }

    CRM_Core_DAO::storeValues($dao, $defaults);
    $defaults['text_message'] = $dao->reply;
    return $defaults;
  }

  /**
   * Function to process the form
   *
   * @access public
   *
   * @return None
   */
  public function postProcess() {
    if ($this->_action & CRM_Core_Action::DELETE) {
      CRM_Smsautoreply_BAO_SmsAutoreply::del($this->_id);
      CRM_Core_Session::setStatus(ts('Selected Autoreply has been deleted.'), ts('Deleted'), 'success');
      return;
    }

    $recData = $values = $this->controller->exportValues($this->_name);
    $recData['is_active'] = CRM_Utils_Array::value('is_active', $recData, 0);
    $recData['reply'] = $recData['text_message'];
    unset($recData['text_message']);

    if (empty($recData['aksjon_id'])) {
      $recData['aksjon_id'] = '';
    }
    if (empty($recData['earmarking'])) {
      $recData['earmarking'] = '';
    }
    
    if ($this->_action & CRM_Core_Action::UPDATE) {
      CRM_Smsautoreply_BAO_SmsAutoreply::updateRecord($recData, $this->_id);
    } elseif ($this->_action & CRM_Core_Action::ADD) {
      CRM_Smsautoreply_BAO_SmsAutoreply::saveRecord($recData);
    }
  }
  
  protected function getProviders() {
    $providers = CRM_SMS_BAO_Provider::getProviders(NULL, NULL, TRUE, 'id');
    $return[] = ' -- '.ts('Select a provider').' -- ';
    foreach($providers as $provider) {
      $return[$provider['id']] = $provider['title'];
    }
    return $return;
  }
  
  protected function getFinancialTypes() {
    $financial_types = new CRM_Financial_DAO_FinancialType();
    $financial_types->is_active = 1;
    $financial_types->find(FALSE);
    $return[] = ' -- '.ts('Select a financial type').' -- ';
    while($financial_types->fetch()) {
      $return[$financial_types->id] = $financial_types->name;
    }
    return $return;
  }
  
  protected function getWeights() {
    $return = array();
    for($i=-150; $i < 150; $i++) {
      $return[$i] = $i;
    }
    return $return;
  }

}
