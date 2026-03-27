<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Config Validation Rules
    |--------------------------------------------------------------------------
    |
    | Define the config keys and expected types or patterns.
    | You can add any Laravel config keys to validate.
    |
    */

    'rules' => [
        'app.name' => 'string|required',
        'app.env' => 'in:local,production,testing,staging|required',
        'app.debug' => 'boolean|required',
        'app.url' => 'url|required',
        'database.connections.mysql.host' => 'string|required',
        'database.connections.mysql.port' => 'numeric|required',
        'database.connections.mysql.database' => 'string|required',
        'database.connections.mysql.username' => 'string|required',
        'database.connections.mysql.password' => 'string|nullable',
    ],

];