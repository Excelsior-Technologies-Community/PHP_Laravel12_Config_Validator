<?php

return [
    'rules' => [
        'app.name' => 'string|required',
        'app.env' => 'in:local,production,testing,staging|required',
        'app.debug' => 'boolean|required',
        'app.url' => 'url|required',
        'database.connections.mysql.host' => 'string|required',
        'database.connections.mysql.port' => 'numeric|required',
        'database.connections.mysql.database' => 'string|required',
        'database.connections.mysql.username' => 'string|required',
        'database.connections.mysql.password' => 'nullable|string',
        'mail.default' => 'string|required',
        'mail.from.address' => 'email|required',
        'mail.from.name' => 'string|required',
        'mail.mailers.smtp.host' => 'nullable|string',
        'mail.mailers.smtp.port' => 'nullable|numeric',
        'cache.default' => 'string|required',
        'queue.default' => 'string|required',
        'cache.stores.file.driver' => 'string|required',
    ],
    'production_rules' => [
        'app.debug' => 'nullable|in:false,0',
        'app.url' => 'url|required',
        'mail.from.address' => 'email|required',
    ],
];
