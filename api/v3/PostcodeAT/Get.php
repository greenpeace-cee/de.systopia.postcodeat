<?php

/**
 * PostcodeNL.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
/*function _civicrm_api3_postcode_n_l_get_spec(&$spec) {
  $spec['magicword']['api.required'] = 1;
}*/

/**
 * PostcodeAT.Get API
 *
 * Returns the found postcode, woonplaats, gemeente with the queried paramaters
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_postcode_a_t_get($params) {
  $validParamFields = array(
    'id',
    'plznr',
    'ortnam',
    'stroffi',
    'return',
  );
  $returnFields = array(
    'id',
    'plznr',
    'ortnam',
    'stroffi',
    'return',
  );

  /* 
   * check if at least one parameter is valid 
   * Also break up an postcode into postcode number (4 digits) and postcode letter (2 letters).
   *
   */
  $validatedParams = array();
  foreach($params as $key => $value) {
    if (in_array($key, $validParamFields)) {
      if ($key == 'plznr') {
        // AT postcode is 4 digits only
        $postcode = preg_replace('/[^\d]/i', '', $value);
        if (strlen($postcode) > 1) {
          $validatedParams['plznr'] = $postcode;
        }
      } elseif (!empty($value)) {
        $validatedParams[$key] = $value;
      }
    }
  }

  /**
   * Build the where clause of the postcode
   */
  $selectFields = '';
  $where = "";
  $values = array();
  $i = 1;
  foreach($validatedParams as $field => $value) {
    if (!empty($selectFields)) { $selectFields .= ','; }
    switch ($field) {
      case 'plznr':
      case 'ortnam':
      case 'stroffi':
        $where .= " AND `" . $field . "` LIKE %" . $i;
        $values[$i] = array($value . '%', 'String');
        break;
    }
    $i++;
  }

  $selectFields = $validatedParams['return'];
  $sql = "SELECT DISTINCT {$selectFields} FROM `civicrm_postcodeat` WHERE 1 {$where} LIMIT 0, 100";
  $dao = CRM_Core_DAO::executeQuery($sql, $values);

  $returnValues = array();
  while($dao->fetch()) {
    $row = array();
    foreach($returnFields as $field) {
      if (isset($dao->$field)) {
        $row[$field] = $dao->$field;
      }
    }
    $returnValues[] = $row;
  }

  CRM_Postcodeat_Utils_Hook::invoke(1,
    $returnValues, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject,
    'civicrm_postcodeat_get'
  );

  return civicrm_api3_create_success($returnValues, $params, 'PostcodeAT', 'get');
}

