<?php
// phpcs:disable
use CRM_Postcodeat_ExtensionUtil as E;
// phpcs:enable

class CRM_Postcodeat_BAO_PostcodeAT extends CRM_Postcodeat_DAO_PostcodeAT {

  const DOWNLOAD_URL = "https://statistik.at/verzeichnis/strassenliste/gemplzstr.zip";
  const XML_FILE = "gemplzstr.xml";

  /**
   * Copy data from `civicrm_statistikaustria_import` to `civicrm_postcodeat`
   *
   * @param bool $discard_empty
   * @return int
   */
  public static function copy($discard_empty = TRUE) {
    $import_count = (int) CRM_Core_DAO::singleValueQuery("
      SELECT COUNT(*) FROM `civicrm_statistikaustria_import`
    ");

    if ($discard_empty && $import_count < 1) return FALSE;

    CRM_Core_DAO::executeQuery("TRUNCATE `civicrm_postcodeat`");

    CRM_Core_DAO::executeQuery("
      INSERT INTO `civicrm_postcodeat` (
        gemnr, gemnr2, gemnam38, okz, ortnam, skz, stroffi, strkurz, plznr, zustort
      ) SELECT
        gemnr, gemnr2, gemnam38, okz, ortnam, skz, stroffi, strkurz, plznr, zustort
      FROM `civicrm_statistikaustria_import`
      ORDER BY gemnr ASC, okz ASC, skz ASC
    ");

    CRM_Core_DAO::executeQuery("TRUNCATE `civicrm_statistikaustria_import`");

    return TRUE;
  }

  /**
   * Import postcode data
   *
   * @param string $zip_file
   * @param string $xml_file
   * @return int
   */
  public static function importStatistikAustria($zip_file = NULL, $xml_file = NULL) {
    $zip_file = empty($zip_file) ? self::DOWNLOAD_URL : $zip_file;
    $xml_file = empty($xml_file) ? self::XML_FILE : $xml_file;

    // Truncate the import table
    CRM_Core_DAO::executeQuery("TRUNCATE `civicrm_statistikaustria_import`");

    // Parse XML file
    $fp = self::getStreamToXML($zip_file, $xml_file);
    $xml = simplexml_load_string(stream_get_contents($fp));
    fclose($fp);

    $columns = [
      'gemnr',
      'gemnam38',
      'okz',
      'ortnam',
      'skz',
      'stroffi',
      'strkurz',
      'plznr',
      'gemnr2',
      'zustort',
    ];

    foreach($xml->daten->children() as $record) {
      $values = (array) $record->element;

      if (count($values) !== count($columns)) continue;
      // Looks like valid data

      // Format `ortnam` field (add space after "," and ":")
      $ortnam_idx = array_search('ortnam', $columns);
      $values[$ortnam_idx] = str_replace([',', ':'], [', ', ': '], $values[$ortnam_idx]);

      // Escape and quote values for SQL
      $esc_values = array_map(fn ($v) => sprintf("'%s'", CRM_Core_DAO::escapeString($v)), $values);

      CRM_Core_DAO::executeQuery("
        INSERT INTO `civicrm_statistikaustria_import` (" . implode(', ', $columns) . ")
        VALUES (" . implode(', ', $esc_values) . ")
      ");
    }
  }

  /**
   * Returns the filepointer to the first file in the zip archive
   *
   * @param string $zip_file
   * @param string $xml_file
   * @throws CRM_Core_Exception
   * @return bool|int
   */
  private static function getStreamToXML($zip_file, $xml_file) {
    $temp_file = tempnam(sys_get_temp_dir(), 'statistikaustria');

    if (!copy($zip_file, $temp_file)) {
      throw new CRM_Core_Exception("Unable to download zipfile: $zip_file");
    }

    static $zip = new ZipArchive();

    if (!$zip->open($temp_file)) {
      throw new CRM_Core_Exception("Unable to open zipfile: $zip_file");
    }

    // Only read first file in zip
    $fp = $zip->getStream($xml_file);

    if (!$fp) {
      throw new CRM_Core_Exception("Unable to retrieve XML ($xml_file) from zipfile: $zip_file");
    }

    return $fp;
  }

}
