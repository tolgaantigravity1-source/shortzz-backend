<?php
// Import SQL file to TiDB database
// This script is run during container startup

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

if (!$host || !$dbname || !$username || !$password) {
    echo "Database environment variables not set, skipping import.\n";
    exit(0);
}

echo "Connecting to TiDB: $host:$port/$dbname\n";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "Connected successfully!\n";
    
    // Check if tables already exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "Tables already exist (" . count($tables) . " tables). Skipping import.\n";
        exit(0);
    }
    
    echo "No tables found. Importing SQL file...\n";
    
    $sqlFile = '/tmp/shortzz_database.sql';
    if (!file_exists($sqlFile)) {
        echo "SQL file not found at $sqlFile\n";
        exit(1);
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Remove problematic lines for TiDB
    $sql = preg_replace('/SET SQL_MODE.*?;/', '', $sql);
    $sql = preg_replace('/START TRANSACTION;/', '', $sql);
    $sql = preg_replace('/COMMIT;/', '', $sql);
    $sql = preg_replace('/\/\*!40101.*?\*\//', '', $sql);
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $imported = 0;
    foreach ($statements as $statement) {
        if (empty($statement) || preg_match('/^--/', $statement) || preg_match('/^\/\*/', $statement)) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $imported++;
        } catch (PDOException $e) {
            // Skip errors for duplicate tables etc
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "Warning: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "Import completed! Executed $imported statements.\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "Continuing anyway...\n";
}
