# Flysystem adapter for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/LucaF87/flysystem-pcloud.svg?style=flat-square)](https://packagist.org/packages/LucaF87/flysystem-pcloud)
[![Total Downloads](https://img.shields.io/packagist/dt/LucaF87/flysystem-pcloud.svg?style=flat-square)](https://packagist.org/packages/LucaF87/flysystem-pcloud)

Flysystem adapter for pCloud with support for Laravel v10+.

A PHP library to access [pCloud API](https://docs.pcloud.com/)

---

## Get started

### Register your application

In order to use this package, you have to register your application in [My applications](https://docs.pcloud.com).

---

## Installation

You can install the package via composer:

```bash
composer require lucaf87/flysystem-pcloud
```

or add the following to `composer.json` file

~~~~
"require": {
  "lucaf87/flysystem-pcloud": "1.0"
}
~~~~

Add the following config to the `disk` array in config/filesystems.php

```php
[
    'pCloud' => [
        'driver' => 'pCloud',
        'clientId' => env('PCLOUD_CLIENT_ID'),
        'clientSecret' => env('PCLOUD_CLIENT_SECRET'),
        'accessToken' => env('PCLOUD_ACCESS_TOKEN'),
        'locationId' => env('PCLOUD_LOCATION_ID'),
    ]
]
```

Then set the `FILESYSTEM_DISK` to `pCloud` in your .env

```env
FILESYSTEM_DISK=pCloud
```

Publish configuration file
```
php artisan vendor:publish --provider="LucaF87\PCloudAdapter\Providers\CustomPCloudServiceProvider" --force
```

Add the following to your .env
```
PCLOUD_CLIENT_ID=[Get this from https://docs.pcloud.com/my_apps/]
PCLOUD_CLIENT_SECRET=[Get this from https://docs.pcloud.com/my_apps/]
PCLOUD_ACCESS_TOKEN=[leave blank]
PCLOUD_LOCATION_ID=[leave blank]
```

---

## Generate Auth

### Artisan 
```php artisan flysystem-pcloud:token```

### Manual
Generate Authorize Code, Navigate to below link (Replace CLIENT_ID with your application Client ID)
https://my.pcloud.com/oauth2/authorize?client_id=CLIENT_ID&response_type=code

After you get the access code and the hostname, next step is to generate Access Token.
**Before you navigate to below link, make sure to replace Client ID, Secret and Access Code & THE HOST NAME (api.pcloud.com) with what was on the page before
https://api.pcloud.com/oauth2_token?client_id=xxxxxxxxx&client_secret=xxxxxxxxx&code=xxxxxxxxx

``` 
Copy the access_token and the locationid to the .env 
```

---

## Example
```php

Storage::disk('pCloud')->putFileAs('files', new File('/tmp/file.txt'), 'file-name.txt');

Storage::disk('pCloud')->exists('/files/file-name.txt'));

Storage::disk('pCloud')->fileUrl('/files/file-name.txt'));

```

**Get the content of a file (Work in progress)**
```php
$contents = Storage::disk('pCloud')->get('/files/file-name.txt');
```

**Deleting a file:**
```php
Storage::disk('pCloud')->delete('/files/file-name.txt');
```
**Deleting a directory:**
```php
Storage::disk('pCloud')->deleteDirectory('/files'));
Storage::disk('pCloud')->deleteDirectory('/files/test'));
```

**Getting the mimetype of a file**
```php
$mimeType = Storage::disk('pCloud')->mimeType('/files/file-name.txt');
```

**Get the info of a file**
```php
$bytes = Storage::disk('pCloud')->fileInfo('/files/file-name.txt');
```

**Get a list of files**
```php
$files = Storage::disk('pCloud')->files('/files'));
```

## Testing

```bash
composer test
```

## Credits

- [LucaF87](https://github.com/LucaF87)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
