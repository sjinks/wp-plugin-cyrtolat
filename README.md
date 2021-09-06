# wp-plugin-cyrtolat

[![CI](https://github.com/sjinks/wp-plugin-cyrtolat/actions/workflows/ci.yaml/badge.svg)](https://github.com/sjinks/wp-plugin-cyrtolat/actions/workflows/ci.yaml)
[![Static Code Analysis](https://github.com/sjinks/wp-plugin-cyrtolat/actions/workflows/static-code-analysis.yml/badge.svg)](https://github.com/sjinks/wp-plugin-cyrtolat/actions/workflows/static-code-analysis.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=sjinks_wp-plugin-cyrtolat&metric=alert_status)](https://sonarcloud.io/dashboard?id=sjinks_wp-plugin-cyrtolat)

WordPress plugin to transliterate cyrillic slugs into latin.

The plugin was written as a replacement to [cyr3lat](https://wordpress.org/plugins/cyr3lat/),
which is no longer maintained and is vulnerable to SQL injection.

Unlike other plugins, this one does not use `sanitize_title` hook (which fires for many things
other than post / page / term slugs), which reduces the risk of unwanted side effects.

The plugin uses `wp_insert_post_data`, `wp_insert_attachment_data`, and `get_sample_permalink`
for posts / pages / attachments, and `wp_insert_term_data` / `wp_update_term_data` for terms.

The plugin provides three custom filters:
1. `wwcyrtolat_xlat_table`: allows for customization of the transliteration table.
2. `wwcyrtolat_xlat_re_table`: allows for customization of the regular expression based transliteration table.
3. `transliterate_name`: this one allows for modification of the transliterated name / slug.

## Installation

Run from WordPress root directory

```
composer require wildwolf/wp-cyrtolat
```

After that, please go to the Admin Dashboard => Plugins and activate the plugin.

## Configuration

Admin Dashboard => Options => WW CyrToLat
