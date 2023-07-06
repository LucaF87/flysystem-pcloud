<?php

namespace LucaF87\LaravelPCloud\Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;
use Illuminate\Http\File;

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

        // Create temp file and get its absolute path
        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        // Save file data in file
        file_put_contents($tempFilePath, 'ciaoooOOOoOOOo');

        $tempFileObject = new File($tempFilePath);
        $file = new UploadedFile(
            $tempFileObject->getPathname(),
            $tempFileObject->getFilename(),
            $tempFileObject->getMimeType(),
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );
        $path = "users/2/test_pCloud/";
        Storage::disk('pCloud')->putFileAs($path, $file, 'ciao.txt');
        //Storage::disk('pCloud')->put($path, $file);

        $this->assertTrue(Storage::disk('pCloud')->exists($path.'ciao.txt'));
    }

    public function test_read_file(){

        $path = "/users/2/test_pCloud/";

        $this->assertTrue(Storage::disk('pCloud')->exists($path.'ciaoooo.txt'));
        $file = Storage::disk('pCloud')->getFileUrl($path.'ciaoooo.txt');
        dd($file);
    }

    public function test_pcloud_from_request(){

        $file = new \Symfony\Component\HttpFoundation\File\File();
        $file->storeAs();
    }
}
