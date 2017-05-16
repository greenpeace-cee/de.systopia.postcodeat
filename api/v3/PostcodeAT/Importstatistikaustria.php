<?php

/**
 * Statistikaustria.Import API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_postcode_a_t_Importstatistikaustria($params) {
  try {
    set_time_limit(-1);
    $db = new CRM_Postcodeat_ImportStatistikAustria();
    // Put data in temporary table
    $db->importStatistikAustria();
    // Overwrite live table
    $db->copy();
    return civicrm_api3_create_success(1, $params, 'PostcodeAT', 'Importstatistikaustria');
  }
  catch (Exception $e) {
    throw new API_Exception(/*errorMessage*/ 'Import failed: ' . $e->getMessage(), /*errorCode*/ 1);
  }
}
