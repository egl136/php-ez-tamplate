<?php
namespace App\Core\Classes;

class DotEnv {
    private string $path;

    public function __construct(string $filePath) {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("Env file not found: $filePath");
        }
        $this->path = $filePath;
    }

    public function load(): void {
        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignore comments
            if (str_starts_with(trim($line), '#')) continue;

            // Parse KEY=VALUE
            [$key, $value] = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            $value = trim($value, '"\'');

            // Set in environment
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
