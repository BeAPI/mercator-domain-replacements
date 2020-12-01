<a href="https://beapi.fr">![Be API Github Banner](.wordpress.org/banner-github.png)</a>

# Mercator Domain Replacements

This plugin is an addon for <a href="https://github.com/humanmade/Mercator">Mercator</a>

Force the replacement of all the original domains of the network by the corresponding mapped domains

# Requirements

- [WordPress](https://wordpress.org/) 4.6+
- Tested up to 5.4.*
- PHP 5.6+

# Installation

## WordPress

- Download and install using the built-in WordPress plugin installer.
- Site activate in the "Plugins" area of the admin.
- Optionally drop the entire `mercator-domain-replacements` directory into mu-plugins.
- Nothing more, this plugin is ready to use !

## [Composer](http://composer.rarst.net/)

- Add repository source : `{ "type": "vcs", "url": "https://github.com/BeAPI/mercator-domain-replacements" }`.
- Include `"beapi/mercator-domain-replacements": "*@stable"` in your composer file for last master's commits or a tag released.
- Nothing more, this plugin is ready to use !

# What ?

## Contributing

Please refer to the [contributing guidelines](.github/CONTRIBUTING.md) to increase the chance of your pull request to be merged and/or receive the best support for your issue.

### Issues & features request / proposal

If you identify any errors or have an idea for improving the plugin, feel free to open an [issue](../../issues/new). Please provide as much info as needed in order to help us resolving / approve your request.

# Who ?

Created by [Be API](https://beapi.fr), the French WordPress leader agency since 2009. Based in Paris, we are more than 30 people and always [hiring](https://beapi.workable.com) some fun and talented guys. So we will be pleased to work with you.

This plugin is only maintained, which means we do not guarantee some free support. Consider reporting an [issue](#issues--features-request--proposal) and be patient. 

If you really like what we do or want to thank us for our quick work, feel free to [donate](https://www.paypal.me/BeAPI) as much as you want / can, even 1€ is a great gift for buying cofee :)

## License

Mercator Domain Replacements is licensed under the [GPLv3 or later](LICENSE.md).

## Changelog

### 1.0.3

* Fixed: Fix the mapped upload path
* Add the mapped plugin and theme path
* Change the dns-prefetch path

### 1.0.2

* Fixed: replace by active current mapped domain only for current sub-site (not use the network domain)