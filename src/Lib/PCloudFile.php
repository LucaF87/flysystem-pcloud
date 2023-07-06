<?php

namespace LucaF87\LaravelPCloud\Lib;

use pCloud\Sdk\App;
use pCloud\Sdk\Exception;
use pCloud\Sdk\Request;

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
     * Get link ( using File ID )
     *
     * @param int $fileId
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

}
