S<?php

$mysqli = require __DIR__ . "/database.php";

$sql = $mysqli->prepare("CALL selectUserFromEmail(?)");
$sql->bind_param("s", $mysqli->real_escape_string($_GET["email"]));
$sql->execute();

$is_available = $sql->get_result()->num_rows === 0;

header("Content-Type: application/json");

echo json_encode(["available" => $is_available]);