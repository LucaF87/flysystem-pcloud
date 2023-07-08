<?php

namespace LucaF87\PCloudAdapter\Console;

use Illuminate\Console\Command;
use pCloud\Sdk\App;

class CreateAuthorisationTokenCommand extends Command
{

    protected $signature = 'flysystem-pcloud:token';

    protected $description = 'Create the Authorization token used to generate the access token';

    protected $pCloudApp;

    public function __construct()
    {
        parent::__construct();

        $this->pCloudApp = new App();
        $this->pCloudApp->setAppKey(config('flysystem-pcloud.client_id') ?? '');
        $this->pCloudApp->setAppSecret(config('flysystem-pcloud.client_secret') ?? '');
    }

    public function handle()
    {
        $codeUrl = $this->pCloudApp->getAuthorizeCodeUrl();
        $this->newLine();
        $this->newLine();
        $this->info('Click this link! ' . $codeUrl);

        $accessCode = $this->ask('What is the access code?');
        $hostName = $this->anticipate('What is the Hostname?', ['eapi.pcloud.com', 'api.pcloud.com']);

        $locationId = 1;
        if ($hostName == 'eapi.pcloud.com') {
            $locationId = 2;
        }

        $accessDetails = $this->pCloudApp->getTokenFromCode($accessCode,$locationId);
        $this->info('Copy this to the PCLOUD_ACCESS_TOKEN env! ' . $accessDetails['access_token']);
        $this->info('Copy this to the PCLOUD_LOCATION_ID env! ' . $accessDetails['locationid']);
    }
}
