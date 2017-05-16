<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Postcodeat_Upgrader extends CRM_Postcodeat_Upgrader_Base {

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

}
