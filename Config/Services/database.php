<?php
return [
    'driver'  => getenv('DB_MANAGER'), 
    'host'    => getenv('DB_HOST'),
    'name'    => getenv('DB_NAME'),
    'user'    => getenv('DB_USERNAME'),
    'pass'    => getenv('DB_PASSWORD'),
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    'port'    => getenv('DB_PORT') ?: 3306,
];