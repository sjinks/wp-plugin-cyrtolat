# wp-plugin-cyrtolat

WordPress plugin to transliterate cyrillic slugs into latin.

The plugin was written as a replacement to [cyr3lat](https://wordpress.org/plugins/cyr3lat/),
which is no longer maintained and is vulnerable to SQL injection.

Unlink other plugins, this one does not use `sanitize_title` hook (which fires for many things
other than post / page / term slugs), which reduces the risk of unwanted side effects.

The plugin uses `name_save_pre` and `get_sample_permalink` for posts / pages, and
`wp_insert_term_data` / `wp_update_term_data` for terms.

The plugin provides two custom filters:
1. `wwcyrtolat_xlat_table`: this one allows for customization of the transliteration table.
2. `transliterate_name`: this one allows for modification of the transliterated name / slug.

## Installation

**Via composer**

Run from WordPress root directory

```
composer require wildwolf/wp-cyrtolat
```

**Traditional way**

Upload the plugin to `wp-content/plugins/`, go to the Admin Dashboard => Plugins and activate the plugin.