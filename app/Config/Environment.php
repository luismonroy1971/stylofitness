<?php

namespace StyleFitness\Config;

use Dotenv\Dotenv;

class Environment
{
    private static $loaded = false;

    /**
     * Load environment variables
     */
    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        // Load .env file if it exists
        if (file_exists(ROOT_PATH . '/.env')) {
            $dotenv = Dotenv::createImmutable(ROOT_PATH);
            $dotenv->load();
        }

        // Set default values
        self::setDefaults();

        self::$loaded = true;
    }

    /**
     * Set default environment values
     */
    private static function setDefaults(): void
    {
        $defaults = [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => 'http://localhost',
            'APP_NAME' => 'StyloFitness',
            'APP_VERSION' => '1.0.0',
            'DB_HOST' => 'localhost',
            'DB_PORT' => '3306',
            'DB_DATABASE' => 'stylofitness',
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '',
            'DB_CHARSET' => 'utf8mb4',

            'SESSION_LIFETIME' => '120',
            'SESSION_DRIVER' => 'file',
            'MAIL_DRIVER' => 'smtp',
            'MAIL_HOST' => 'localhost',
            'MAIL_PORT' => '587',
            'MAIL_USERNAME' => '',
            'MAIL_PASSWORD' => '',
            'MAIL_ENCRYPTION' => 'tls',
            'UPLOAD_MAX_SIZE' => '10485760', // 10MB
            'UPLOAD_ALLOWED_TYPES' => 'jpg,jpeg,png,gif,pdf,doc,docx',
            'API_RATE_LIMIT' => '100',
            'API_RATE_LIMIT_WINDOW' => '3600',
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }
    }

    /**
     * Get environment variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::load();

        return $_ENV[$key] ?? $default;
    }

    /**
     * Get environment variable as boolean
     *
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
    }

    /**
     * Get environment variable as integer
     *
     * @param string $key
     * @param int $default
     * @return int
     */
    public static function getInt(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    /**
     * Get environment variable as float
     *
     * @param string $key
     * @param float $default
     * @return float
     */
    public static function getFloat(string $key, float $default = 0.0): float
    {
        return (float) self::get($key, $default);
    }

    /**
     * Get environment variable as array
     *
     * @param string $key
     * @param array $default
     * @param string $separator
     * @return array
     */
    public static function getArray(string $key, array $default = [], string $separator = ','): array
    {
        $value = self::get($key);

        if ($value === null) {
            return $default;
        }

        return array_map('trim', explode($separator, $value));
    }

    /**
     * Check if application is in debug mode
     *
     * @return bool
     */
    public static function isDebug(): bool
    {
        return self::getBool('APP_DEBUG');
    }

    /**
     * Check if application is in development environment
     *
     * @return bool
     */
    public static function isDevelopment(): bool
    {
        return self::get('APP_ENV') === 'development';
    }

    /**
     * Check if application is in production environment
     *
     * @return bool
     */
    public static function isProduction(): bool
    {
        return self::get('APP_ENV') === 'production';
    }

    /**
     * Check if application is in testing environment
     *
     * @return bool
     */
    public static function isTesting(): bool
    {
        return self::get('APP_ENV') === 'testing';
    }

    /**
     * Get database configuration
     *
     * @return array
     */
    public static function getDatabaseConfig(): array
    {
        return [
            'host' => self::get('DB_HOST'),
            'port' => self::getInt('DB_PORT'),
            'database' => self::get('DB_DATABASE'),
            'username' => self::get('DB_USERNAME'),
            'password' => self::get('DB_PASSWORD'),
            'charset' => self::get('DB_CHARSET'),
            'options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ];
    }



    /**
     * Get session configuration
     *
     * @return array
     */
    public static function getSessionConfig(): array
    {
        return [
            'lifetime' => self::getInt('SESSION_LIFETIME'),
            'driver' => self::get('SESSION_DRIVER'),
            'path' => ROOT_PATH . '/storage/sessions',
        ];
    }

    /**
     * Validate required environment variables
     *
     * @param array $required
     * @throws \Exception
     */
    public static function validateRequired(array $required): void
    {
        $missing = [];

        foreach ($required as $key) {
            if (empty(self::get($key))) {
                $missing[] = $key;
            }
        }

        if (!empty($missing)) {
            throw new \Exception('Missing required environment variables: ' . implode(', ', $missing));
        }
    }
}
