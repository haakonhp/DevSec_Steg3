<?php

require("check.php");
require_once 'logger.php';
require_once 'logger.php';

if ($is_admin) {
    $logger->info("ADMIN ACTIONS: {$bruker["user_id"]} - {$bruker["name"]} performed admin actions.");
    $user_id = $_POST['user_id'];
    if (isset($_POST['c_name']) && $_POST['i_name']) {
        $i_name = $_POST['i_name'];

        $sql = $mysqli->prepare("CALL adminChangeName(?,?)");
        $sql->bind_param("is", $user_id, $i_name);
        $sql->execute();
        $result = $sql->get_result();

    } elseif (isset($_POST['c_email']) && ($_POST['i_email'])) {
        $i_email = $_POST['i_email'];

        $sql = $mysqli->prepare("CALL adminChangeEmail(?,?)");
        $sql->bind_param("is", $user_id, $i_email);
        $sql->execute();
        $result = $sql->get_result();
    } elseif (isset($_POST['c_password']) && ($_POST['i_password'])) {
        $i_password = $_POST['i_password'];

        $password_hash = password_hash($i_password, PASSWORD_DEFAULT);
        $sql = $mysqli->prepare("CALL adminChangePassword(?,?)");
        $sql->bind_param("is", $user_id, $password_hash);
        $sql->execute();
        $result = $sql->get_result();
    } elseif (isset($_POST['c_semester']) && ($_POST['i_semester'])) {
        $semester = $_POST['i_semester'];

        $sql = $mysqli->prepare("CALL adminChangeSemester(?,?)");
        $sql->bind_param("ii", $user_id, $semester);
        $sql->execute();
        $result = $sql->get_result();

    } elseif (isset($_POST['c_study_field']) && ($_POST['i_study_field'])) {
        $study_field = $_POST['i_study_field'];
        $sql = $mysqli->prepare("CALL adminChangeField(?,?)");
        $sql->bind_param("ii", $user_id, $study_field);
        $sql->execute();
        $result = $sql->get_result();
    }
    header("Location: admin.php");
} else {
    if ($bruker) {
        $logger->warning("ADMIN ACTIONS: {$bruker["user_id"]} {$bruker["name"]} tried to access admin action process page without valid authentication.");
    } else {
        $logger->warning("ADMIN ACTIONS: Unauthenticated user tried to access admin action process page without valid authentication.");
    }
    // Nektet tilgang tilsendes rett til index.php
    header("Location: index.php");
}