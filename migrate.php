#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/config/constants.php';

// Load environment
$config = require __DIR__ . '/config/env.php';

// Simple Database class for migrations
class MigrationDB {
    private \PDO $pdo;

    public function __construct(array $config) {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );

        $this->pdo = new \PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );
    }

    public function exec(string $sql): void {
        $this->pdo->exec($sql);
    }

    public function query(string $sql) {
        return $this->pdo->query($sql);
    }

    public function prepare(string $sql) {
        return $this->pdo->prepare($sql);
    }
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║        AI Agent Platform - Database Migration Tool         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

try {
    // Connect to database
    echo "📦 Connecting to database...\n";
    $db = new MigrationDB($config['db']);
    echo "✓ Connected successfully\n\n";

    // Get command
    $command = $argv[1] ?? 'run';
    $steps = isset($argv[2]) ? (int)$argv[2] : 1;

    // Autoload Database Migrator
    spl_autoload_register(function ($class) {
        $prefix = 'Database\\';
        $baseDir = __DIR__ . '/database/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });

    $migrator = new Database\Migrator($db->pdo, __DIR__ . '/database/migrations');

    if ($command === 'run') {
        echo "🚀 Running migrations...\n";
        $migrator->run();
        echo "\n✅ All migrations completed!\n";
    } elseif ($command === 'rollback') {
        echo "⏮️  Rolling back migrations...\n";
        $migrator->rollback($steps);
        echo "\n✅ Rollback completed!\n";
    } else {
        echo "❌ Unknown command: $command\n";
        echo "Usage:\n";
        echo "  php migrate.php run          - Run all pending migrations\n";
        echo "  php migrate.php rollback [n] - Rollback last n migrations\n";
        exit(1);
    }

    echo "\n";

} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
    exit(1);
}
