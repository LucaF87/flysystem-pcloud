<?php

namespace LucaF87\PCloudAdapter\Tests\Feature;

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
        file_put_contents($tempFilePath, 'test text');

        $tempFileObject = new File($tempFilePath);
        $file = new UploadedFile(
            $tempFileObject->getPathname(),
            $tempFileObject->getFilename(),
            $tempFileObject->getMimeType(),
            0,
            true
        );
        $path = "users/2/test/";
        Storage::disk('pCloud')->putFileAs($path, $file, 'hello.txt');
        //Storage::disk('pCloud')->put($path, $file);

        $this->assertTrue(Storage::disk('pCloud')->exists($path.'hello.txt'));
    }

    public function test_read_file(){

        $path = "/users/2/test/";

        $this->assertTrue(Storage::disk('pCloud')->exists($path.'hello.txt'));
        $file = Storage::disk('pCloud')->get($path.'hello.txt');
        $files = Storage::disk('pCloud')->files($path.'hello.txt');
    }

}
