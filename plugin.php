<?php
/*
Plugin Name: WW CyrToLat
Description: Transliterates cyrillic symbols in post names to latine ones.
Author: Volodymyr Kolesnykov <volodymyr@wildwolf.name>
Version: 0.1
*/

use WildWold\WordPress\CyrToLat;

defined('ABSPATH') or die();

require_once __DIR__ . '/cyrtolat.php';
CyrToLat::instance();
