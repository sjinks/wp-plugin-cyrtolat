<?php
/*
Plugin Name: WW CyrToLat
Description: Transliterates cyrillic characters in post names to latin ones.
Author: Volodymyr Kolesnykov <volodymyr@wildwolf.name>
Version: 3.0.1
*/

use WildWolf\WordPress\CyrToLat\Admin;
use WildWolf\WordPress\CyrToLat\Plugin;

// @codeCoverageIgnoreStart
if ( defined( 'ABSPATH' ) ) {
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	} elseif ( file_exists( ABSPATH . 'vendor/autoload.php' ) ) {
		require_once ABSPATH . 'vendor/autoload.php';
	}

	Plugin::instance();
	if ( is_admin() ) {
		Admin::instance();
	}
}
// @codeCoverageIgnoreEnd
