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
    'zustort',
    'return',
  );
  $returnFields = array(
    'id',
    'plznr',
    'ortnam',
    'stroffi',
    'zustort',
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
      } elseif ($key == 'return') {
        $return_columns = explode(',', str_replace(' ', '', $value));

        $validatedParams[$key] = array_filter(
          $return_columns,
          fn ($col) => in_array($col, $returnFields)
        );
      } elseif (!empty($value)) {
        $validatedParams[$key] = $value;
      }
    }
  }

  if (empty($validatedParams['return'])) {
    $validatedParams['return'] = $returnFields;
  }

  // Build the where clause of the postcode
  $conditions = ['1'];
  $values = array();
  $i = 1;
  foreach($validatedParams as $field => $value) {
    switch ($field) {
      case 'plznr':
      case 'ortnam':
      case 'stroffi':
      case 'gemnam38':
      case 'zustort':
        if (!empty($params['strict_fields_searching'])
          && is_array($params['strict_fields_searching'])
          && in_array($field, $params['strict_fields_searching'])) {
          $conditions[] = "`$field` = %$i";
          $values[$i] = array($value, 'String');
        } else {
          $conditions[] = "`$field` LIKE %$i";
          $values[$i] = array($value . '%', 'String');
        }
        break;
    }
    $i++;
  }

  $selectFields = implode(', ', $validatedParams['return']);
  $whereClause = implode(' AND ', $conditions);
  $sql = "SELECT DISTINCT $selectFields FROM `civicrm_postcodeat` WHERE $whereClause";

  // For ortnam (City) we select gemnam38 (Politische Gemeinde) as well
  if ($selectFields == 'ortnam') {
    $sql.= " UNION SELECT gemnam38 FROM `civicrm_postcodeat` WHERE $whereClause";
  }
  $sql .= " LIMIT 0, 100";
  $dao = CRM_Core_DAO::executeQuery($sql, $values);

  $returnValues = array();
  $mode = $params['mode'] ?? NULL;
  while($dao->fetch()) {
    $row = array();
    if ($mode == 0) {
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

/**
 * Adjust Metadata for Payment action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_postcode_a_t_get_spec(&$params) {
  $params['id'] = [
    'name'         => 'id',
    'api.required' => 0,
    'title'        => 'ID of the row',
  ];
  $params['plznr'] = [
    'name'         => 'plznr',
    'api.required' => 0,
    'title'        => 'Postal code(plznr)',
  ];
  $params['gemnam38'] = [
    'name'         => 'gemnam38',
    'api.required' => 0,
    'title'        => 'gemnam38',
  ];
  $params['ortnam'] = [
    'name'         => 'ortnam',
    'api.required' => 0,
    'title'        => 'City(ortnam)',
  ];
  $params['stroffi'] = [
    'name'         => 'stroffi',
    'api.required' => 0,
    'title'        => 'Street address(stroffi)',
  ];
  $params['zustort'] = [
    'name'         => 'zustort',
    'api.required' => 0,
    'title'        => 'zustort',
  ];
  $params['return'] = [
    'name'         => 'return',
    'api.required' => 0,
    'title'        => 'Return field',
    'description'  => 'Available one of the fields: "id", "plznr", "gemnam38", "ortnam", "stroffi", "zustort", "return"',
  ];
  $params['strict_fields_searching'] = [
    'name'         => 'strict_fields_searching',
    'api.required' => 0,
    'title'        => "Strict filed searching",
    'description'  => "Use '=' operator instead of 'LIKE' for fields. Ex: «['plznr', 'ortnam']»"
  ];
}
