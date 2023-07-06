<?php

namespace LucaF87\LaravelPCloud\Providers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use LucaF87\LaravelPCloud\Console\CreateAuthorisationTokenCommand;
use LucaF87\LaravelPCloud\Facades\PCloudAdapter;
use League\Flysystem\Filesystem;
use pCloud\Sdk\App;
use pCloud\Sdk\Folder;
use pCloud\Sdk\File;

class CustomPCloudServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-pcloud.php', 'laravel-pcloud');

        $this->app->bind('pcloud', function($app) {
            return new App();
        });
    }

    public function boot()
    {
        $this->publishes([
            // Config
            __DIR__ . '/../config/laravel-pcloud.php' => config_path('laravel-pcloud.php'),
        ], 'laravel-pcloud');

        $this->commands([
            CreateAuthorisationTokenCommand::class
        ]);

        Storage::extend('pCloud', function($app, $config) {
            $client = new App();
            $client->setAppKey($config['clientId']);
            $client->setAppSecret($config['clientSecret']);
            $client->setAccessToken($config['accessToken']);
            $client->setLocationId($config['locationId']);
            $adapter = new PCloudAdapter($client);

            FilesystemAdapter::macro('fileInfo', function (string $path) use ($adapter) {
                return $adapter->getFileInfo($path);
            });
            FilesystemAdapter::macro('getFileUrl', function (string $path) use ($adapter) {
                return $adapter->getFileUrl($path);
            });

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}
