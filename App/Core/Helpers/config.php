<?php
function config(string $key, mixed $default = null): mixed 
{
    static $configs = [];

    $parts = explode('.', $key);
    $file = array_shift($parts);

    if (!isset($configs[$file])) {
        $path = CONFIG_PATH . "/Services/$file.php";
        if (!file_exists($path)) {
            throw new Exception("Config file not found: $file");
        }
        $configs[$file] = require $path;
    }

    $value = $configs[$file];

    foreach ($parts as $part) {
        if (!isset($value[$part])) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}