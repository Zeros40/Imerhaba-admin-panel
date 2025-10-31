<?php
// Database setup script
// Run this file once to initialize the database

require_once __DIR__ . '/config.php';

try {
    $pdo = db();

    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');

    // Split by semicolons and execute each statement
    $statements = array_filter(
        array_map('trim', explode(';', $schema)),
        fn($stmt) => !empty($stmt) && !preg_match('/^\s*--/', $stmt)
    );

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }

    echo "Database setup completed successfully!\n";
    echo "Default admin credentials:\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "\nPlease change the password after first login!\n";

} catch (PDOException $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
