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

/* 
 * Autocomplete for postcode at
 * 
 */

class CRM_Postcodeat_Page_AJAX {
  
  function autocomplete() {
    $mode = CRM_Utils_Request::retrieve('mode', 'Integer', CRM_Core_DAO::$_nullObject, FALSE, 0);
    $plznr = CRM_Utils_Request::retrieve('plznr', 'String', CRM_Core_DAO::$_nullObject, FALSE, '');
    $ortnam = CRM_Utils_Request::retrieve('ortnam', 'String', CRM_Core_DAO::$_nullObject, FALSE, '');
    $stroffi = CRM_Utils_Request::retrieve('stroffi', 'String', CRM_Core_DAO::$_nullObject, FALSE, '');
    $return = CRM_Utils_Request::retrieve('return', 'String', CRM_Core_DAO::$_nullObject, FALSE, 'ortnam');

    $params = array(
      'sequential' => 0,
      'plznr' => $plznr,
      'ortnam' => $ortnam,
      'stroffi' => $stroffi,
      'mode' => $mode,
    );
    if ($mode == 0) {
      $params['return'] = $return;
    }
    elseif ($mode == 1) {
      $params['return'] = 'plznr,ortnam,stroffi';
      // Don't try and search if we have no address details
      if (empty($plznr) && empty($ortnam) && empty($stroffi)) {
        CRM_Utils_System::civiExit();
        return;
      }
    }

    try {
      $result = civicrm_api3('PostcodeAT', 'get', $params);
    }
    catch (Exception $e) {
      CRM_Utils_System::civiExit();
      return;
    }

    if (empty($result['is_error'])) {
      if ($mode == 0) {
        foreach ($result['values'] as $value) {
          foreach ($value as $key => $entry) {
            $autocomplete[] = array('value' => $entry);
          }
        }
      }
      else {
        if (count($result['values']['plznr']) == 1) {
          $plznr = reset($result['values']['plznr']);
        }
        if (count($result['values']['ortnam']) == 1) {
          $ortnam = reset($result['values']['ortnam']);
        }
        if (count($result['values']['stroffi']) == 1) {
          $stroffi = reset($result['values']['stroffi']);
        }
        $autocomplete[] = array(
          'plznr' => $plznr,
          'ortnam' => $ortnam,
          'stroffi' => $stroffi,
        );
      }
      echo json_encode($autocomplete);
    }
    CRM_Utils_System::civiExit();
  }
}

