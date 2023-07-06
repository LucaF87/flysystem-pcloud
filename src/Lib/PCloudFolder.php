<?php

namespace LucaF87\LaravelPCloud\Lib;

use pCloud\Sdk\App;
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

}
