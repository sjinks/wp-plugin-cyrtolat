<?php
/*
Plugin Name: WW CyrToLat
Description: Transliterates cyrillic characters in post names to latin ones.
Author: Volodymyr Kolesnykov <volodymyr@wildwolf.name>
Version: 3.0.0
*/

use WildWolf\WordPress\CyrToLat\Admin;
use WildWolf\WordPress\CyrToLat\Plugin;

if ( defined( 'ABSPATH' ) ) {
	require_once __DIR__ . '/inc/class-plugin.php';
	Plugin::instance();

	if ( is_admin() ) {
		require_once __DIR__ . '/inc/class-admin.php';
		Admin::instance();
	}
}
