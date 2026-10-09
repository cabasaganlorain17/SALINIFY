<?php

$host = "YOUR_AIVEN_HOST";
$port = "YOUR_AIVEN_PORT";
$username = "YOUR_AIVEN_USERNAME";
$password = "YOUR_AIVEN_PASSWORD";
$database = "YOUR_DATABASE_NAME";

try {
    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$database;sslmode=require",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Unable to connect to the database.");
}
?>
