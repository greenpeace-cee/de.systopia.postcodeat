<?php
/* TODO: Set state
cj("#address_1_state_province_id").select2('data', { id:"1634", text: "Tirol"});
gemnr : id : state
1xxxx : 1628 : Burgenland
2xxxx : 1629 : Kärnten
3xxxx : 1630 : Niederösterreich
4xxxx : 1631 : Oberösterreich
5xxxx : 1632 : Salzburg
6xxxx : 1633 : Steiermark
7xxxx : 1634 : Tirol
8xxxx : 1635 : Vorarlberg
9xxxx : 1636 : Wien
*/


/**
 * PostcodeNL.Get API specification (optional)
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
    return civicrm_api3_create_error('Invalid plznr');
  }
  $plznr = $params['plznr'];

  $sql = "SELECT DISTINCT gemnr FROM `civicrm_postcodeat` WHERE plznr = {$plznr} LIMIT 0, 1";
  $dao = CRM_Core_DAO::executeQuery($sql);

  if (!$dao->fetch()) {
    return civicrm_api3_create_error("Plznr {$plznr} not found in database.");
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

