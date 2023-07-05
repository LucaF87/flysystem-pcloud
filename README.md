# pCloud SDK for Laravel

A PHP library to access [pCloud API](https://docs.pcloud.com/)

---

## Table of Contents
* [System requirements](#system-requirements)
* [Get started](#get-started)
  * [Register your application](#register-your-application)
* [Install the SDK](#install-the-sdk)
  * [Using Composer](#using-composer)
  * [Manually](#manually)
* [Initializing the SDK](#initializing-the-sdk)
* [Example](#example)

---

## System requirements

  * PHP 5.6+
  * PHP [cURL extension](http://php.net/manual/en/curl.setup.php)

---

## Get started

### Register your application

In order to use this SDK, you have to register your application in [My applications](https://docs.pcloud.com).

---

## Install the SDK

### Using Composer

Install [Composer](http://getcomposer.org/download/).

```bash
$ composer require LucaF87/laravel-pcloud
```

or add the following to `composer.json` file

~~~~
"require": {
  "LucaF87/laravel-pcloud": "^1.0"
}
~~~~

~~~~

php artisan vendor:publish --provider="LucaF87\LaravelPCloud\Providers\CustomPCloudServiceProvider" --force

//Add the following to your .env

PCLOUD_CLIENT_ID=[Get this from https://docs.pcloud.com/my_apps/]
PCLOUD_CLIENT_SECRET=[Get this from https://docs.pcloud.com/my_apps/]
PCLOUD_ACCESS_TOKEN=[leave blank]
PCLOUD_LOCATION_ID=[leave blank]
~~~~

~~~~
//Add the following to your config/filesystem.php

'pCloud' => [
    'driver' => 'pCloud',
    'clientId' => env('PCLOUD_CLIENT_ID'),
    'clientSecret' => env('PCLOUD_CLIENT_SECRET'),
    'accessToken' => env('PCLOUD_ACCESS_TOKEN'),
    'locationId' => env('PCLOUD_LOCATION_ID'),
],
~~~~

---

## Generate Auth

### Artisan 
```php artisan laravel-pcloud:token```

### Manual
Generate Authorize Code, Navigate to below link (Replace CLIENT_ID with your application Client ID)
https://my.pcloud.com/oauth2/authorize?client_id=CLIENT_ID&response_type=code

After you get the access code and the hostname, next step is to generate Access Token.
**Before you navigate to below link, make sure to replace Client ID, Secret and Access Code & THE HOST NAME (api.pcloud.com) with what was on the page before
https://api.pcloud.com/oauth2_token?client_id=xxxxxxxxx&client_secret=xxxxxxxxx&code=xxxxxxxxx

Copy the access_token and the locationid to the .env

---

## Example
~~~~
use pCloud\Sdk\App;
use pCloud\Sdk\File;
use pCloud\Sdk\Folder;

protected $pCloudApp;

public function __construct()
{
    $this->pCloudApp = new App();
    $this->pCloudApp->setAccessToken(config('laravel-pcloud.access_token'));
    $this->pCloudApp->setLocationId(config('laravel-pcloud.location_id'));
}

// Create Folder instance

$pcloudFolder = new Folder($this->pCloudApp);

// Create new folder in root

$folderId = $pcloudFolder->create("New folder");

// Create File instance

$pcloudFile = new File($this->pCloudApp);

// Upload new file in created folder

$fileMetadata = $pcloudFile->upload("./sample.png", $folderId);

// Get folder content

$folderContent = $pcloudFolder->getContent($folderId);

// Get file

$pcloudFile = new File($this->pCloudApp);
$pcloudFile->getLink((int)$fileMetadata->metadata->fileid)
~~~~

### Creating custom requests

~~~~
use pCloud\Sdk\Request;
use pCloud\Sdk\App;

protected $pCloudApp;

public function __construct()
{
    $this->pCloudApp = new App();
    $this->pCloudApp->setAccessToken(config('laravel-pcloud.access_token'));
    $this->pCloudApp->setLocationId(config('laravel-pcloud.location_id'));
}

$method = "userinfo";
$params = array();

$request = new Request($this->pCloudApp);
$response = $request->get($method, $params); // the second argument is optional
~~~~