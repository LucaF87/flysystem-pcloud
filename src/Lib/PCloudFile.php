<?php

namespace LucaF87\PCloudAdapter\Lib;

use pCloud\Sdk\App;
use pCloud\Sdk\Exception;
use pCloud\Sdk\Request;
use pCloud\Sdk\Config;

class PCloudFile extends \pCloud\Sdk\File
{
    /**
     * Holds the Request class
     *
     * @var Request $request
     */
    private $request;

    /**
     * Main class constructor
     *
     * @param App $app
     */
    function __construct(App $app)
    {
        $this->request = new Request($app);
        parent::__construct($app);
    }

    public function getInfoFromPath(string $filePath)
    {
        $response = $this->request->get("checksumfile", array("path" => "/".$filePath));
        return property_exists($response, 'metadata') ? $response->metadata : $response;
    }

    public function getDownloadLink(int $fileId, string $code){
        $response = $this->request->get("getpublinkdownload", array("fileid" => $fileId, 'code' => $code));
        return property_exists($response, 'metadata') ? $response->metadata : $response;
    }

    /**
     * Get link ( using FilePath )
     *
     * @param string $filePath
     *
     * @return string
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function getLinkFromPath(string $filePath): string
    {
        $params = array(
            "path" => "/".$filePath,
            "forcedownload" => true
        );

        $response = $this->request->get("getfilelink", $params);

        if (property_exists($response, 'hosts')) {
            $link = "https://" . $response->hosts[0] . $response->path;
        } else {
            throw new Exception("Failed to get file link!");
        }

        return $link;
    }

    /**
     * Download file  ( using FilePath )
     *
     * @param string $filePath File Path
     * @param string $destination The destination, where the file will be stored!
     *
     * @return string
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function downloadFromPath(string $filePath, string $destination = ""): string
    {
        $fileLink = $this->getLinkFromPath($filePath);

        if (!empty($destination)) {
            $destination = str_replace(array("\\", "/"), DIRECTORY_SEPARATOR, $destination) . DIRECTORY_SEPARATOR;
        }

        if (!empty($destination) && !is_dir($destination)) {
            if (!mkdir($destination,0777,true)) {
                throw new Exception("Couldn't create destination folder");
            }
        }

        $parts = explode("/", $fileLink);
        $path = $destination . rawurldecode(end($parts));

        $source = fopen($fileLink, "rb");
        $file = fopen($path . ".download", "wb");
        while (!feof($source)) {
            $content = fread($source, Config::$filePartSize);
            fwrite($file, $content);
        }
        fclose($file);
        fclose($source);

        rename($path . ".download", $path);
        return $path;
    }

    /**
     * Delete file ( using filePath )
     *
     * @param string $filePath
     *
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function deleteFromPath(string $filePath)
    {
        $response = $this->request->get("deletefile", array("path" => "/".$filePath));

        return property_exists($response, 'metadata') ? $response->metadata->isdeleted : $response;
    }

    /**
     * Rename file ( using filePath )
     *
     * @param string $filePath
     * @param string $name
     *
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function renameByPath(string $filePath, string $name)
    {
        if (empty($name)) {
            throw new Exception("Please, provide valid file name!");
        }

        $params = array(
            "path" => $filePath,
            "toname" => $name
        );

        return $this->request->get("renamefile", $params);
    }



    /**
     * Copy file
     *
     * @param string $filePath
     * @param string $folderPath
     *
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function copyByPath(string $filePath, string $folderPath)
    {
        $params = array(
            "path" => $filePath,
            "topath" => $folderPath
        );

        return $this->request->get("copyfile", $params);
    }

    /**
     * Moves file
     *
     * @param string $filePath
     * @param string $folderPath
     *
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function moveByPath(string $filePath, string $folderPath)
    {
        $params = array(
            "path" => $filePath,
            "topath" => $folderPath
        );

        return $this->request->get("renamefile", $params);
    }

}
