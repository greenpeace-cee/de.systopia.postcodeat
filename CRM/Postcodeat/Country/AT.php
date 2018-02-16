<?php

class CRM_Postcodeat_Country_AT {

  static function whereClause($params) {
    $plznr = $params['plznr'];
    $ortnam = $params['ortnam'];
    $stroffi = $params['stroffi'];

    $where = array();
    if (!empty($plznr)) {
      // only take the first 3 signs of the PLZ, since in some Cities there are
      // private PLZs, indicated by an alternating last digit
      $sub_plz = substr($plznr, 0, 3);
      $where[] = "plznr LIKE '{$sub_plz}_'";
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
    $sql = "SELECT DISTINCT LEFT(gemnr,1) AS gemnr,plznr,ortnam FROM `civicrm_postcodeat` c WHERE {$where} LIMIT 0,10";
    $dao = CRM_Core_DAO::executeQuery($sql);

    if (!$dao->fetch()) {
      return FALSE;
    }

    $gemnr = $dao->gemnr;
    switch($gemnr) {
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