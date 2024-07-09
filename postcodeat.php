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

require_once 'postcodeat.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function postcodeat_civicrm_config(&$config) {
  _postcodeat_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function postcodeat_civicrm_install() {
  _postcodeat_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function postcodeat_civicrm_enable() {
  _postcodeat_civix_civicrm_enable();
}

function postcodeat_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  if (strtolower($entity) == strtolower('postcode_a_t') || strtolower($entity) == strtolower('PostcodeAT')) {
    switch ($action) {
      case 'get':
      case 'getatstate':
      case 'getstate':
        $params['check_permissions'] = FALSE; //allow everyone to use the postcode api
        break;
    }
  }
}

function postcodeat_civicrm_alterContent(  &$content, $context, $tplName, &$object ) {
  if ($object instanceof CRM_Contact_Form_Inline_Address) {
    $locBlockNo = CRM_Utils_Request::retrieve('locno', 'Positive', CRM_Core_DAO::$_nullObject, TRUE, NULL, $_REQUEST);
    $template = CRM_Core_Smarty::singleton();
    $template->assign('blockId', $locBlockNo);
    $content .= $template->fetch('CRM/Contact/Form/Edit/Address/postcodeat_js.tpl');
    $content .= $template->fetch('CRM/Postcodeat/autocomplete.tpl');
  }
  if ($object instanceof CRM_Contact_Form_Contact) {
    $template = CRM_Core_Smarty::singleton();
    $content .= $template->fetch('CRM/Postcodeat/autocomplete.tpl');
  }
}

function postcodeat_civicrm_buildForm( $formName, &$form ) {
  if ($formName == 'CRM_Contact_Form_Contact') {
    CRM_Core_Resources::singleton()
      ->addScriptFile('de.systopia.postcodeat', 'js/postcodeat.js');
  }
}

function postcodeat_civicrm_pageRun( &$page ) {
  if ($page instanceof CRM_Contact_Page_View_Summary) {
    CRM_Core_Resources::singleton()->addScriptFile('de.systopia.postcodeat', 'js/postcodeat.js');
  }
}
// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function postcodeat_civicrm_navigationMenu(&$menu) {
  _postcodeat_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'de.systopia.postcodeat')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _postcodeat_civix_navigationMenu($menu);
} // */
