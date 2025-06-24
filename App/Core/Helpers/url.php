<?php
function base_url(string $path = ''): string 
{
    $base = rtrim(getenv('APP_URL') ?: 'http://localhost', '/');
    $path = ltrim($path, '/');
    return "$base/$path";
}

function redirect(string $to): void 
{
    header("Location: " . base_url($to));
    exit;
}

function current_url(): string 
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function query_param(string $key, mixed $default = null): mixed 
{
    return $_GET[$key] ?? $default;
}