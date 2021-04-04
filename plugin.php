<?php
/*
Plugin Name: WW CyrToLat
Description: Transliterates cyrillic characters in post names to latin ones.
Author: Volodymyr Kolesnykov <volodymyr@wildwolf.name>
Version: 2.0.1
*/

use WildWolf\WordPress\CyrToLat;
use WildWolf\WordPress\CyrToLatAdmin;

/** @phpstan-ignore-next-line */
defined('ABSPATH') || die();

require_once __DIR__ . '/inc/cyrtolat.php';
CyrToLat::instance();

if (is_admin()) {
    require_once __DIR__ . '/inc/admin.php';
    CyrToLatAdmin::instance();
}
