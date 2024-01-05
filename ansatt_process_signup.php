<?php

session_start();
require_once(__DIR__ . "/file_handler.php");
require_once 'logger.php';

if (isset($_POST['signup'])) {

    $_SESSION['form_data'] = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'subjects' => $_POST['subjects'],
    ];

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];
    $subjects = $_POST['subjects'];

    $errors = [];

    if (empty($name)) {
        $errors['name_error'] = 'Navn må fylles ut';
    }

    if (strlen($name) < 3) {
        $errors['name_error'] = 'Navn må være minst 3 bokstaver';
    }

    if (empty($email)) {
        $errors['email_error'] = 'E-post må fylles ut';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email_error'] = 'E-post må være en gyldig e-post';
    }

    if (empty($password)) {
        $errors['password_error'] = 'Passord må fylles ut';
    }

    if (strlen($password) < 8) {
        $errors['password_error'] = 'Passord må inneholde minst 8 tegn';
    }

    if (!preg_match("/[a-zA-Z]/", $password)) {
        $errors['password_error'] = 'Passord må inneholde minst 1 bokstav';
    }

    if (!preg_match("/[0-9]/", $password)) {
        $errors['password_error'] = 'Passord må inneholde minst 1 tall';
    }

    if ($password !== $password_confirmation) {
        $errors['password_confirmation_error'] = 'Passordene må være like';
    }

    if (empty($subjects)) {
        $errors['subjects_error'] = 'Emne må fylles ut';
    }

    // Bilde implementering i dir folder og setter path navn
    $photo_path = uploadImg($_FILES["bilde"]);

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $mysqli = getConnection(0,0);

    $emailCheckQuery = "Call DoesEmailExist(?)";
    $emailstmt = mysqli_prepare($mysqli, $emailCheckQuery);
    $emailstmt->bind_param("s", $email);
    mysqli_stmt_execute($emailstmt);
    mysqli_stmt_bind_result($emailstmt, $rowValue);
    mysqli_stmt_fetch($emailstmt);
    mysqli_stmt_close($emailstmt);

    if ($rowValue > 0) {
        $errors['email_error'] = 'Eposten er allerede registrert.';
        $errors['error'] = 'E-posten er allerede registrert. Vennligst prøv en annen e-post, eller <a href="login.php">logg inn</a>.';
    }

    if (!empty($errors)) {
        $query = http_build_query($errors);
        header("Location: ansatt_signup.php?$query");
    } else {

        $sql = "CALL createTeacher(?, ?, ?, ?, ?);";

        $stmt = $mysqli->stmt_init();
        if (!$stmt->prepare($sql)) {
            die('SQL error: (' . $mysqli->errno . ') ' . $mysqli->error);
        }

        $stmt->bind_param("sssss", $name, $email, $subjects, $password_hash, $photo_path);

        $stmt_result = $stmt->execute();

        if ($stmt_result) {
            $logger->info("TEACHER SIGNUP: Teacher successfully registered.");
            header("Location: signup_success.php");
            session_destroy();
        } else {
            header("Location: ansatt_signup.php?error=Noe gikk galt. Vennligst prøv igjen.");
        }

        $stmt->close();
        $mysqli->close();
        exit();
    }
}