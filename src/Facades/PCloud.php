<?php

namespace LucaF87\PCloudAdapter\Facades;

use Illuminate\Support\Facades\Facade;

class PCloud extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pcloud';
    }
}