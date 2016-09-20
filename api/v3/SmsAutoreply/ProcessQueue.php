<?php

/**
 * SmsAutoreply.ProcessQueue API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_sms_autoreply_processqueue_spec(&$spec) {
}

/**
 * SmsAutoreply.ProcessQueue API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sms_autoreply_processqueue($params) {
  $limit = 100;
  if (isset($params['limit']) && is_numeric($params['limit'])) {
    $limit = $params['limit'];
  }
  $reply = CRM_Smsautoreply_Reply::singleton();
  $reply->processQueue($limit);
  return civicrm_api3_create_success();
}

