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
 * PostcodeAT.Getatstate API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_postcode_a_t_getatstate_spec(&$spec) {
  $spec['plznr']['api.required'] = 1;
}

/**
 * PostcodeAT.Getstate API
 *
 * Returns the state based on the postcode.
 * (Looks up gemnr from postcode and maps to state)
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_postcode_a_t_getatstate($params) {
  // Validate parameters
  if (empty($params['plznr']) || strlen($params['plznr']) != 4) {
    return civicrm_api3_create_error(ts('Invalid plznr'));
  }
  $plznr = $params['plznr'];

  $sql = "SELECT DISTINCT gemnr FROM `civicrm_postcodeat` WHERE plznr = {$plznr} LIMIT 0, 1";
  $dao = CRM_Core_DAO::executeQuery($sql);

  if (!$dao->fetch()) {
    return civicrm_api3_create_error(ts("Plznr {$plznr} not found."));
  }

  $gemnr = $dao->gemnr;
  switch($gemnr[0]) {
    case 1:
      $values['id'] = 1628;
      $values['state'] = "Burgenland";
      break;
    case 2:
      $values['id'] = 1629;
      $values['state'] = "Kärnten";
      break;
    case 3:
      $values['id'] = 1630;
      $values['state'] = "Niederösterreich";
      break;
    case 4:
      $values['id'] = 1631;
      $values['state'] = "Oberösterreich";
      break;
    case 5:
      $values['id'] = 1632;
      $values['state'] = "Salzburg";
      break;
    case 6:
      $values['id'] = 1633;
      $values['state'] = "Steiermark";
      break;
    case 7:
      $values['id'] = 1634;
      $values['state'] = "Tirol";
      break;
    case 8:
      $values['id'] = 1635;
      $values['state'] = "Vorarlberg";
      break;
    case 9:
      $values['id'] = 1636;
      $values['state'] = "Wien";
      break;
  }

  /* TODO: Set state
cj("#address_1_state_province_id").select2('data', { id:"1634", text: "Tirol"});
*/

  $returnValues = array($values);
  return civicrm_api3_create_success($returnValues, $params, 'PostcodeAT', 'getstate');
}

