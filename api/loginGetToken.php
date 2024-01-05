<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once('../database.php');
    require_once '../logger.php';
    global $logger;
    $mysqli = getConnection(1, 0);
    $sql = $mysqli->prepare("CALL selectUserFromEmail(?)");
    $sql->bind_param("s", $_POST["email"]);
    $sql->execute();
    $bruker = $sql->get_result()->fetch_assoc();
    $mysqli->next_result();
    if ($bruker) {
        if (password_verify($_POST["password"], $bruker["password_hash"])) {
            $sql = $mysqli->prepare("CALL createAuthToken(?)");
            $sql->bind_param("i", $bruker["user_id"]);
            $sql->execute();
            $UUID = $sql->get_result()->fetch_row()[0];
            echo json_encode($UUID);
        } else {
            $logger->info("Invalid password login attempt to email: " . $_POST["email"]);

        }

    } else {
        $logger->info("Invalid email login attempt: " . $_POST["email"]);
    }
}
?>