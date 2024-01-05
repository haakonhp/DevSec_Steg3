<?php
require_once 'logger.php';

$name = $_POST["name"];
$message = $_POST["message"];
$priority = filter_input(INPUT_POST, "priority", FILTER_VALIDATE_INT);
$type = filter_input(INPUT_POST, "type", FILTER_VALIDATE_INT);
$terms = filter_input(INPUT_POST,"terms", FILTER_VALIDATE_BOOLEAN);

var_dump($name, $message, $priority, $type, $terms);

if ( ! $terms) {
    die("Please click agree");
}

$host = "localhost";
$dbname = "mydb";
$username = "root";
$password = "";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (mysqli_connect_errno()) {
    die("Connection error: " . mysqli_connect_error());
}

$sql = "INSERT INTO message (name, body, priority, type)
        VALUES (?, ?, ?, ?)";

$stmt = mysqli_stmt_init($conn);

if ( ! mysqli_stmt_prepare($stmt, $sql)) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "ssii",
    $name,
    $message,
    $priority,
    $type);

mysqli_stmt_execute($stmt);

echo "Saved!";






/* VULNERABLE TO SQLI
 $sql = "INSERT INTO message (name, body, priority, type)
        VALUES ('$name', '$message', '$priority', '$type')";
 */





//echo ("Connection established.");




