<?php

namespace WildWold\WordPress;

class CyrToLat
{
    /**
     * @var string
     */
    const OPTIONS_KEY = 'wwc2r';

    private static $self = null;

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

    private static $table = [
        'А' => 'A',  'Б' => 'B',  'В' => 'V',  'Г' => 'H',    'Ѓ' => 'G',
        'Ґ' => 'G',  'Д' => 'D',  'Е' => 'E',  'Ё' => 'YO',   'Є' => 'YE',
        'Ж' => 'ZH', 'З' => 'Z',  'Ѕ' => 'Z',  'И' => 'I',    'Й' => 'J',
        'Ј' => 'J',  'І' => 'I',  'Ї' => 'YI', 'К' => 'K',    'Ќ' => 'K',
        'Л' => 'L',  'Љ' => 'L',  'М' => 'M',  'Н' => 'N',    'Њ' => 'N',
        'О' => 'O',  'П' => 'P',  'Р' => 'R',  'С' => 'S',    'Т' => 'T',
        'У' => 'U',  'Ў' => 'U',  'Ф' => 'F',  'Х' => 'KH',   'Ц' => 'TS',
        'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SHCH', 'Ъ' => '',
        'Ы' => 'Y',  'Ь' => '',   'Э' => 'E',  'Ю' => 'YU',   'Я' => 'YA',

        'а' => 'a',  'б' => 'b',  'в' => 'v',  'г' => 'h',    'ѓ' => 'g',
        'ґ' => 'g',  'д' => 'd',  'е' => 'e',  'ё' => 'yo',   'є' => 'ye',
        'ж' => 'zh', 'з' => 'z',  'ѕ' => 'z',  'и' => 'y',    'й' => 'j',
        'ј' => 'j',  'і' => 'i',  'ї' => 'yi', 'к' => 'k',    'ќ' => 'k',
        'л' => 'l',  'љ' => 'l',  'м' => 'm',  'н' => 'n',    'њ' => 'n',
        'о' => 'o',  'п' => 'p',  'р' => 'r',  'с' => 's',    'т' => 't',
        'у' => 'u',  'ў' => 'u',  'ф' => 'f',  'х' => 'kh',   'ц' => 'ts',
        'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '',
        'ы' => 'y',  'ь' => '',   'э' => 'e',  'ю' => 'yu',   'я' => 'ya'
    ];

    public static function instance()
    {
        if (!self::$self) {
            self::$self = new self();
        }

        return self::$self;
    }

    private function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        static $defaults = [
            'posts' => 0,
            'terms' => 0,
            'files' => 0
        ];

        register_setting('wwc2r', self::OPTIONS_KEY, ['default' => $defaults]);

        $this->setUpHooks();
    }

    public function setUpHooks()
    {
        $options = (array)get_option(self::OPTIONS_KEY);
        $posts   = $options['posts'] ?? 0;
        $terms   = $options['terms'] ?? 0;
        $files   = $options['files'] ?? 0;

        if ($posts) {
            add_filter('get_sample_permalink',      [$this, 'get_sample_permalink'],      50);
            add_filter('wp_insert_attachment_data', [$this, 'wp_insert_attachment_data'], 50, 2);
            add_filter('wp_insert_post_data',       [$this, 'wp_insert_post_data'],       50, 2);
        }

        if ($terms) {
            add_filter('wp_update_term_data', [$this, 'wp_update_term_data'], 50, 4);
            add_filter('wp_insert_term_data', [$this, 'wp_insert_term_data'], 50, 3);
        }

        if ($files) {
            add_filter('sanitize_file_name',  [$this, 'sanitize_file_name'],  50);
        }
    }

    public function reinstallHooks()
    {
        remove_filter('get_sample_permalink',      [$this, 'get_sample_permalink'],      50);
        remove_filter('wp_insert_attachment_data', [$this, 'wp_insert_attachment_data'], 50);
        remove_filter('wp_insert_post_data',       [$this, 'wp_insert_post_data'],       50);
        remove_filter('wp_update_term_data',       [$this, 'wp_update_term_data'],       50);
        remove_filter('wp_insert_term_data',       [$this, 'wp_insert_term_data'],       50);
        remove_filter('sanitize_file_name',        [$this, 'sanitize_file_name'],        50);

        $this->setUpHooks();
    }

    private function getTable() : array
    {
        $locale = get_locale();
        $parts  = explode('_', $locale, 2);
        $lang   = $parts[0];
        $table  = self::$table;

        switch ($lang) {
            case 'bg':
                $table['Г'] = 'G';   $table['г'] = 'g';
                $table['И'] = 'I';   $table['и'] = 'i';
                $table['Ц'] = 'C';   $table['ц'] = 'c';
                $table['Щ'] = 'STH'; $table['щ'] = 'sth';
                $table['Ъ'] = 'A';   $table['ъ'] = 'a';
                break;

            case 'mk':
                $table['Г'] = 'G';   $table['г'] = 'g';
                $table['И'] = 'I';   $table['и'] = 'i';
                $table['Ц'] = 'C';   $table['ц'] = 'c';
                break;

            case 'ru':
                $table['Г'] = 'G';   $table['г'] = 'g';
                $table['И'] = 'I';   $table['и'] = 'i';
                $table['Ц'] = 'C';   $table['ц'] = 'c';
                $table['Щ'] = 'SHH'; $table['щ'] = 'shh';
                break;

            case 'be':
                $table['Ц'] = 'C';   $table['ц'] = 'c';
                break;
        }

        return apply_filters('wwcyrtolat_xlat_table', $table);
    }

    private function getReTable() : array
    {
        $locale = get_locale();
        $parts  = explode('_', $locale, 2);
        $lang   = $parts[0];

        switch ($lang) {
            case 'uk': $table = self::$retable; break;
            default:   $table = []; break;
        }

        return apply_filters('wwcyrtolat_xlat_re_table', $table);
    }

    private function transliterate($value, $what)
    {
        $retbl = $this->getReTable();
        if ($retbl) {
            $value = preg_replace(array_keys($retbl), array_values($retbl), $value);
        }

        $table = $this->getTable();
        $value = strtr($value, $table);
        $value = iconv('UTF-8', 'UTF-8//TRANSLIT//IGNORE', $value);
        $value = preg_replace('/[^A-Za-z0-9_.-]/', '-', $value);
        $value = trim(preg_replace('/-{2,}/', '-', $value), '-');
        return apply_filters('transliterate_name', $value, $what);
    }

    public function get_sample_permalink($name)
    {
        $name[1] = $this->transliterate($name[1], 'post_name');
        return $name;
    }

    public function wp_insert_post_data($data, $args)
    {
        $name = $this->transliterate(urldecode($data['post_name']), 'post_name');
        $data['post_name'] = wp_unique_post_slug($name, $args['ID'] ?? 0, $data['post_status'], $data['post_type'], $data['post_parent']);
        return $data;
    }

    public function wp_insert_attachment_data($data, $args)
    {
        return $this->wp_insert_post_data($data, $args);
    }

    public function wp_insert_term_data($data, $taxonomy, $args)
    {
        $data['slug'] = wp_unique_term_slug($this->transliterate(urldecode($data['slug']), 'term'), (object)$args);
        return $data;
    }

    public function wp_update_term_data($data, $id, $taxonomy, $args)
    {
        return $this->wp_insert_term_data($data, $taxonomy, $args);
    }

    public function sanitize_file_name($name)
    {
        return $this->transliterate($name, 'file_name');
    }

    private function __clone()  {}
    private function __wakeup() {}
}
