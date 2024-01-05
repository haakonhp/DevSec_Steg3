<?php
require_once("database.php");
require_once("functions.php");
session_start();

if (isset($_SESSION["s_bruker_id"])) {
    $mysqli = getConnection(0,0);
    $sql = $mysqli->prepare("CALL getUserFromID(?)");
    $sql->bind_param("i", $_SESSION["s_bruker_id"]);
    $sql->execute();
    $bruker = $sql->get_result()->fetch_assoc();
    $mysqli->next_result();

    $adminRoleID = 4;
    $sql = $mysqli->prepare("CALL doesUserHaveRoleQuery(?, ?)");
    $sql->bind_param("ii", $_SESSION["s_bruker_id"], $adminRoleID);
    $sql->execute();
    $is_admin = ($sql->get_result()->fetch_row()[0] == 1);

    $mysqli->next_result();
}
elseif (isset($_SESSION["s_pin_code"])) {
    $mysqli = getConnection(0,1);

    $sql = $mysqli->prepare("CALL getSubjectWithPIN(?,?)");
    $sql->bind_param("ii", $_SESSION["s_pin_code"], $_SESSION["s_subject_id"]);
    $sql->execute();
    $emne = $sql->get_result()->fetch_assoc()["subject_id"];
    $mysqli->next_result();
}

$ip = getIPAddress();