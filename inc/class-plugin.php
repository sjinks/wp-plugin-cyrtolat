<?php
declare(strict_types=1);

namespace WildWolf\WordPress\CyrToLat;

use WildWolf\Utils\Singleton;

final class Plugin {
	use Singleton;

	const OPTIONS_KEY = 'wwc2r';

	/** @var array<string,string> */
	private static $retable = [
		'/зг/u'   => 'zgh',
		'/Зг/u'   => 'Zgh',
		'/\\bє/u' => 'ye',
		'/\\Bє/u' => 'ie',
		'/\\bї/u' => 'yi',
		'/\\Bї/u' => 'i',
		'/\\bй/u' => 'y',
		'/\\Bй/u' => 'i',
		'/\\bю/u' => 'yu',
		'/\\Bю/u' => 'iu',
		'/\\bя/u' => 'ya',
		'/\\Bя/u' => 'ia',
	];

	/** @var array<string,string> */
	private static $table = [
		'А' => 'A',
		'Б' => 'B',
		'В' => 'V',
		'Г' => 'H',
		'Ѓ' => 'G',
		'Ґ' => 'G',
		'Д' => 'D',
		'Е' => 'E',
		'Ё' => 'YO',
		'Є' => 'YE',
		'Ж' => 'ZH',
		'З' => 'Z',
		'Ѕ' => 'Z',
		'И' => 'I',
		'Й' => 'J',
		'Ј' => 'J',
		'І' => 'I',
		'Ї' => 'YI',
		'К' => 'K',
		'Ќ' => 'K',
		'Л' => 'L',
		'Љ' => 'L',
		'М' => 'M',
		'Н' => 'N',
		'Њ' => 'N',
		'О' => 'O',
		'П' => 'P',
		'Р' => 'R',
		'С' => 'S',
		'Т' => 'T',
		'У' => 'U',
		'Ў' => 'U',
		'Ф' => 'F',
		'Х' => 'KH',
		'Ц' => 'TS',
		'Ч' => 'CH',
		'Џ' => 'DH',
		'Ш' => 'SH',
		'Щ' => 'SHCH',
		'Ъ' => '',
		'Ы' => 'Y',
		'Ь' => '',
		'Э' => 'E',
		'Ю' => 'YU',
		'Я' => 'YA',

		'а' => 'a',
		'б' => 'b',
		'в' => 'v',
		'г' => 'h',
		'ѓ' => 'g',
		'ґ' => 'g',
		'д' => 'd',
		'е' => 'e',
		'ё' => 'yo',
		'є' => 'ye',
		'ж' => 'zh',
		'з' => 'z',
		'ѕ' => 'z',
		'и' => 'y',
		'й' => 'j',
		'ј' => 'j',
		'і' => 'i',
		'ї' => 'yi',
		'к' => 'k',
		'ќ' => 'k',
		'л' => 'l',
		'љ' => 'l',
		'м' => 'm',
		'н' => 'n',
		'њ' => 'n',
		'о' => 'o',
		'п' => 'p',
		'р' => 'r',
		'с' => 's',
		'т' => 't',
		'у' => 'u',
		'ў' => 'u',
		'ф' => 'f',
		'х' => 'kh',
		'ц' => 'ts',
		'ч' => 'ch',
		'џ' => 'dh',
		'ш' => 'sh',
		'щ' => 'shch',
		'ъ' => '',
		'ы' => 'y',
		'ь' => '',
		'э' => 'e',
		'ю' => 'yu',
		'я' => 'ya',
	];

	private function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init(): void {
		static $defaults = [
			'posts' => 0,
			'terms' => 0,
			'files' => 0,
		];

		register_setting( 'wwc2r', self::OPTIONS_KEY, [ 'default' => $defaults ] );

		$this->set_up_hooks();
	}

	public function set_up_hooks(): void {
		$options = (array) get_option( self::OPTIONS_KEY, [] );
		$posts   = (int) ( $options['posts'] ?? 0 );
		$terms   = (int) ( $options['terms'] ?? 0 );
		$files   = (int) ( $options['files'] ?? 0 );

		if ( 0 !== $posts ) {
			add_filter( 'get_sample_permalink', [ $this, 'get_sample_permalink' ], 50 );
			add_filter( 'wp_insert_attachment_data', [ $this, 'wp_insert_attachment_data' ], 50, 2 );
			add_filter( 'wp_insert_post_data', [ $this, 'wp_insert_post_data' ], 50, 2 );
		}

		if ( 0 !== $terms ) {
			add_filter( 'wp_update_term_data', [ $this, 'wp_update_term_data' ], 50, 4 );
			add_filter( 'wp_insert_term_data', [ $this, 'wp_insert_term_data' ], 50, 3 );
		}

		if ( 0 !== $files ) {
			add_filter( 'sanitize_file_name', [ $this, 'sanitize_file_name' ], 50 );
		}
	}

