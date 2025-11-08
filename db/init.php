<?php
/**
 * Database Initialization Script
 * Run this once to set up all tables
 */

require_once __DIR__ . '/config.php';

try {
    $db = db();

    echo "🚀 Initializing Imerhaba Design Studio Database...\n\n";

    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $statements = explode(';', $schema);

    $count = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;

        try {
            $db->exec($statement);
            $count++;
        } catch (PDOException $e) {
            echo "⚠️  Warning: " . $e->getMessage() . "\n";
        }
    }

    echo "✅ Executed $count SQL statements\n\n";

    // Verify tables
    $tables = ['designs', 'templates', 'campaigns', 'landing_pages', 'settings', 'analytics'];
    echo "📊 Verifying tables...\n";

    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'")->fetch();
        if ($result) {
            echo "  ✓ $table\n";
        } else {
            echo "  ✗ $table (MISSING!)\n";
        }
    }

    echo "\n✨ Database initialization complete!\n";
    echo "\n📝 Next steps:\n";
    echo "  1. Visit dashboard.php to start using the Design Studio\n";
    echo "  2. Configure API keys in settings.php for AI image generation\n";
    echo "  3. Start creating designs!\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
