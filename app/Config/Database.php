<?php

namespace StyleFitness\Config;

use PDO;
use PDOException;
use Exception;

/**
 * Configuración de Base de Datos - STYLOFITNESS
 * Versión optimizada y corregida
 */

class Database
{
    private static $instance = null;
    private $connection;

    // Configuración de base de datos
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    private $charset = 'utf8mb4';

    private function __construct()
    {
        // Cargar variables de entorno si no están cargadas
        $this->loadEnvironmentVariables();

        // Leer configuración desde variables de entorno o usar valores por defecto
        $this->host = $this->getEnvVar('DB_HOST', 'localhost');
        $this->port = $this->getEnvVar('DB_PORT', '3306');
        $this->dbname = $this->getEnvVar('DB_DATABASE', 'stylofitness_gym');
        $this->username = $this->getEnvVar('DB_USERNAME', 'root');
        $this->password = $this->getEnvVar('DB_PASSWORD', '');

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE {$this->charset}_unicode_ci",
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $e) {
            // Log el error
            error_log('Database connection error: ' . $e->getMessage());
            throw new Exception('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }

    /**
     * Cargar variables de entorno desde el archivo .env
     */
    private function loadEnvironmentVariables()
    {
        $envFile = dirname(__DIR__, 2) . '/.env';

        // Si ya están cargadas las variables principales, no cargar de nuevo
        if (isset($_ENV['DB_HOST']) || isset($_SERVER['DB_HOST'])) {
            return;
        }

        if (!file_exists($envFile)) {
            return;
        }

        try {
            $content = file_get_contents($envFile);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                $line = trim($line);

                // Saltar comentarios y líneas vacías
                if (empty($line) || $line[0] === '#') {
                    continue;
                }

                // Parsear línea key=value
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Remover comillas si existen
                    if (strlen($value) > 1 &&
                        (($value[0] === '"' && $value[-1] === '"') ||
                         ($value[0] === "'" && $value[-1] === "'"))) {
                        $value = substr($value, 1, -1);
                    }

                    // Solo establecer si no existe ya
                    if (!isset($_ENV[$key])) {
                        $_ENV[$key] = $value;
                        $_SERVER[$key] = $value;
                        putenv("$key=$value");
                    }
                }
            }
        } catch (Exception $e) {
            error_log('Error cargando archivo .env: ' . $e->getMessage());
        }
    }

    /**
     * Obtener variable de entorno con valor por defecto
     */
    private function getEnvVar($key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?? $default;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    // Método para ejecutar consultas preparadas
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Error en consulta: ' . $e->getMessage());
        }
    }

    // Método para obtener un registro
    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    // Método para obtener todos los registros
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    // Método para insertar y retornar el ID
    public function insert($sql, $params = [])
    {
        $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }

    // Método para contar registros
    public function count($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }

    // Método para verificar conexión
    public function isConnected()
    {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Método para obtener información de la base de datos
    public function getDatabaseInfo()
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->dbname,
            'username' => $this->username,
            'charset' => $this->charset,
            'connected' => $this->isConnected(),
        ];
    }
}
