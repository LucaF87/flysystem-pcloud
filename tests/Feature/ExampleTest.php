<?php

namespace LucaF87\LaravelPCloud\Tests\Feature;

use Faker\Core\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('api/');

        $response->assertStatus(200);
    }

    public function test_pcloud(){
        //Storage::disk('pCloud')->createDirectory('lol5');  //Funziona ma l'idee non vede il metodo
        Storage::disk('pCloud')->createDirectory('lol7');
    }

    public function test_pcloud_from_request(){

        $file = new \Symfony\Component\HttpFoundation\File\File();
        $file->storeAs();
    }
}
