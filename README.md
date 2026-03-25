<a href="https://beapi.fr">![Be API Github Banner](.wordpress.org/banner-github.png)</a>

# Mercator Domain Replacements

Add-on for [Mercator](https://github.com/humanmade/Mercator) on **WordPress multisite**. It rewrites the HTML output so internal URLs (network domain, subsite URLs, uploads, plugins, themes, DNS prefetch, etc.) use the **mapped public domains** instead of the original multisite domains.

Mercator maps domains to sites; this plugin makes sure the front-end output consistently reflects those mappings—including variants used in JavaScript (`https:\/\/…`), protocol-relative URLs (`//…`), and URL-encoded strings.

## Requirements

- [WordPress](https://wordpress.org/) **multisite** (4.6+)
- [Mercator](https://github.com/humanmade/Mercator) installed and domain mappings configured
- PHP **8.0+**

The plugin does nothing if Mercator is unavailable or if `$GLOBALS['mercator_current_mapping']` is not set in context.

## How it works

1. On `init`, it builds a map of source URLs → mapped URLs for the current network and for sites returned by `WP_Site_Query` (public sites by default).
2. It starts an output buffer callback that runs `str_replace` over the full response for each pair, including escaped and encoded forms.

**Note:** This is a full-page string replacement. Use it in environments where that trade-off is acceptable.

## Features

- Replaces subsite internal URLs with Mercator `mangle_url()` results for active mappings.
- Aligns network-level URLs (uploads, plugins, parent and child theme URIs, DNS prefetch host) with the mapped domain when the main site differs from the mapped URL.
- Optional early pass on `init` (priority `0`) for **FacetWP** AJAX refresh / autocomplete requests so URLs stay correct in those responses.

## Hooks

### `mercator.domain_replacement.site_query_args`

Filters the arguments passed to `WP_Site_Query` when collecting sites for replacement (default: public sites, ordered by ID, max 500).

```php
add_filter( 'mercator.domain_replacement.site_query_args', function ( array $args ): array {
	$args['number'] = 1000;
	return $args;
} );
```

## Installation

### WordPress

1. Install and activate **Mercator** according to its documentation.
2. Install this plugin (ZIP upload, or clone into `wp-content/plugins/` or `wp-content/mu-plugins/`).
3. **Network activate** if you use it as a normal plugin, or place the folder under `mu-plugins` if you run it as a must-use plugin.

### Composer

Package: `beapi/mercator-domain-replacements` (type `wordpress-muplugin`). Point Composer’s installer to your MU-plugins directory, for example:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/BeAPI/mercator-domain-replacements"
    }
  ],
  "require": {
    "beapi/mercator-domain-replacements": "^1.0"
  },
  "extra": {
    "installer-paths": {
      "wp-content/mu-plugins/{$name}/": ["type:wordpress-muplugin"]
    }
  }
}
```

Adjust `installer-paths` to match your project layout.

## Contributing

Issues and pull requests are welcome on [GitHub](https://github.com/BeAPI/mercator-domain-replacements). Please describe steps to reproduce for bugs and, when possible, the multisite + mapping setup you use.

## Credits

Made by [Be API](https://beapi.fr). This plugin is **maintained on a best-effort basis**; we do not guarantee free support. If it helps your project, you can [support us](https://www.paypal.me/BeAPI).

## License

GPL-3.0-or-later. See [LICENSE](LICENSE).

## Changelog

### 1.0.10

- Fixed: support mapping for encoded URLs

### 1.0.9

- Fixed: mapping network private domain

### 1.0.8

- Fixed: handle parent theme URLs

### 1.0.6

- Fixed: URLs on FacetWP refresh

### 1.0.5

- Add filter to modify site query args

### 1.0.4

- Fixed: URL fixes (notably Polylang / `sitemap.xml`)

### 1.0.3

- Fixed: mapped upload path
- Add mapped plugin and theme paths
- Change DNS-prefetch path

### 1.0.2

- Fixed: use active mapped domain for the current subsite only (not the network domain)
