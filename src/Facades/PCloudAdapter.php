<?php
namespace LucaF87\LaravelPCloud\Facades;

use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Config;
use pCloud\Sdk\App;
use LucaF87\LaravelPCloud\Facades\PCloud;
use pCloud\Sdk\File;
use pCloud\Sdk\Folder;

class PCloudAdapter implements FilesystemAdapter
{
    protected $prefix;

    /**@var App*/
    protected $service;

    protected $fileInstance;
    protected $folderInstance;

    public function __construct($client, $location)
    {
        $this->service = $client;
        $this->prefix = $location;
        $this->fileInstance = new File($this->service);
        $this->folderInstance = new Folder($this->service);
    }

    public function getService()
    {
        return $this->service;
    }

    public function fileExists(string $path): bool
    {
        $this->fileInstance->getInfo($path);

    }

    public function directoryExists(string $path): bool
    {
        return !empty($this->folderInstance->search($path));
    }

    public function deleteDirectory(string $path): void
    {
        $this->folderInstance->deleteRecursive($path);
    }

    public function visibility(string $path): FileAttributes
    {
        // TODO: Implement visibility() method.
    }

    public function mimeType(string $path): FileAttributes
    {
        // TODO: Implement mimeType() method.
    }

    public function lastModified(string $path): FileAttributes
    {
        // TODO: Implement lastModified() method.
    }

    public function fileSize(string $path): FileAttributes
    {
        // TODO: Implement fileSize() method.
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->fileInstance->move($source, $destination);
    }

    public function createDirectory(string $path, Config $config): void
    {
        $this->folderInstance->create($path);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // TODO: Implement setVisibility() method.
    }

    public function listContents(string $path, bool $deep): iterable
    {
        // TODO: Implement listContents() method.
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $this->fileInstance->upload($path);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        // TODO: Implement writeStream() method.
    }

    public function read(string $path): string
    {
        // TODO: Implement read() method.
    }

    public function readStream(string $path)
    {
        // TODO: Implement readStream() method.
    }

    public function delete(string $path): void
    {
        $this->fileInstance->delete($path);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $this->fileInstance->copy($source, $destination);
    }

    protected function readFileChunk($handle, $chunkSize)
    {
        $byteCount = 0;
        $giantChunk = '';
        while (! feof($handle)) {
            // fread will never return more than 8192 bytes if the stream is read buffered and it does not represent a plain file
            // An example of a read buffered file is when reading from a URL
            $chunk = fread($handle, 8192);
            $byteCount += strlen($chunk);
            $giantChunk .= $chunk;
            if ($byteCount >= $chunkSize) {
                return $giantChunk;
            }
        }
        return $giantChunk;
    }

    /**
     * Return bytes from php.ini value
     *
     * @param string $iniName
     * @param string $val
     * @return number
     */
    protected function getIniBytes($iniName = '', $val = '')
    {
        if ($iniName !== '') {
            $val = ini_get($iniName);
            if ($val === false) {
                return 0;
            }
        }
        $val = trim($val, "bB \t\n\r\0\x0B");
        $last = strtolower($val[strlen($val) - 1]);
        $val = (int)$val;
        switch ($last) {
            case 't':
                $val *= 1024;
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    /**
     * Return the number of memory bytes allocated to PHP
     *
     * @return int
     */
    protected function getMemoryUsedBytes()
    {
        return memory_get_usage(true);
    }

    /**
     * Get the size of a file resource
     *
     * @param $resource
     *
     * @return int
     */
    protected function getFileSizeBytes($resource)
    {
        return fstat($resource)['size'];
    }
}
