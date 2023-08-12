<?php

return [
    'access_token'  => env('PCLOUD_ACCESS_TOKEN',''),
    'location_id'   => env('PCLOUD_LOCATION_ID',''),
    'client_id'     => env('PCLOUD_CLIENT_ID',''),
    'client_secret' => env('PCLOUD_CLIENT_SECRET',''),
    'local_files_keep_alive' => env('PCLOUD_LOCAL_FILE_KEEP_ALIVE',60),

    'clean_excluded' => [
        'public/',
        '.gitignore',
        //file or path to exclude from local deletion
    ]
];