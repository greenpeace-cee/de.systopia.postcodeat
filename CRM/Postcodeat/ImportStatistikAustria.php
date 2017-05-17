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
 * Importer class to import the Statistik Austria data
 * 
 */

class CRM_Postcodeat_ImportStatistikAustria {

  private $downloadUrl;
  private $xmlFile;

  public function __construct($downloadUrl = "http://www.statistik.at/verzeichnis/strassenliste/gemplzstr.zip", $xmlFile = 'gemplzstr.xml') {
    $this->downloadUrl = $downloadUrl;
    $this->xmlFile = $xmlFile;
  }

  /**
   * Imports the postcode data
   *
   * @return int
   */
  public function importStatistikAustria() {
    $fp = $this->getStreamToXML();

    //truncate the import table
    CRM_Core_DAO::executeQuery("TRUNCATE `civicrm_statistikaustria_import`;");

    //read csv file line for line
    $sql = "INSERT INTO `civicrm_statistikaustria_import` "
      . " (`gemnr`, `gemnam38`, `okz`, `ortnam`, `skz`, `stroffi`, `plznr`, `gemnr2`)"
      . " VALUES ";

    // Results are XML so turn this into a PHP Array

    $xml = simplexml_load_string(stream_get_contents($fp));
    $this->closeFP($fp);

    foreach($xml->daten->children() as $key => $element) {
      $elements = (array) $element->element;
      if (count($elements) == 8) {
        // Looks like valid data
        //escape data for database
        foreach ($elements as $n => $val) {
          $elements[$n] = CRM_Core_DAO::escapeString($val);
        }

        $values = " ('" . $elements[0] . "', '" . $elements[1] . "', '" . $elements[2] . "', '" . $elements[3] . "', '" . $elements[4] .
          "', '" . $elements[5] . "', '" . $elements[6] . "', '" . $elements[7] . "')";
        CRM_Core_DAO::executeQuery($sql . $values);
      }
    }
  }

  public function copy() {
    CRM_Core_DAO::executeQuery("TRUNCATE `civicrm_postcodeat`;");
    CRM_Core_DAO::executeQuery("INSERT INTO `civicrm_postcodeat` SELECT * FROM `civicrm_statistikaustria_import`");
    CRM_Core_DAO::executeQuery("TRUNCATE `civicrm_statistikaustria_import`;");
  }

  /**
   * Returns the filepointer to the first file in the zip archive
   *
   * @param String $zipfile
   * @return filepointer
   * @throws CRM_Core_Exception
   */
  protected function getStreamToXML() {

    $temp_file = tempnam(sys_get_temp_dir(), 'statistikaustria');
    $zipfile = $this->downloadUrl;

    if (!copy($zipfile, $temp_file)) {
      throw new CRM_Core_Exception("Unable to download zipfile: " . $zipfile);
    }

    $zip = new ZipArchive();
    if (!$zip->open($temp_file)) {
      throw new CRM_Core_Exception("Unable to open zipfile: " . $zipfile);
    }
    //only read first file in zip
    $fp = $zip->getStream($this->xmlFile);
    if (!$fp) {
      throw new CRM_Core_Exception("Unable to retrieve XML (". $this->xmlFile .") from zipfile: " . $zipfile);
    }

    /*if ($convertToUtf8) {
      $this->fopen_utf8($fp);
    }*/

    return $fp;
  }

  protected function closeFP($fp) {
    fclose($fp);
  }

  protected function fopen_utf8($handle) {
    $encoding = '';
    $bom = fread($handle, 2);
    rewind($handle);

    if ($bom === chr(0xff) . chr(0xfe)) {
      // UTF16 Byte Order Mark present
      $encoding = 'UTF-16LE';
    }
    elseif( $bom === chr(0xfe) . chr(0xff)) {
      // UTF16 Byte Order Mark present
      $encoding = 'UTF-16';
    } else {
      $file_sample = fread($handle, 1000) + 'e'; //read first 1000 bytes
      // + e is a workaround for mb_string bug
      rewind($handle);

      $encoding = mb_detect_encoding($file_sample, 'UTF-8, UTF-7, ASCII, EUC-JP,SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP');
    }
    if ($encoding) {
      stream_filter_append($handle, 'convert.iconv.' . $encoding . '/UTF-8');
    }
    return ($handle);
  }

}
