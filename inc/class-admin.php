<?php
declare(strict_types=1);

namespace WildWolf\WordPress\CyrToLat;

final class Admin {
	/** @var self|null */
	private static $instance = null;

	const OPTIONS_KEY = Plugin::OPTIONS_KEY;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init(): void {
		load_plugin_textdomain( 'wwc2r', false, plugin_basename( dirname( __DIR__ ) ) . '/lang/' ); // phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_menu(): void {
		add_options_page( __( 'WW CyrToLat', 'wwc2r' ), __( 'WW CyrToLat', 'wwc2r' ), 'manage_options', 'wwc2r', [ $this, 'options_page' ] );
	}

	public function admin_init(): void {
		add_settings_section( 'wwc2r_section_main', '', '__return_null', 'wwc2r' );
		add_settings_field( 'posts', __( 'Transliterate post / page slugs', 'wwc2r' ), [ $this, 'checkbox_field' ], 'wwc2r', 'wwc2r_section_main', [ 'label_for' => 'posts' ] );
		add_settings_field( 'terms', __( 'Transliterate term slugs', 'wwc2r' ), [ $this, 'checkbox_field' ], 'wwc2r', 'wwc2r_section_main', [ 'label_for' => 'terms' ] );
		add_settings_field( 'files', __( 'Transliterate file names', 'wwc2r' ), [ $this, 'checkbox_field' ], 'wwc2r', 'wwc2r_section_main', [ 'label_for' => 'files' ] );
	}

	/**
	 * @param array<string,string> $args
	 */
	public function checkbox_field( array $args ): void {
		/** @var array<string,string> */
		$options = get_option( self::OPTIONS_KEY );
		$name    = esc_attr( self::OPTIONS_KEY );
		$id      = esc_attr( $args['label_for'] );
		$checked = checked( $options[ $id ], 1, false );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo <<< EOT
<input type="checkbox" name="{$name}[{$id}]" id="{$id}" value="1"{$checked}/>
EOT;
	}

	public function options_page(): void {
		require __DIR__ . '/../views/settings.php';
	}
}
