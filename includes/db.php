<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio_db');

function columnExists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

function applicationSchemaReady($conn) {
    return columnExists($conn, 'users', 'full_name') &&
           columnExists($conn, 'users', 'username') &&
           columnExists($conn, 'users', 'email') &&
           columnExists($conn, 'users', 'password') &&
           columnExists($conn, 'users', 'role') &&
           columnExists($conn, 'portfolios', 'user_id') &&
           columnExists($conn, 'portfolios', 'title');
}

function redirectToSetup() {
    if (!headers_sent()) {
        header('Location: setup.php');
        exit;
    }

    die('The database is not set up yet. Please open setup.php.');
}

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
    }
    $conn->set_charset("utf8");
    if (!applicationSchemaReady($conn)) {
        redirectToSetup();
    }
    return $conn;
}
?>
