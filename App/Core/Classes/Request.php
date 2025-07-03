<?php

namespace App\Core\Classes;

class Request
{
    protected array $getParams;
    protected array $postParams;
    protected array $files;
    protected array $headers;
    protected array $server;
    protected array $cookies;
    protected string $uri;
    protected string $method;
    protected ?string $ipAddress = null; // To store resolved IP

    public function __construct()
    {
        // Capture raw input for POST requests (especially useful for JSON payloads)
        $this->postParams = $_POST;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (str_contains($contentType, 'application/json')) {
                $input = file_get_contents('php://input');
                $this->postParams = json_decode($input, true) ?? [];
            }
        }
        
        $this->getParams = $_GET;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
        $this->headers = getallheaders();
        $this->uri = $this->resolveUri();
        $this->method = $this->resolveMethod();
    }

    public function input(string $key, $default = null): mixed
    {
        if (isset($this->postParams[$key])) {
            return $this->postParams[$key];
        }
        if (isset($this->getParams[$key])) {
            return $this->getParams[$key];
        }
        return $default;
    }

    public function query(): array
    {
        return $this->getParams;
    }

    public function post(): array
    {
        return $this->postParams;
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->getParams[$key] ?? $default;
    }

    public function postParam(string $key, $default = null): mixed
    {
        return $this->postParams[$key] ?? $default;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function header(string $key, $default = null): ?string
    {
        // Headers are typically case-insensitive, so convert to consistent format
        $key = str_replace('-', '_', strtoupper($key));
        foreach ($this->headers as $name => $value) {
            if (str_replace('-', '_', strtoupper($name)) === $key) {
                return $value;
            }
        }
        return $default;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function isAjax(): bool
    {
        return (
            !empty($this->server['HTTP_X_REQUESTED_WITH']) &&
            strtolower($this->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        );
    }

    public function ip(): ?string
    {
        if ($this->ipAddress !== null) {
            return $this->ipAddress; 
        }

        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (isset($this->server[$key]) && filter_var($this->server[$key], FILTER_VALIDATE_IP)) {
                $this->ipAddress = $this->server[$key];
                return $this->ipAddress;
            }
            // For HTTP_X_FORWARDED_FOR, it might be a comma-separated list
            if ($key === 'HTTP_X_FORWARDED_FOR' && isset($this->server[$key])) {
                $ips = explode(',', $this->server[$key]);
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        // Take the first valid IP (often the client's original IP)
                        $this->ipAddress = $ip;
                        return $this->ipAddress;
                    }
                }
            }
        }

        // Fallback to REMOTE_ADDR if no others are found or valid
        $this->ipAddress = $this->server['REMOTE_ADDR'] ?? null;
        return $this->ipAddress;
    }

    public function validate(array $rules): array
    {
        $errors = [];
        $dataToValidate = array_merge($this->getParams, $this->postParams); // Combine GET and POST for validation

        foreach ($rules as $field => $fieldRules) {
            if (!is_array($fieldRules)) {
                // If it's a string, split it by '|'
                $fieldRules = explode('|', $fieldRules);
            }

            foreach ($fieldRules as $rule) {
                // Trim the rule to handle potential whitespace
                $rule = trim($rule);
                if (empty($rule)) {
                    continue; // Skip empty rules
                }

                // Split rule into name and potential value (e.g., 'min:3')
                list($ruleName, $ruleValue) = array_pad(explode(':', $rule, 2), 2, null);

                $value = $dataToValidate[$field] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (empty($value) && $value !== 0 && $value !== '0') {
                            $errors[$field][] = "$field is required.";
                        }
                        break;
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "$field must be a valid email address.";
                        }
                        break;
                    case 'string':
                        if (!empty($value) && !is_string($value)) {
                            $errors[$field][] = "$field must be a string.";
                        }
                        break;
                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = "$field must be a number.";
                        }
                        break;
                    case 'min':
                        if (isset($ruleValue)) {
                            $ruleValue = (int)$ruleValue; // Ensure integer comparison
                            if (is_string($value) && strlen($value) < $ruleValue) {
                                $errors[$field][] = "$field must be at least $ruleValue characters long.";
                            } elseif (is_numeric($value) && $value < $ruleValue) {
                                $errors[$field][] = "$field must be at least $ruleValue.";
                            }
                        }
                        break;
                    case 'max':
                        if (isset($ruleValue)) {
                            $ruleValue = (int)$ruleValue; // Ensure integer comparison
                            if (is_string($value) && strlen($value) > $ruleValue) {
                                $errors[$field][] = "$field may not be greater than $ruleValue characters long.";
                            } elseif (is_numeric($value) && $value > $ruleValue) {
                                $errors[$field][] = "$field may not be greater than $ruleValue.";
                            }
                        }
                        break;
                    case 'confirmed': // For password_confirmation
                        $confirmationField = $field . '_confirmation';
                        if ($value !== ($dataToValidate[$confirmationField] ?? null)) {
                            $errors[$field][] = "$field confirmation does not match.";
                        }
                        break;
                    // Add more validation rules as needed (e.g., unique, date, url, ip, regex, etc.)
                    default:
                        // Log unknown validation rule or throw an exception
                        // error_log("Unknown validation rule: $ruleName for field: $field");
                        break;
                }
            }
        }

        return $errors;
    }
    public function userAgent() : string | null
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }
    protected function resolveUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        // Remove query string
        $pos = strpos($uri, '?');
        if ($pos !== false) {
            $uri = substr($uri, 0, $pos);
        }
        return '/' . trim($uri, '/');
    }

    protected function resolveMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
}