<?php
/*-------------------------------------------------------+
| SYSTOPIA - Postcode Lookup for Austria                 |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: M. Wire (mjw@mjwconsult.co.uk)                 |
|         B. Endres (endres@systopia.de)                 |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

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
    'gemnam38',
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

  // Validate parameters
  $validatedParams = array();
  foreach($params as $key => $value) {
    if (in_array($key, $validParamFields)) {
      if ($key == 'plznr') {
        // AT postcode is 4 digits only
        $postcode = preg_replace('/[^\d]/i', '', $value);
        if (strlen($postcode) > 0) {
          $validatedParams['plznr'] = $postcode;
        }
      } elseif (!empty($value)) {
        $validatedParams[$key] = $value;
      }
    }
  }

  // Build the where clause of the postcode
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
      case 'gemnam38':
        $where .= " AND `" . $field . "` LIKE %" . $i;
        $values[$i] = array($value . '%', 'String');
        break;
    }
    $i++;
  }

  $selectFields = $validatedParams['return'];
  $sql = "SELECT DISTINCT {$selectFields} FROM `civicrm_postcodeat` WHERE 1 {$where}";
  // For ortnam (City) we select gemnam38 (Politische Gemeinde) as well
  if ($selectFields == 'ortnam') {
    $sql.= " UNION SELECT gemnam38 FROM `civicrm_postcodeat` WHERE 1 {$where}";
  }
  $sql .= " LIMIT 0, 100";
  $dao = CRM_Core_DAO::executeQuery($sql, $values);

  $returnValues = array();
  while($dao->fetch()) {
    $row = array();
    if ($params['mode'] == 0) {
      // Order as array 0 => plznr, ortnam, stroffi etc.
      foreach ($returnFields as $field) {
        if (isset($dao->$field)) {
          $row[$field] = $dao->$field;
        }
      }
      $returnValues[] = $row;
    }
    else {
      // Order as array plznr => 1020,1030; ortnam => Wien, Salzburg..; stroffi => ...,...
      foreach ($returnFields as $field) {
        if (isset($dao->$field)) {
          $returnValues[$field][$dao->$field]=$dao->$field;
        }
      }
    }

  }

  CRM_Postcodeat_Utils_Hook::invoke(1,
    $returnValues, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject,
    'civicrm_postcodeat_get'
  );

  return civicrm_api3_create_success($returnValues, $params, 'PostcodeAT', 'get');
}

