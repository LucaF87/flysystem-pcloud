<?php

namespace LucaF87\PCloudAdapter\Facades;

use Illuminate\Support\Facades\Facade;

class PCloudFolder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pcloudfolder';
    }
}