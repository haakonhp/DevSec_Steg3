<?php
require_once('database.php');
require("verifySubject.php");
require("check.php");
require_once 'logger.php';

$bruker_id = $bruker["id"];

$mysqli = getConnection(0,0);

if($is_admin && isset($_POST['delete-comment'])){
    $sql = $mysqli->prepare("CALL deleteCommentWithId(?)");
    $sql->bind_param("i", $_POST['comment_id']);
    $sql->execute();
    $logger->info("ADMIN USER: $bruker_id - Deleted a comment.");
    header("Location: admin.php?room={$_POST['roomRedirect']}");
}

if($is_admin && isset($_POST['delete-report'])){
    $sql = $mysqli->prepare("CALL deleteReportWithId(?)");
    $sql->bind_param("i", $_POST['report_id']);
    $sql->execute();
    $logger->info("ADMIN USER: $bruker_id -Deleted a report.");
    header("Location: admin.php?room={$_POST['roomRedirect']}");
}