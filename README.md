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
* extendable image storage, caching and processing drivers
* image processing concurrency protection
* fixed resolution limitation

### Under the hood

* [PSR-12](https://www.php-fig.org/psr/psr-12) compliant codebase
* [PSR-16](https://www.php-fig.org/psr/psr-16)-similar cache drivers
* [phpstan](https://phpstan.org/)-checked codebase (level 9, maximum)
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer#readme)-checked codebase (syntax)
* test-covered codebase (unit tests)

## Requirements

* PHP 7.4 (default on Ubuntu 20.04 LTS)
* PDO (PHP Extension)
* GD2 (PHP Extension)

## Overview

This service provides the following operations:

* grant access to images via token-based authentication
* revoke access to images
* upload and delete original images
* serve resized images
## Installation

Clone the repository in your preferred location. Then, install the required packages:

`composer install --no-dev`

Copy the configuration sample and then edit it to your needs:

`cp ./config.ini.sample ./config.ini`
`nano ./config.ini`

Set up the database:

`php ./setup.php`

Configure your web server to use the `public/index.php` file as the entry point.

## Getting started

There are two types of tokens:

1. The ADMIN_TOKEN, required to perform administrative actions, such as granting client tokens or upload images.
1. The client tokens, required for API clients to be able to retrieve resized/optimized images.

For simplicity, tokens can be any alphanumeric string.

First, ensure you have at least one `ADMIN_TOKEN[]` in your `config.php`. The ADMIN_TOKEN in an
array to support easy token rotation.

The following operations require the `ADMIN_TOKEN` to be set and sent via the `Authorization` header:
 * Grant client token
 * Revoke client token
 * Upload original image
 * Delete original image

In the examples below, we're assuming the URL is `storlex.test`.

To grant a client token, send a POST request to `/token`:

```shell
## Grant Token
curl -X "POST" "http://storlex.test/token" \
     -H 'Authorization: Bearer sheldon' \
     -H 'Content-Type: application/json; charset=utf-8' \
     -d $'{
  "token": "helloworld"
}'
```

You'll receive a success response:

```json
{"success":true, "message":null}
```

Now you can upload an original image using the Admin token, via multipart upload, for faster performance.

```shell
## Upload Original
curl -X "POST" "http://storlex.test/original" \
     -H 'Authorization: Bearer sheldon' \
     -F file=@Airbus_Pleiades_50cm_8bit_RGB_Yogyakarta.jpeg
```

Replace `Airbus_Pleiades_50cm_8bit_RGB_Yogyakarta.jpeg` with an image filename that you have locally.

If it all went good, you will receive a success response:

```json
{"success":true,"message":"File 'Airbus_Pleiades_50cm_8bit_RGB_Yogyakarta.jpeg' uploaded successfully"}
```

Finally, retrieve a resized image using the GET `/img` request:

```bash
## Serve 300x200 JPG
curl "http://storlex.test/img/Airbus_Pleiades_50cm_8bit_RGB_Yogyakarta.jpeg-310x500.webp" \
     -H 'Authorization: Bearer helloworld'
```

You'll receive the resized image, ready to display:

```
HTTP/1.1 200 OK
Server: nginx/1.21.2
Date: Thu, 11 Nov 2021 07:55:07 GMT
Content-Type: image/webp
Transfer-Encoding: chunked
Connection: close
X-Powered-By: PHP/7.4.23

RIFF...(the actual image data)...
```

The `/img` endpoint has the following format:

```
/img/<original_filename>-<width>x<height>.<format>
```

Formats currently supported are JPEG, PNG and WEBP.

## Extendiong the service

The app is built to be easily extendable, via Drivers which can be added in the `Drivers` folder.
Each Driver must implement the corresponding interface (`StorageInterface`, `ProcessorInterface`, `CacheInterface` etc.) and must extend the base driver class (`ImageStorage`, `ImageProcessor`, `ImageCache` etc.).
### Image storage

Currently, only the filesystem storage driver is implemented.

Feel free to add your own storage driver (such as S3, Azure, etc) by extending the `ImageStorage` class.

The image storage driver is selected in `config.ini` via the `IMAGE_STORAGE_DRIVER` setting.

### Image processing

Currently the app uses PHP's GD2 library for image processing.

Feel free to add your own image processing driver by extending the `ImageProcessing` class.

The image processing driver is selected in `config.ini` via the `IMAGE_PROCESSING_DRIVER` setting.

### Image caching

Currently the app uses filesystem for caching the images.

To add a Redis caching driver (recommended for high-traffic sites), extend the `ImageCache` class.

The image caching driver is selected in `config.ini` via the `IMAGE_CACHE_DRIVER` setting.

The Caching driver isn't a PSR-16 compliant driver, because it needs to invalidate multiple items based on a common tag (such as the original image filename).

For a Redis driver, look into the [HSET command](https://redis.io/commands/hset) for using tags.

## Local development

After the steps in the Installation section, you'll need to install the development dependencies:

`composer install`

Run the test suite:

`composer test` or `phpunit`

Ensure you don't have code or syntax errors.

`composer check`

To check for coverage, run:

`composer coverage` (this requires you have Xcode 3 installed)

To start a debugging session of the tests, run:

`composer debug`

## To do

- [ ] Redis cache driver
- [ ] Environment support, to hide errors in production
- [ ] Logging of requests, errors, warnings, etc.
- [ ] `/img` serving switch for crop/no-crop
- [ ] `/img` serving quality setting (ex. `q80`)
- [ ] resize on a single axis (for example `800x` or `x600`)
- [ ] broken photo placeholder (when a photo cannot be served)

## Contributing

If you wish to improve this project, please open an issue or pull request.

All code must be in the [PSR-12](https://www.php-fig.org/psr/psr-12) format,
and must pass the phpstan and PHPCS checks.

## License

This software is licensed under the [MIT license](https://opensource.org/licenses/MIT).
