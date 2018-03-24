<?php
/*
Plugin Name: WW CyrToLat
Description: Transliterates cyrillic symbols in post names to latin ones.
Author: Volodymyr Kolesnykov <volodymyr@wildwolf.name>
Version: 0.3
*/

use WildWold\WordPress\CyrToLat;
use WildWold\WordPress\CyrToLatAdmin;

defined('ABSPATH') || die();

require_once __DIR__ . '/inc/cyrtolat.php';
CyrToLat::instance();

if (is_admin()) {
    require_once __DIR__ . '/inc/admin.php';
    CyrToLatAdmin::instance();
}
