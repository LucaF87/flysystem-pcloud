<?php

namespace LucaF87\PCloudAdapter\Tests\Unit;

use Illuminate\Support\Facades\Storage;
use vendor\PCloudAdapter\src\Facades\PCloudAdapter;
use LucaF87\PCloudAdapter\PCloudAdapter\src\Providers\CustomPCloudServiceProvider;
use Orchestra\Testbench\TestCase;
use pCloud\Sdk\App;
use pCloud\Sdk\Folder;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [CustomPCloudServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function makeDirTest(){

        /*
        $client = new App();
        $client->setAppKey('7nVfAJz6kvX');
        $client->setAppSecret('uWiLqhwiEmLemPOfATsB05lwExdV');
        $client->setAccessToken('DIeQZ7nVfAJz6kvXZbTOio7ZU81GznDAPWj6NGtWfloOxf4hnwF7');
        $client->setLocationId(2);
        $adapter = new PCloudAdapter($client);
        */


        try {
            Storage::disk('pCloud')->makeDirectory('lol3');
        }catch (\Exception $e){
            echo $e->getMessage();
        }


    }
}