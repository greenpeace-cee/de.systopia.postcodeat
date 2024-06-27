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
 * PostcodeAT.Importstatistikaustria API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_postcode_a_t_Importstatistikaustria_spec(&$spec) {
  $spec['error_on_empty'] = [
    'title'       => 'Error on empty import result?',
    'description' => 'Indicates whether an empty new data set should raise an error and not overwrite the table data.',
    'type'        => CRM_Utils_Type::T_BOOLEAN,
    'api.default' => TRUE,
  ];
  $spec['zipfile'] = [
    'title' => "Path to zip file if available locally",
    'type'  => CRM_Utils_Type::T_STRING,
  ];
}

/**
 * PostcodeAT.Importstatistikaustria API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_postcode_a_t_Importstatistikaustria($params) {
  try {
    set_time_limit(0);

    // Put data in temporary table
    CRM_Postcodeat_BAO_PostcodeAT::importStatistikAustria($params['zipfile'] ?? NULL);

    // Overwrite live table
    if (!CRM_Postcodeat_BAO_PostcodeAT::copy($params['error_on_empty'])) {
      return civicrm_api3_create_error(
        'Received empty data set. Discarding results.',
        [ 'error_code' => 'empty_data_set' ]
      );
    }

    // Count total number imported
    $result = [
      'count' => (int) CRM_Core_DAO::singleValueQuery("SELECT COUNT(*) FROM `civicrm_postcodeat`"),
    ];

    return civicrm_api3_create_success($result, $params, 'PostcodeAT', 'Importstatistikaustria');
  } catch (Exception $e) {
    throw new API_Exception('Import failed: ' . $e->getMessage(), 1);
  }
}