	public function reinstall_hooks(): void {
		remove_filter( 'get_sample_permalink', [ $this, 'get_sample_permalink' ], 50 );
		remove_filter( 'wp_insert_attachment_data', [ $this, 'wp_insert_attachment_data' ], 50 );
		remove_filter( 'wp_insert_post_data', [ $this, 'wp_insert_post_data' ], 50 );
		remove_filter( 'wp_update_term_data', [ $this, 'wp_update_term_data' ], 50 );
		remove_filter( 'wp_insert_term_data', [ $this, 'wp_insert_term_data' ], 50 );
		remove_filter( 'sanitize_file_name', [ $this, 'sanitize_file_name' ], 50 );

		$this->set_up_hooks();
	}

	/**
	 * @return array<string,string>
	 */
	private function get_table(): array {
		$locale = get_locale();
		$parts  = explode( '_', $locale, 2 );
		$lang   = $parts[0];
		$tbl    = self::$table;

		if ( in_array( $lang, [ 'bg', 'mk', 'ru', 'be' ], true ) ) {
			$tbl['Ц'] = 'C';
			$tbl['ц'] = 'c';
		}

		if ( in_array( $lang, [ 'bg', 'mk', 'ru' ], true ) ) {
			$tbl['Г'] = 'G';
			$tbl['г'] = 'g';
			$tbl['И'] = 'I';
			$tbl['и'] = 'i';
		}

		switch ( $lang ) {
			case 'bg':
				$tbl['Щ'] = 'STH';
				$tbl['щ'] = 'sth';
				$tbl['Ъ'] = 'A';
				$tbl['ъ'] = 'a';
				break;

			case 'ru':
				$tbl['Щ'] = 'SHH';
				$tbl['щ'] = 'shh';
				break;

			default:
				break;
		}

		return apply_filters( 'wwcyrtolat_xlat_table', $tbl );
	}

	/**
	 * @return array<string,string>
	 */
	private function get_re_table(): array {
		$locale = get_locale();
		$parts  = explode( '_', $locale, 2 );
		$lang   = $parts[0];

		$tbl = ( 'uk' === $lang ) ? self::$retable : [];
		return apply_filters( 'wwcyrtolat_xlat_re_table', $tbl );
	}

	private function transliterate( string $value, string $what ): string {
		$retbl = $this->get_re_table();
		if ( count( $retbl ) > 0 ) {
			$value = (string) preg_replace( array_keys( $retbl ), array_values( $retbl ), $value );
		}

		$tbl   = $this->get_table();
		$value = strtr( $value, $tbl );
		$value = (string) iconv( 'UTF-8', 'UTF-8//TRANSLIT//IGNORE', $value );
		/** @var string */
		$value = preg_replace( '/[^A-Za-z0-9_.-]/', '-', $value );
		/** @var string */
		$value = preg_replace( '/-{2,}/', '-', $value );
		$value = trim( $value, '-' );

		/** @psalm-suppress RedundantCastGivenDocblockType */
		return (string) apply_filters( 'transliterate_name', $value, $what );
	}

	/**
	 * @param mixed[] $name
	 * @return mixed[]
	 */
	public function get_sample_permalink( array $name ): array {
		/** @psalm-var array{0: string, 1: string} $name */
		$name[1] = $this->transliterate( $name[1], 'post_name' );
		return $name;
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $args
	 * @return mixed[]
	 */
	public function wp_insert_post_data( array $data, array $args ): array {
		/**
		 * @psalm-var array{post_name: string, post_status: string, post_type: string, post_parent: positive-int} $data
		 * @psalm-var array{ID: positive-int|null} $args
		 */
		$name              = $this->transliterate( urldecode( $data['post_name'] ), 'post_name' );
		$data['post_name'] = wp_unique_post_slug( $name, $args['ID'] ?? 0, $data['post_status'], $data['post_type'], $data['post_parent'] );
		return $data;
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $args
	 * @return mixed[]
	 */
	public function wp_insert_attachment_data( array $data, array $args ): array {
		/**
		 * @psalm-var array{'post_name': string, 'post_status': string, 'post_type': string, 'post_parent': positive-int, 'slug': string} $data
		 * @psalm-var array{'ID'?: positive-int} $args
		 */
		return $this->wp_insert_post_data( $data, $args );
	}

	/**
	 * @param mixed[] $data
	 * @param string $taxonomy
	 * @param mixed[] $args
	 * @return mixed[]
	 */
	public function wp_insert_term_data( $data, $taxonomy, $args ): array {
		/** @psalm-var array{'slug': string} $data */
		$data['slug'] = wp_unique_term_slug( $this->transliterate( urldecode( $data['slug'] ), 'term' ), (object) $args );
		return $data;
	}

	/**
	 * @param mixed[] $data
	 * @param int $id
	 * @param string $taxonomy
	 * @param mixed[] $args
	 * @return mixed[]
	 */
	public function wp_update_term_data( $data, $id, $taxonomy, $args ): array {
		return $this->wp_insert_term_data( $data, $taxonomy, $args );
	}

	public function sanitize_file_name( string $name ): string {
		return $this->transliterate( $name, 'file_name' );
	}
}
