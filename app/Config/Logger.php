<?php

namespace StyleFitness\Config;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\WebProcessor;

class Logger
{
    private static $loggers = [];

    /**
     * Get logger instance
     *
     * @param string $channel
     * @return MonologLogger
     */
    public static function getLogger(string $channel = 'app'): MonologLogger
    {
        if (!isset(self::$loggers[$channel])) {
            self::$loggers[$channel] = self::createLogger($channel);
        }

        return self::$loggers[$channel];
    }

    /**
     * Create logger instance
     *
     * @param string $channel
     * @return MonologLogger
     */
    private static function createLogger(string $channel): MonologLogger
    {
        $logger = new MonologLogger($channel);

        // Add handlers based on environment
        if (self::isDevelopment()) {
            self::addDevelopmentHandlers($logger, $channel);
        } else {
            self::addProductionHandlers($logger, $channel);
        }

        // Add processors
        $logger->pushProcessor(new IntrospectionProcessor());
        $logger->pushProcessor(new WebProcessor());
        $logger->pushProcessor(new MemoryUsageProcessor());

        return $logger;
    }

    /**
     * Add development handlers
     *
     * @param MonologLogger $logger
     * @param string $channel
     */
    private static function addDevelopmentHandlers(MonologLogger $logger, string $channel): void
    {
        // Console handler for development
        $consoleHandler = new StreamHandler('php://stdout', MonologLogger::DEBUG);
        $consoleHandler->setFormatter(self::getConsoleFormatter());
        $logger->pushHandler($consoleHandler);

        // File handler for all logs
        $fileHandler = new StreamHandler(self::getLogPath($channel . '.log'), MonologLogger::DEBUG);
        $fileHandler->setFormatter(self::getFileFormatter());
        $logger->pushHandler($fileHandler);
    }

    /**
     * Add production handlers
     *
     * @param MonologLogger $logger
     * @param string $channel
     */
    private static function addProductionHandlers(MonologLogger $logger, string $channel): void
    {
        // Rotating file handler for production
        $rotatingHandler = new RotatingFileHandler(
            self::getLogPath($channel . '.log'),
            30, // Keep 30 days of logs
            MonologLogger::INFO
        );
        $rotatingHandler->setFormatter(self::getFileFormatter());
        $logger->pushHandler($rotatingHandler);

        // Error log handler
        $errorHandler = new RotatingFileHandler(
            self::getLogPath('errors.log'),
            30,
            MonologLogger::ERROR
        );
        $errorHandler->setFormatter(self::getFileFormatter());
        $logger->pushHandler($errorHandler);
    }

    /**
     * Get console formatter
     *
     * @return LineFormatter
     */
    private static function getConsoleFormatter(): LineFormatter
    {
        return new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s',
            true,
            true
        );
    }

    /**
     * Get file formatter
     *
     * @return LineFormatter
     */
    private static function getFileFormatter(): LineFormatter
    {
        return new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s',
            true,
            true
        );
    }

    /**
     * Get log file path
     *
     * @param string $filename
     * @return string
     */
    private static function getLogPath(string $filename): string
    {
        $logDir = ROOT_PATH . '/storage/logs';

        // Create logs directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        return $logDir . '/' . $filename;
    }

    /**
     * Check if in development environment
     *
     * @return bool
     */
    private static function isDevelopment(): bool
    {
        return ($_ENV['APP_ENV'] ?? 'production') === 'development';
    }

    /**
     * Log application error
     *
     * @param string $message
     * @param array $context
     */
    public static function logError(string $message, array $context = []): void
    {
        self::getLogger('error')->error($message, $context);
    }

    /**
     * Log application warning
     *
     * @param string $message
     * @param array $context
     */
    public static function logWarning(string $message, array $context = []): void
    {
        self::getLogger('app')->warning($message, $context);
    }

    /**
     * Log application info
     *
     * @param string $message
     * @param array $context
     */
    public static function logInfo(string $message, array $context = []): void
    {
        self::getLogger('app')->info($message, $context);
    }

    /**
     * Log database queries
     *
     * @param string $query
     * @param array $params
     * @param float $executionTime
     */
    public static function logQuery(string $query, array $params = [], float $executionTime = 0): void
    {
        self::getLogger('database')->debug('Query executed', [
            'query' => $query,
            'params' => $params,
            'execution_time' => $executionTime . 'ms',
        ]);
    }

    /**
     * Log user activity
     *
     * @param string $action
     * @param int|null $userId
     * @param array $context
     */
    public static function logUserActivity(string $action, ?int $userId = null, array $context = []): void
    {
        self::getLogger('user_activity')->info($action, array_merge([
            'user_id' => $userId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        ], $context));
    }
}
