<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Postcodeat_Upgrader extends CRM_Extension_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed.
   */
  public function install() {
    $this->executeSqlFile('sql/install.sql');
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled.
   */
  public function uninstall() {
   $this->executeSqlFile('sql/uninstall.sql');
  }

  /**
   * Add column zustort to civicrm_postcodeat and civicrm_statistikaustria_import
   */
  public function upgrade_1301() {
    $column_exists = CRM_Core_DAO::singleValueQuery("SHOW COLUMNS FROM `civicrm_postcodeat` LIKE 'zustort';");
    if (!$column_exists) {
      $this->ctx->log->info("Adding column `zustort` varchar(75) NULL to table `civicrm_postcodeat`");
      CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_postcodeat` ADD `zustort` varchar(75) NULL");
    }

    $column_exists = CRM_Core_DAO::singleValueQuery("SHOW COLUMNS FROM `civicrm_statistikaustria_import` LIKE 'zustort';");
    if (!$column_exists) {
      $this->ctx->log->info("Adding column `zustort` varchar(75) NULL to table `civicrm_statistikaustria_import`");
      CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_statistikaustria_import` ADD `zustort` varchar(75) NULL");
    }

    $logging = new CRM_Logging_Schema();
    $logging->fixSchemaDifferences();
    return TRUE;
  }

  /**
   * Add column strkurz to civicrm_postcodeat and civicrm_statistikaustria_import
   */
  public function upgrade_1302() {
    $column_exists = CRM_Core_DAO::singleValueQuery("SHOW COLUMNS FROM `civicrm_postcodeat` LIKE 'strkurz';");

    if (!$column_exists) {
      $this->ctx->log->info("Adding column `strkurz` varchar(50) NULL to table `civicrm_postcodeat`");
      CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_postcodeat` ADD `strkurz` varchar(50) NULL");
    }

    $column_exists = CRM_Core_DAO::singleValueQuery("SHOW COLUMNS FROM `civicrm_statistikaustria_import` LIKE 'strkurz';");

    if (!$column_exists) {
      $this->ctx->log->info("Adding column `strkurz` varchar(50) NULL to table `civicrm_statistikaustria_import`");
      CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_statistikaustria_import` ADD `strkurz` varchar(50) NULL");
    }

    $logging = new CRM_Logging_Schema();
    $logging->fixSchemaDifferences();

    return TRUE;
  }

}
