<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database connection for tests
    |--------------------------------------------------------------------------
    |
    | Name at database connection for models in test mode and maintenance
    |
     */

    'database' => [
        'connections' => [
            'test'    => env('API_TESTING_DB_CONNECTION_TESTS', 'mysql_tests'),
            'default' => env('API_TESTING_DB_CONNECTION_DEFAULT', 'mysql')
        ],
    ]
];