<?php
namespace LucaF87\LaravelPCloud\Facades;

use ErrorException;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\InvalidVisibilityProvided;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use LucaF87\LaravelPCloud\Lib\PCloudFile;
use LucaF87\LaravelPCloud\Lib\PCloudFolder;
use pCloud\Sdk\Exception;
use Psr\Http\Message\StreamInterface;
use pCloud\Sdk\App;
use pCloud\Sdk\File;
use pCloud\Sdk\Folder;

class PCloudAdapter implements FilesystemAdapter
{
    /**@var App*/
    protected $service;

    protected $fileInstance;
    protected $folderInstance;

    public function __construct($client)
    {
        $this->service = $client;
        $this->fileInstance = new PCloudFile($this->service);
        $this->folderInstance = new PCloudFolder($this->service);
    }

    public function getService()
    {
        return $this->service;
    }

    public function fileExists(string $path): bool
    {
        //done
        try {
            $this->fileInstance->getInfoFromPath($path);
        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                return false;
            }
            throw new UnableToCheckExistence($e->getMessage());
        }

        return true;
    }

    public function directoryExists(string $path): bool
    {
        try {
            $this->folderInstance->search($path);
        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                return false;
            }
            throw new UnableToCheckExistence($e->getMessage());
        }

        return true;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        return;$this->writeStream($path, $contents, $config);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        //done
        $folders = explode('/',$path);
        $fileName = array_pop($folders);

        $folderParent = 0;
        foreach ($folders as $folder) {
            try {
                $folder = $this->folderInstance->create($folder, $folderParent);
                $folderParent = $folder;
            }catch (Exception $e){
                throw new UnableToCreateDirectory($e->getMessage()." ".$e->getCode());
            }
        }

        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];
        file_put_contents($tempFilePath, $contents);

        try {
            $this->fileInstance->upload($tempFilePath, $folderParent, $fileName);
        } catch (InvalidArgumentException $e) {
            throw new UnableToWriteFile($e->getMessage());
        }
    }
    
    public function getFileUrl(string $path){
        try {
            $url = $this->fileInstance->getLinkFromPath($path);
            return $url;
        } catch (ErrorException $e) {
            throw new UnableToReadFile($e->getMessage());
        }
    }

    public function read(string $path): string
    {
        try {
            $url = $this->fileInstance->getFileUrl($path);
            return $url;
        } catch (ErrorException $e) {
            throw new UnableToReadFile($e->getMessage());
        }

        return $content;
    }

    public function readStream(string $path)
    {
        try {
            $url = $this->fileInstance->getFileUrl($path);
            $stream = fopen($url, 'rb');
        } catch (ErrorException $e) {
            throw new UnableToReadFile($e->getMessage());
        }

        return $stream;
    }

    public function delete(string $path): void
    {
        try {
            $fileId = 0;  //TODO:
            $this->fileInstance->delete($fileId);
        } catch (\Exception $e) {
            throw new UnableToDeleteFile($e->getMessage());
        }
    }

    public function deleteDirectory(string $path): void
    {
        try {
            $folderId = 0; //TODO:
            $this->folderInstance->delete($folderId);
        } catch (\Exception $e) {
            throw new UnableToDeleteDirectory($e->getMessage());
        }
    }

    public function createDirectory(string $path, Config $config): void
    {
        try {
            $this->folderInstance->create($path);
        } catch (\Exception $e) {
            throw new UnableToCreateDirectory($e->getMessage());
        }
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw new InvalidVisibilityProvided();
    }

    public function visibility(string $path): FileAttributes
    {
        return $this->getFileInfo($path);
    }

    public function mimeType(string $path): FileAttributes
    {
        return $this->getFileInfo($path);
    }

    public function lastModified(string $path): FileAttributes
    {
        return $this->getFileInfo($path);
    }

    public function fileSize(string $path): FileAttributes
    {
        return $this->getFileInfo($path);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        // TODO: Implement listContents() method.
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $fileId = 0; //TODO:
        $folderId = 1;  //TODO:
        $this->fileInstance->move($fileId, $folderId);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $fileId = 0; //TODO:
        $folderId = 1;  //TODO:
        $this->fileInstance->copy($fileId, $folderId);
    }

    public function getFileInfo(string $path): FileAttributes
    {
        try {
            $info = $this->fileInstance->getInfoFromPath($path);
        } catch (\Exception $e) {
            throw new UnableToRetrieveMetadata($e->getMessage());
        }

        return $this->getFileAttributes($info);
    }

    public function getFileAttributes($file)
    {
        return new FileAttributes(
            path: $file->getUuid(),
            fileSize: $file->getSize(),
            lastModified: $file->getDatetimeUploaded() ? strtotime($file->getDatetimeUploaded()->format('Y-m-d H:i:s')) : null,
            mimeType: $file->getMimeType(),
            extraMetadata: array_merge([
                'originalFilename' => $file->getOriginalFilename(),
            ],
                (array)$file->getMetadata()
            )
        );
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

    function get_remote_data($url, $post_paramtrs=false,  $curl_opts=[])
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        //if parameters were passed to this function, then transform into POST method.. (if you need GET request, then simply change the passed URL)
        if($post_paramtrs){ 
            curl_setopt($c, CURLOPT_POST,TRUE);  
            curl_setopt($c, CURLOPT_POSTFIELDS, (is_array($post_paramtrs)? http_build_query($post_paramtrs) : $post_paramtrs) ); 
        }
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;');
        $headers[]= "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:76.0) Gecko/20100101 Firefox/76.0";	 
        $headers[]= "Pragma: ";  
        $headers[]= "Cache-Control: max-age=0";
        if (!empty($post_paramtrs) && !is_array($post_paramtrs) && is_object(json_decode($post_paramtrs))){ 
            $headers[]= 'Content-Type: application/json'; 
            $headers[]= 'Content-Length: '.strlen($post_paramtrs); 
        }
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($c, CURLOPT_MAXREDIRS, 10);
        //if SAFE_MODE or OPEN_BASEDIR is set,then FollowLocation cant be used.. so...
        $follow_allowed= ( ini_get('open_basedir') || ini_get('safe_mode')) ? false:true;  
        if ($follow_allowed){
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        }
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
        curl_setopt($c, CURLOPT_REFERER, $url);
        curl_setopt($c, CURLOPT_TIMEOUT, 60);
        curl_setopt($c, CURLOPT_AUTOREFERER, true);
        curl_setopt($c, CURLOPT_ENCODING, '');
        
        $data = curl_exec($c);
        
        $status = curl_getinfo($c); 
        curl_close($c);
        
        if ( $status['http_code'] != 200 ) {
            return false;
        }
        return $data;
    }
}
