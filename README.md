# WordPress Core Installer

[![Tests](https://img.shields.io/github/actions/workflow/status/roots/wordpress-core-installer/tests.yml?branch=master&logo=github&label=CI&style=flat-square)](https://github.com/roots/wordpress-core-installer/actions/workflows/tests.yml)
[![Packagist Downloads](https://img.shields.io/packagist/dt/roots/wordpress-core-installer?label=downloads&colorB=2b3072&colorA=525ddc&style=flat-square)](https://packagist.org/packages/roots/wordpress-core-installer)
![GitHub tag](https://img.shields.io/github/tag/roots/wordpress-core-installer.svg?style=flat-square)
[![Follow Roots](https://img.shields.io/badge/follow%20@rootswp-1da1f2?logo=twitter&logoColor=ffffff&message=&style=flat-square)](https://twitter.com/rootswp)
[![Sponsor Roots](https://img.shields.io/badge/sponsor%20roots-525ddc?logo=github&style=flat-square&logoColor=ffffff&message=)](https://github.com/sponsors/roots)

> [!NOTE]
> This is a fork of [johnpbloch/wordpress-core-installer](https://github.com/johnpbloch/wordpress-core-installer) with fixes to enhance compatibility with [roots/wordpress](https://packagist.org/packages/roots/wordpress).

A custom Composer plugin to install WordPress core outside of `vendor`.

This installer is meant to support a rather specific WordPress installation setup in which WordPress is installed in a subdirectory ([see the WordPress codex on that subject](https://codex.wordpress.org/Giving_WordPress_Its_Own_Directory)) and in which the location of `WP_CONTENT_DIR` and `WP_CONTENT_URL` have been customized to be outside of WordPress core ([see the WordPress codex on that subject](https://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder)). This is because composer will delete your whole wp-content directory every time it updates core if you don't separate the two. If that installation setup isn't what you are looking for, then this installer is probably not something you will want to use.

For more information on using Composer with WordPress, check out our [Composer WordPress resources](https://roots.io/composer-wordpress-resources/). Use [WP Packages](https://wp-packages.org/) to install WordPress plugins and themes via Composer, and see the [`roots/wordpress` documentation](https://wp-packages.org/roots-wordpress) for WordPress core installation options.

## Support us

Roots is an independent open source org, supported only by developers like you. Your sponsorship funds [WP Packages](https://wp-packages.org/) and the entire Roots ecosystem, and keeps them independent. Support us by purchasing [Radicle](https://roots.io/radicle/) or [sponsoring us on GitHub](https://github.com/sponsors/roots) — sponsors get access to our private Discord.

## Requirements

- Composer 2+
- PHP 7.2.24+

Composer 2 requires explicitly allowing plugins. If needed, run:

```bash
composer config --no-plugins allow-plugins.roots/wordpress-core-installer true
```

## Community

Keep track of development and community news.

- Join us on Discord by [sponsoring us on GitHub](https://github.com/sponsors/roots)
- Join us on [Roots Discourse](https://discourse.roots.io/)
- Follow [@rootswp on Twitter](https://twitter.com/rootswp)
- Follow the [Roots Blog](https://roots.io/blog/)
- Subscribe to the [Roots Newsletter](https://roots.io/subscribe/)
