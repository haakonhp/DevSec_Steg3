<?php
require_once 'logger.php';
require_once("database.php");
session_start();

if (isset($_POST['signup'])) {
    $_SESSION['form_data'] = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'studie_retning' => $_POST['studie_retning'],
        'studie_kull' => $_POST['studie_kull'],
        'answer_1' => $_POST['answer_1'],
        'answer_2' => $_POST['answer_2'],
    ];

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];
    $studie_retning = $_POST['studie_retning'];
    $studie_kull = $_POST['studie_kull'];
    $security_question_1 = $_POST['security_question_1'];
    $security_question_2 = $_POST['security_question_2'];
    $answer_1 = $_POST['answer_1'];
    $answer_2 = $_POST['answer_2'];

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

    if (strlen($password) < 8) {
        $errors['password_error'] = 'Passord må inneholde minst 8 tegn';
    }

    if (!preg_match("/[a-zA-Z]/", $_POST['password'])) {
        $errors['password_error'] = 'Passordet må inneholde minst 1 bokstav';
    }

    if (!preg_match("/[0-9]/", $_POST['password'])) {
        $errors['password_error'] = 'Passordet må inneholde minst 1 tall';
    }

    if ($_POST['password'] !== $_POST['password_confirmation']) {
        $errors['password_confirmation_error'] = 'Passordene må være like';
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $mysqli = getConnection(0,0);

    $emailCheckQuery = "Call DoesEmailExist(?)";
    $emailstmt = mysqli_prepare($mysqli, $emailCheckQuery);
    $emailstmt->bind_param("s", $email);
    mysqli_stmt_execute($emailstmt);
    mysqli_stmt_bind_result($emailstmt, $rowValue);
    mysqli_stmt_fetch($emailstmt);
    mysqli_stmt_close($emailstmt);

    if($rowValue > 0) {
        $errors['email_error'] = 'Eposten er allerede registrert.';
        $errors['error'] = 'E-posten er allerede registrert. Vennligst prøv en annen e-post, eller <a href="login.php">logg inn</a>.';
    }

    if(!empty($errors)) {
        $query = http_build_query($errors);
        header("Location: student_signup.php?$query");
    } else {
        $sql = "CALL createStudent(?,?,?,?,?,?,?,?,?,?);";
        $stmt = $mysqli->stmt_init();

        if (!$stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }

        $photo_path = "fetchFile.php?UUID=default";

        $stmt->bind_param("ssssiiiiss",
            $name,
            $photo_path,
            $password_hash,
            $email,
            $studie_retning,
            $studie_kull,
            $security_question_1,
            $security_question_2,
            $answer_1,
            $answer_2);

        $stmt_result = $stmt->execute();

        if($stmt_result) {
            $logger->info("STUDENT SIGNUP: Student successfully registered.");
            header("Location: signup_success.php");
            session_destroy();
        } else {
            header("Location: student_signup.php?error=Noe gikk galt. Vennligst prøv igjen.");
        }

        $stmt->close();
        $mysqli->close();
        exit();
    }
}

