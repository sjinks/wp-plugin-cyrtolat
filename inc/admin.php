<?php
declare(strict_types=1);

namespace WildWolf\WordPress;

final class CyrToLatAdmin
{
    /**
     * @var CyrToLatAdmin|null
     */
    private static $self = null;

    /**
     * @var string
     */
    const OPTIONS_KEY = CyrToLat::OPTIONS_KEY;

    public static function instance(): self
    {
        if (!self::$self) {
            self::$self = new self();
        }

        return self::$self;
    }

    private function __construct()
    {
        \add_action('init', [$this, 'init']);
    }

    public function init(): void
    {
        \load_plugin_textdomain('wwc2r', /** @scrutinizer ignore-type */ false, \plugin_basename(dirname(__DIR__)) . '/lang/');

        \add_action('admin_menu', [$this, 'admin_menu']);
        \add_action('admin_init', [$this, 'admin_init']);
    }

    public function admin_menu(): void
    {
        \add_options_page(\__('WW CyrToLat', 'wwc2r'), \__('WW CyrToLat', 'wwc2r'), 'manage_options', 'wwc2r', [$this, 'options_page']);
    }

    public function admin_init(): void
    {
        \add_settings_section('wwc2r_section_main', '', '__return_null', 'wwc2r');
        \add_settings_field('posts', \__('Transliterate post / page slugs', 'wwc2r'), [$this, 'checkbox_field'], 'wwc2r', 'wwc2r_section_main', ['label_for' => 'posts']);
        \add_settings_field('terms', \__('Transliterate term slugs',        'wwc2r'), [$this, 'checkbox_field'], 'wwc2r', 'wwc2r_section_main', ['label_for' => 'terms']);
        \add_settings_field('files', \__('Transliterate file names',        'wwc2r'), [$this, 'checkbox_field'], 'wwc2r', 'wwc2r_section_main', ['label_for' => 'files']);
    }

    public function checkbox_field(array $args): void
    {
        $options = \get_option(self::OPTIONS_KEY);
        $name    = \esc_attr(self::OPTIONS_KEY);
        $id      = \esc_attr($args['label_for']);
        $checked = \checked($options[$id], 1, false);
        echo <<< EOT
<input type="checkbox" name="{$name}[{$id}]" id="{$id}" value="1"{$checked}/>
EOT;
    }

    public function options_page(): void
    {
        if (\current_user_can('manage_options')) {
            require __DIR__ . '/../views/settings.php';
        }
    }

    private function __clone()  {}
    private function __wakeup() {}
}
