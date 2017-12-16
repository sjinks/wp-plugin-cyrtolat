<?php

namespace WildWold\WordPress;

class CyrToLat
{
    private static $self = null;

    private static $table = [
        'А' => 'A',  'Б' => 'B',  'В' => 'V',  'Г' => 'H',    'Ѓ' => 'G',
        'Ґ' => 'G',  'Д' => 'D',  'Е' => 'E',  'Ё' => 'YO',   'Є' => 'YE',
        'Ж' => 'ZH', 'З' => 'Z',  'Ѕ' => 'Z',  'И' => 'I',    'Й' => 'Y',
        'Ј' => 'J',  'І' => 'I',  'Ї' => 'YI', 'К' => 'K',    'Ќ' => 'K',
        'Л' => 'L',  'Љ' => 'L',  'М' => 'M',  'Н' => 'N',    'Њ' => 'N',
        'О' => 'O',  'П' => 'P',  'Р' => 'R',  'С' => 'S',    'Т' => 'T',
        'У' => 'U',  'Ў' => 'U',  'Ф' => 'F',  'Х' => 'KH',   'Ц' => 'C',
        'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SHCH', 'Ъ' => '',
        'Ы' => 'Y',  'Ь' => '',   'Э' => 'E',  'Ю' => 'YU',   'Я' => 'YA',
        'а' => 'a',  'б' => 'b',  'в' => 'v',  'г' => 'h',    'ѓ' => 'g',
        'ґ' => 'g',  'д' => 'd',  'е' => 'e',  'ё' => 'yo',   'є' => 'ye',
        'ж' => 'zh', 'з' => 'z',  'ѕ' => 'z',  'и' => 'y',    'й' => 'i',
        'ј' => 'j',  'і' => 'i',  'ї' => 'yi', 'к' => 'k',    'ќ' => 'k',
        'л' => 'l',  'љ' => 'l',  'м' => 'm',  'н' => 'n',    'њ' => 'n',
        'о' => 'o',  'п' => 'p',  'р' => 'r',  'с' => 's',    'т' => 't',
        'у' => 'u',  'ў' => 'u',  'ф' => 'f',  'х' => 'kh',   'ц' => 'c',
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
        add_filter('name_save_pre',        [$this, 'name_save_pre']);
        add_filter('get_sample_permalink', [$this, 'get_sample_permalink']);
        add_filter('wp_update_term_data',  [$this, 'wp_update_term_data'], 10, 4);
        add_filter('wp_insert_term_data',  [$this, 'wp_insert_term_data'], 10, 3);
    }

    private function getTable() : array
    {
        $locale = get_locale();
        $parts  = explode($locale, '_', 2);
        $lang   = $parts[0];
        $table  = self::$table;

        switch ($lang) {
            case 'bg':
                $table['Г'] = 'G';   $table['г'] = 'g';
                $table['И'] = 'I';   $table['и'] = 'i';
                $table['Й'] = 'J';   $table['й'] = 'j';
                $table['Щ'] = 'STH'; $table['щ'] = 'sth';
                $table['Ъ'] = 'A';   $table['ъ'] = 'a';
                break;

            case 'mk':
                $table['Г'] = 'G';   $table['г'] = 'g';
                $table['И'] = 'I';   $table['и'] = 'i';
                $table['Й'] = 'J';   $table['й'] = 'j';
                break;

            case 'ru':
                $table['Г'] = 'G';   $table['г'] = 'g';
                $table['И'] = 'I';   $table['и'] = 'i';
                $table['Й'] = 'J';   $table['й'] = 'j';
                $table['Щ'] = 'SHH'; $table['щ'] = 'shh';
                break;

            case 'be':
                $table['Й'] = 'J';   $table['й'] = 'j';
                break;
        }

        return apply_filters('wwcyrtolat_xlat_table', $table);
    }

    private function transliterate($value, $what)
    {
        $table = $this->getTable();
        $value = strtr($value, $table);
        $value = iconv('UTF-8', 'UTF-8//TRANSLIT//IGNORE', $value);
        $value = preg_replace('/[^A-Za-z0-9_.-]/', '-', $value);
        $value = trim(preg_replace('/-{2,}/', '-', $value), '-');
        return apply_filters('transliterate_name', $value, $what);
    }

    public function name_save_pre($name)
    {
        return $this->transliterate($name, 'post_name');
    }

    public function get_sample_permalink($name)
    {
        $name[1] = $this->transliterate($name[1], 'post_name');
        return $name;
    }

    public function wp_insert_term_data($data, $taxonomy, $args)
    {
        $args['slug'] = $data['slug'];
        $data['slug'] = wp_unique_term_slug($this->transliterate(urldecode($data['slug']), 'term'), (object)$args);
        return $data;
    }

    public function wp_update_term_data($data, $id, $taxonomy, $args)
    {
        return $this->wp_insert_term_data($data, $taxonomy, $args);
    }

    private function __clone()  {}
    private function __wakeup() {}
}
