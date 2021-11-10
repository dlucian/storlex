# Storlex Image Server

[![Build Application](https://github.com/dlucian/storlex/actions/workflows/build.yml/badge.svg)](https://github.com/dlucian/storlex/actions/workflows/build.yml)

This is vanilla PHP image resizing, caching and serving server.

## Features

* lightweight, fast, pure-PHP server
* no production package dependencies
* on-the-fly resizing and caching
* provides token-based authentication
* supports multiple image formats (jpeg, png, webp)
* supports multiple image sizes
* extendable image storage drivers
* extendable image caching drivers

### Under the hood

* [PSR-12](https://www.php-fig.org/psr/psr-12) compliant codebase
* ?[PSR-16](https://www.php-fig.org/psr/psr-16) compliant cache drivers
* [phpstan](https://phpstan.org/)-checked codebase (level 9, maximum)
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer#readme)-checked codebase (syntax)
* test-covered codebase (unit tests)

## Installation

Clone the repository in your preferred location. Then, install the required packages:

`composer install --no-dev`

Copy the configuration sample and then edit it to your needs:

`cp ./config.ini.sample ./config.ini`
`nano ./config.ini`

Set up the database:

`php ./setup.php`

Configure your web server to use the `public/index.php` file as the entry point.

## Local development

After the above steps, you'll need to install the development dependencies:

`composer install`

Run the test suite:

`composer test` or `phpunit`

Ensure you don't have code or syntax errors.

`composer check`

To check for coverage, run:

`composer coverage` (this requires you have Xcode 3 installed)

To start a debugging session of the tests, run:

`composer debug`

## Contributing

If you wish to improve this project, please open an issue or pull request.

All code must be in the [PSR-12](https://www.php-fig.org/psr/psr-12) format,
and must pass the phpstan and PHPCS checks.

## License

This software is licensed under the [MIT license](https://opensource.org/licenses/MIT).
