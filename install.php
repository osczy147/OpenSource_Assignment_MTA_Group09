<?php
/**
 * install.php - Run this once to create the database and tables
 * Access via browser: http://localhost/portfolio_system/install.php
 */

$host = 'localhost';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>");
}

$sql_db = "CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8 COLLATE utf8_general_ci";
$conn->query($sql_db);
$conn->select_db('portfolio_db');

function ensureColumn($conn, $table, $columnSql, $afterColumn = null) {
    $table = $conn->real_escape_string($table);
    $columnName = preg_replace('/\s.*$/', '', trim($columnSql));
    $exists = $conn->query("SHOW COLUMNS FROM `$table` LIKE '" . $conn->real_escape_string($columnName) . "'");
    if ($exists && $exists->num_rows === 0) {
        $afterClause = $afterColumn ? " AFTER `$afterColumn`" : '';
        $conn->query("ALTER TABLE `$table` ADD COLUMN $columnSql$afterClause");
    }
}

function ensureTable($conn, $tableSql) {
    $conn->query($tableSql);
}

// Users table (User Management Module - mandatory)
ensureTable($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','artist') DEFAULT 'artist',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

ensureColumn($conn, 'users', 'full_name VARCHAR(100) NOT NULL DEFAULT ""', 'id');
ensureColumn($conn, 'users', 'username VARCHAR(50) NOT NULL UNIQUE', 'full_name');
ensureColumn($conn, 'users', 'email VARCHAR(100) NOT NULL UNIQUE', 'username');
ensureColumn($conn, 'users', 'password VARCHAR(255) NOT NULL', 'email');
ensureColumn($conn, 'users', 'role ENUM("admin","artist") DEFAULT "artist"', 'password');
ensureColumn($conn, 'users', 'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP', 'role');

$conn->query("UPDATE users SET full_name = CASE WHEN full_name IS NULL OR full_name = '' THEN username ELSE full_name END");

// Portfolio entries table
ensureTable($conn, "CREATE TABLE IF NOT EXISTS portfolios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    category ENUM('2D Animation','3D Animation','Illustration','Motion Graphics','Photography','Video','Graphic Design','Other') NOT NULL,
    description TEXT,
    tools_used VARCHAR(255),
    image_filename VARCHAR(255),
    project_year YEAR,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

ensureColumn($conn, 'portfolios', 'user_id INT NOT NULL', 'id');
ensureColumn($conn, 'portfolios', 'title VARCHAR(150) NOT NULL', 'user_id');
ensureColumn($conn, 'portfolios', 'category ENUM("2D Animation","3D Animation","Illustration","Motion Graphics","Photography","Video","Graphic Design","Other") NOT NULL', 'title');
ensureColumn($conn, 'portfolios', 'description TEXT', 'category');
ensureColumn($conn, 'portfolios', 'tools_used VARCHAR(255)', 'description');
ensureColumn($conn, 'portfolios', 'image_filename VARCHAR(255)', 'tools_used');
ensureColumn($conn, 'portfolios', 'project_year YEAR', 'image_filename');
ensureColumn($conn, 'portfolios', 'is_featured TINYINT(1) DEFAULT 0', 'project_year');
ensureColumn($conn, 'portfolios', 'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP', 'is_featured');

// Insert default admin user (password: admin123)
$hashed = password_hash('admin123', PASSWORD_DEFAULT);
$conn->query("INSERT IGNORE INTO users (full_name, username, email, password, role)
              VALUES ('Administrator', 'admin', 'admin@portfolio.ac.tz', '$hashed', 'admin')");

echo "<div style='font-family:sans-serif;padding:30px;background:#0f0f0f;color:#e0e0e0;min-height:100vh'>
        <h2 style='color:#c084fc'>✅ Installation Complete!</h2>
        <p>Database <strong>portfolio_db</strong> and tables created successfully.</p>
        <p><strong>Default Admin Login:</strong><br>
           Username: <code>admin</code><br>
           Password: <code>admin123</code></p>
        <p><a href='index.php' style='color:#c084fc'>→ Go to Homepage</a></p>
        <p style='color:#888;font-size:0.85em'>Delete this file after installation for security.</p>
      </div>";

$conn->close();
?>
