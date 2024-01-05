<?php

require("check.php");
require_once 'logger.php';

if (isset($is_admin)) {
    $logger->info("ADMIN VALIDATE TEACHER PROCESS: UserID: {$bruker["user_id"]} - {$bruker["name"]} accessed ADMIN validate teacher process.");
    if (isset($_POST['godkjenn'])) {
        $sql = $mysqli->prepare("CALL adminVerifyTeacher(?)");
        $sql->bind_param("i", $_POST['user_id']);
        $sql->execute();
        $result = $sql->get_result();

    } elseif (isset($_POST['slett'])) {
        $sql = $mysqli->prepare("CALL adminDeleteUser(?)");
        $sql->bind_param("i", $_POST['user_id']);
        $sql->execute();
        $result = $sql->get_result();
    }
    header("Location: admin.php");
} else {
    if ($bruker) {
        $logger->warning("ADMIN VALIDATE TEACHER: UserID: {$bruker["user_id"]} {$bruker["name"]} tried to access ADMIN validate teacher process without valid authentication.");
    } else {
        $logger->warning("ADMIN VALIDATE TEACHER: Unauthenticated user tried to access ADMIN validate teacher process without valid authentication.");
    }
    // Nektet tilgang tilsendes rett til index.php
    header("Location: index.php");
}