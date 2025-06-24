<?php
namespace App\Core;
use App\Core\Classes\DotEnv;

require_once __DIR__ . '/Core/Classes/DotEnv.php';
require_once __DIR__ . '/../Config/constants.php';
//echo __DIR__ . '/Core/Helpers/';
foreach (glob(__DIR__ . '/Core/Helpers/*.php') as $file) {

    require_once $file;
}

$env = new DotEnv(__DIR__ . '/../.env');
$env->load();



