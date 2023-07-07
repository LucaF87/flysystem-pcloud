<?php

namespace LucaF87\PCloudAdapter\Lib;

use InvalidArgumentException;
use pCloud\Sdk\App;
use pCloud\Sdk\Exception;
use pCloud\Sdk\Request;

class PCloudFolder extends \pCloud\Sdk\Folder
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
    
    public function create(?string $name, ?int $parent = 0)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Please, provide valid folder name");
        }

        $params = array(
            "name" => $name,
            "folderid" => $parent
        );

        $response = $this->request->get("createfolderifnotexists", $params);

        return property_exists($response, 'metadata') ? $response->metadata->folderid : $response;
    }

    /**
     * Get folder content
     *
     * @param string $folderPath
     *
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function getContentFromPath(string $folderPath, bool $deep)
    {
        $params = [
            'path' => $folderPath
        ];
        if ($deep) {
            $params['recursive'] = true;
        }
        $folderMetadata = $this->request->get("listfolder", $params);

        return property_exists($folderMetadata, 'metadata') ? $folderMetadata->metadata->contents : $folderMetadata;
    }


    /**
     * Delete folder
     *
     * @param string $folderPath
     *
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function deleteFromPath(string $folderPath)
    {
        $response = $this->request->get("deletefolder", array("path" => $folderPath));

        return property_exists($response, 'metadata') ? $response->metadata->isdeleted : $response;
    }

    /**
     * Delete recursive
     *
     * @param string $folderPath
     *
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function deleteRecursiveFromPath(string $folderPath)
    {
        return $this->request->get("deletefolderrecursive", array("path" => $folderPath));
    }

}
