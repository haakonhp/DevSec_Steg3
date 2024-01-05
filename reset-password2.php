<?php
include 'inc/header.php';
include_once ('database.php');
require_once 'logger.php';

$mysqli = getConnection(0,0);

if (isset($_POST["reset-password-submit"])) {
    $selector = mysqli_real_escape_string($mysqli, $_POST["selector"]);
    $validator = mysqli_real_escape_string($mysqli, $_POST["validator"]);
    $pwd = mysqli_real_escape_string($mysqli, $_POST["pwd"]);
    $passwordRepeat = mysqli_real_escape_string($mysqli, $_POST["pwd-repeat"]);

    //baaaaaaaack
    if (empty($pwd) || empty($passwordRepeat)) {
        header("reset-password.php");
        exit();
        // if password not empty
    } elseif ($pwd != $passwordRepeat) {
        header("reset-password.php");
        exit();
    }
}

//t checking tokens
// check selector sendt av forrige form. Check db for selector

$sql = "CALL getValidPWDToken(?)";
$stmt = mysqli_stmt_init($mysqli);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo("Error!");
} else {
    mysqli_stmt_bind_param($stmt, "s", $selector);
    mysqli_stmt_execute($stmt);
    //converting back token from hexa to binary
    $result = mysqli_stmt_get_result($stmt);
    //cant get r put in associative array
    $row = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
    //convert valid token to binary from hexa
    $tokenBin = hex2bin($validator);
    //matching and varify binary token with token from  row in db.
    $tokenCheck = password_verify($tokenBin, $row["pwdResetToken"]);
    //if check fails
    if ($tokenCheck = false) {
        echo "Token check failed. ";
        exit();

    } elseif ($tokenCheck = true) {
        //grab email from db column and make changes to user table.
        $tokenEmail = $row['pwdResetEmail'];
        //select
        $sql = "CALL selectUserFromEmail(?)";
        $stmt = mysqli_stmt_init($mysqli);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (!$row = mysqli_fetch_assoc($result)) {
                echo "Error no user";
                exit();
            } else {
                //updating password in user table
                $sql = "CALL updateUserSetPasswordFromEmail(?,?)";
                $stmt = mysqli_stmt_init($mysqli);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo "Error 2";
                    exit();
                } else {
                    $newPwdHash = password_hash($pwd, PASSWORD_DEFAULT);
                    mysqli_stmt_bind_param($stmt, "ss", $newPwdHash, $tokenEmail);
                    mysqli_stmt_execute($stmt);
                    $logger->info("PASSWORD RESET: User with email $tokenEmail successfully changed their password.");
                    $sql = "CALL deletePWDToken(?)";
                    $stmt = mysqli_stmt_init($mysqli);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        exit();
                    } else {
                        print_r("Reached completed delete");
                        mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                        mysqli_stmt_execute($stmt);
                        header("Location: login.php?newpwd=passwordupdated");
                    }


                }
            }
        }
    }


}