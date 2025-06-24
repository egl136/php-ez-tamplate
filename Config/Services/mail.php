<?php
return [
    'driver' => 'smtp',
    'host' => getenv('MAIL_HOST'),
    'port' => 587,
    'username' => getenv('MAIL_USER'),
    'password' => getenv('MAIL_PASS'),
];
