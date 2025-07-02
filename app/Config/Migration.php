<?php

namespace StyleFitness\Config;

use StyleFitness\Config\Database;
use StyleFitness\Config\Logger;
use PDO;
use PDOException;
use Exception;

class Migration
{
    private $pdo;
    private $logger;
    private $migrationsTable = 'migrations';
    private $migrationsPath;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->logger = Logger::getLogger('migration');
        $this->migrationsPath = ROOT_PATH . '/database/migrations';

        $this->createMigrationsTable();
    }

    /**
     * Create migrations table if it doesn't exist
     */
    private function createMigrationsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_migration (migration)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        try {
            $this->pdo->exec($sql);
            $this->logger->info('Migrations table created or verified');
        } catch (PDOException $e) {
            $this->logger->error('Failed to create migrations table', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Run pending migrations
     *
     * @return array
     */
    public function migrate(): array
    {
        $pendingMigrations = $this->getPendingMigrations();
        $executed = [];

        if (empty($pendingMigrations)) {
            $this->logger->info('No pending migrations found');
            return [];
        }

        $batch = $this->getNextBatchNumber();

        foreach ($pendingMigrations as $migration) {
            try {
                $this->executeMigration($migration, $batch);
                $executed[] = $migration;
                $this->logger->info("Migration executed: {$migration}");
            } catch (Exception $e) {
                $this->logger->error("Migration failed: {$migration}", ['error' => $e->getMessage()]);
                throw $e;
            }
        }

        return $executed;
    }

    /**
     * Rollback last batch of migrations
     *
     * @return array
     */
    public function rollback(): array
    {
        $lastBatch = $this->getLastBatchNumber();

        if ($lastBatch === 0) {
            $this->logger->info('No migrations to rollback');
            return [];
        }

        $migrations = $this->getMigrationsByBatch($lastBatch);
        $rolledBack = [];

        // Rollback in reverse order
        foreach (array_reverse($migrations) as $migration) {
            try {
                $this->rollbackMigration($migration);
                $rolledBack[] = $migration;
                $this->logger->info("Migration rolled back: {$migration}");
            } catch (Exception $e) {
                $this->logger->error("Rollback failed: {$migration}", ['error' => $e->getMessage()]);
                throw $e;
            }
        }

        return $rolledBack;
    }

    /**
     * Get pending migrations
     *
     * @return array
     */
    private function getPendingMigrations(): array
    {
        $allMigrations = $this->getAllMigrationFiles();
        $executedMigrations = $this->getExecutedMigrations();

        return array_diff($allMigrations, $executedMigrations);
    }

    /**
     * Get all migration files
     *
     * @return array
     */
    private function getAllMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
            return [];
        }

        $files = glob($this->migrationsPath . '/*.php');
        $migrations = [];

        foreach ($files as $file) {
            $migrations[] = basename($file, '.php');
        }

        sort($migrations);
        return $migrations;
    }

    /**
     * Get executed migrations
     *
     * @return array
     */
    private function getExecutedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM {$this->migrationsTable} ORDER BY migration");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Execute a migration
     *
     * @param string $migration
     * @param int $batch
     */
    private function executeMigration(string $migration, int $batch): void
    {
        $migrationFile = $this->migrationsPath . '/' . $migration . '.php';

        if (!file_exists($migrationFile)) {
            throw new \Exception("Migration file not found: {$migrationFile}");
        }

        // Include migration file
        $migrationClass = include $migrationFile;

        if (!is_object($migrationClass) || !method_exists($migrationClass, 'up')) {
            throw new \Exception("Invalid migration file: {$migration}");
        }

        // Begin transaction
        $this->pdo->beginTransaction();

        try {
            // Execute migration
            $migrationClass->up($this->pdo);

            // Record migration
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)"
            );
            $stmt->execute([$migration, $batch]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Rollback a migration
     *
     * @param string $migration
     */
    private function rollbackMigration(string $migration): void
    {
        $migrationFile = $this->migrationsPath . '/' . $migration . '.php';

        if (!file_exists($migrationFile)) {
            throw new \Exception("Migration file not found: {$migrationFile}");
        }

        // Include migration file
        $migrationClass = include $migrationFile;

        if (!is_object($migrationClass) || !method_exists($migrationClass, 'down')) {
            throw new \Exception("Invalid migration file or missing down method: {$migration}");
        }

        // Begin transaction
        $this->pdo->beginTransaction();

        try {
            // Execute rollback
            $migrationClass->down($this->pdo);

            // Remove migration record
            $stmt = $this->pdo->prepare(
                "DELETE FROM {$this->migrationsTable} WHERE migration = ?"
            );
            $stmt->execute([$migration]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Get next batch number
     *
     * @return int
     */
    private function getNextBatchNumber(): int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM {$this->migrationsTable}");
        $lastBatch = $stmt->fetchColumn();

        return ($lastBatch ?: 0) + 1;
    }

    /**
     * Get last batch number
     *
     * @return int
     */
    private function getLastBatchNumber(): int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM {$this->migrationsTable}");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get migrations by batch
     *
     * @param int $batch
     * @return array
     */
    private function getMigrationsByBatch(int $batch): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT migration FROM {$this->migrationsTable} WHERE batch = ? ORDER BY migration"
        );
        $stmt->execute([$batch]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Create a new migration file
     *
     * @param string $name
     * @return string
     */
    public function createMigration(string $name): string
    {
        $timestamp = date('Y_m_d_His');
        $className = $this->studlyCase($name);
        $filename = "{$timestamp}_{$name}.php";
        $filepath = $this->migrationsPath . '/' . $filename;

        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }

        $template = $this->getMigrationTemplate($className);

        file_put_contents($filepath, $template);

        $this->logger->info("Migration created: {$filename}");

        return $filename;
    }

    /**
     * Get migration template
     *
     * @param string $className
     * @return string
     */
    private function getMigrationTemplate(string $className): string
    {
        return "<?php

use StyleFitness\Config\Migration;

return new class {
    /**
     * Run the migration
     *
     * @param PDO \$pdo
     */
    public function up(PDO \$pdo): void
    {
        // Add your migration logic here
        \$sql = \"\";
        \$pdo->exec(\$sql);
    }

    /**
     * Reverse the migration
     *
     * @param PDO \$pdo
     */
    public function down(PDO \$pdo): void
    {
        // Add your rollback logic here
        \$sql = \"\";
        \$pdo->exec(\$sql);
    }
};
";
    }

    /**
     * Convert string to StudlyCase
     *
     * @param string $value
     * @return string
     */
    private function studlyCase(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $value)));
    }

    /**
     * Get migration status
     *
     * @return array
     */
    public function status(): array
    {
        $allMigrations = $this->getAllMigrationFiles();
        $executedMigrations = $this->getExecutedMigrations();
        $pendingMigrations = array_diff($allMigrations, $executedMigrations);

        return [
            'total' => count($allMigrations),
            'executed' => count($executedMigrations),
            'pending' => count($pendingMigrations),
            'pending_list' => $pendingMigrations,
        ];
    }
}
