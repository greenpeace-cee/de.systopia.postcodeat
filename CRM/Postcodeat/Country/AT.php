<?php

class CRM_Postcodeat_Country_AT {

  static function whereClause($params) {
    $plznr = $params['plznr'];
    $ortnam = $params['ortnam'];
    $stroffi = $params['stroffi'];

    $where = array();
    if (!empty($plznr)) {
      $where[] = "plznr LIKE '{$plznr}%'";
    }
    if (!empty($ortnam)) {
      $where[] = "ortnam LIKE '{$ortnam}%'";
    }
    if (!empty($stroffi)) {
      $where[] = "stroffi LIKE '{$stroffi}%'";
    }
    if (count($where) > 1) {
      $where = implode(' AND ', $where);
    }
    else {
      $where = reset($where);
    }
    return $where;
  }

  static function getState($params) {
    $where = self::whereClause($params);

    // Validate parameters
    if (empty($where)) {
      return FALSE;
    }

    $sql = "SELECT DISTINCT gemnr FROM `civicrm_postcodeat` WHERE {$where} LIMIT 0,10";
    $dao = CRM_Core_DAO::executeQuery($sql);

    if (!$dao->fetch()) {
      return FALSE;
    }

    $gemnr = $dao->gemnr;
    switch($gemnr[0]) {
      case 1:
        $values['id'] = 1628;
        //$values['state'] = "Burgenland";
        break;
      case 2:
        $values['id'] = 1629;
        //$values['state'] = "Kärnten";
        break;
      case 3:
        $values['id'] = 1630;
        //$values['state'] = "Niederösterreich";
        break;
      case 4:
        $values['id'] = 1631;
        //$values['state'] = "Oberösterreich";
        break;
      case 5:
        $values['id'] = 1632;
        //$values['state'] = "Salzburg";
        break;
      case 6:
        $values['id'] = 1633;
        //$values['state'] = "Steiermark";
        break;
      case 7:
        $values['id'] = 1634;
        //$values['state'] = "Tirol";
        break;
      case 8:
        $values['id'] = 1635;
        //$values['state'] = "Vorarlberg";
        break;
      case 9:
        $values['id'] = 1636;
        //$values['state'] = "Wien";
        break;
      default:
        $values['id'] = NULL;
    }

    // Get state/province label
    if (isset($values['id'])) {
      $values['state'] = CRM_Core_PseudoConstant::stateProvince($values['id'], FALSE);
    }

    return $values;
  }
}