<?php

declare(strict_types=1);

namespace Database;

class Migrator
{
    private $pdo;
    private string $migrationsPath;

    public function __construct($pdo, string $migrationsPath)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = $migrationsPath;
        $this->ensureMigrationsTable();
    }

    private function ensureMigrationsTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function run(): void
    {
        $migrations = $this->getMigrationFiles();
        $executedMigrations = $this->getExecutedMigrations();

        foreach ($migrations as $migration) {
            $migrationName = basename($migration, '.php');

            if (in_array($migrationName, $executedMigrations)) {
                echo "⊘ Skipping $migrationName (already executed)\n";
                continue;
            }

            try {
                $this->executeMigration($migration, 'up');
                $this->recordMigration($migrationName);
                echo "✓ Successfully executed $migrationName\n";
            } catch (\Exception $e) {
                echo "✗ Failed to execute $migrationName: " . $e->getMessage() . "\n";
                throw $e;
            }
        }
    }

    public function rollback(int $steps = 1): void
    {
        $migrations = array_reverse($this->getMigrationFiles());
        $executedMigrations = $this->getExecutedMigrations();

        $rolledBack = 0;
        foreach ($migrations as $migration) {
            if ($rolledBack >= $steps) {
                break;
            }

            $migrationName = basename($migration, '.php');

            if (!in_array($migrationName, $executedMigrations)) {
                continue;
            }

            try {
                $this->executeMigration($migration, 'down');
                $this->removeMigration($migrationName);
                echo "✓ Rolled back $migrationName\n";
                $rolledBack++;
            } catch (\Exception $e) {
                echo "✗ Failed to rollback $migrationName: " . $e->getMessage() . "\n";
                throw $e;
            }
        }
    }

    private function getMigrationFiles(): array
    {
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);
        return $files ?: [];
    }

    private function getExecutedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM migrations ORDER BY executed_at ASC");
        $migrations = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $migrations[] = $row['migration'];
        }
        return $migrations;
    }

    private function executeMigration(string $file, string $direction): void
    {
        $migration = require $file;

        if (!isset($migration[$direction]) || !is_callable($migration[$direction])) {
            throw new \Exception("Migration must have a callable '$direction' method");
        }

        $migration[$direction]($this->pdo);
    }

    private function recordMigration(string $migrationName): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->execute([$migrationName]);
    }

    private function removeMigration(string $migrationName): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE migration = ?");
        $stmt->execute([$migrationName]);
    }
}
