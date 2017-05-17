<?php

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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function postcodeat_civicrm_xmlMenu(&$files) {
  _postcodeat_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function postcodeat_civicrm_postInstall() {
  _postcodeat_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function postcodeat_civicrm_uninstall() {
  _postcodeat_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function postcodeat_civicrm_enable() {
  _postcodeat_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function postcodeat_civicrm_disable() {
  _postcodeat_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function postcodeat_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _postcodeat_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function postcodeat_civicrm_managed(&$entities) {
  _postcodeat_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function postcodeat_civicrm_caseTypes(&$caseTypes) {
  _postcodeat_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function postcodeat_civicrm_angularModules(&$angularModules) {
  _postcodeat_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function postcodeat_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _postcodeat_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function postcodeat_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  if ((strtolower($entity) == strtolower('postcode_a_t') || strtolower($entity) == strtolower('PostcodeAT')) && $action == 'get') {
    $params['check_permissions'] = false; //allow everyone to use the postcode api
  }
}

function postcodeat_civicrm_alterContent(  &$content, $context, $tplName, &$object ) {
  if ($object instanceof CRM_Contact_Form_Inline_Address) {
    $locBlockNo = CRM_Utils_Request::retrieve('locno', 'Positive', CRM_Core_DAO::$_nullObject, TRUE, NULL, $_REQUEST);
    $template = CRM_Core_Smarty::singleton();
    $template->assign('blockId', $locBlockNo);
    CRM_Core_Resources::singleton()->addVars('postcodeat', array('blockId' => $locBlockNo));
    $content .= $template->fetch('CRM/Contact/Form/Edit/Address/postcodeat_js.tpl');
    $content .= $template->fetch('CRM/Postcodeat/autocomplete.tpl');
  }
  if ($object instanceof CRM_Contact_Form_Contact) {
    $template = CRM_Core_Smarty::singleton();
    $content .= $template->fetch('CRM/Contact/Form/Edit/postcodeat_contact_js.tpl');
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
function postcodeat_civicrm_preProcess($formName, &$form) {

} // */

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
