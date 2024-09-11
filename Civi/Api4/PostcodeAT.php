<?php
namespace Civi\Api4;

/**
 * PostcodeAT entity.
 *
 * Provided by the Austrian Postcode extension.
 *
 * @package Civi\Api4
 */
class PostcodeAT extends Generic\DAOEntity {

  public static function permissions() {
    return [
      'meta' => ['access CiviCRM'],
      'get' => ['access CiviCRM'],
      'default' => ['administer CiviCRM'],
    ];
  }

}
