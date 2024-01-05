<?php
include 'inc/header.php';
require_once 'logger.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once('database.php');
$mysqli = getConnection(0, 0);


//få med input fra form
if (isset($_POST["reset-request-submit"])) {

    //lage 2 tokens
    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);
    //inkludere select token og validator token i link
    // Rewrite på apache tar ikke denne automatisk, legger til tvungen ssl
    $url = "https://" . $_SERVER['SERVER_NAME'] . "/steg2/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);
    //dagens dato + 30min
    $userEmail = $_POST["email"];

    $userEmail = filter_var($userEmail, FILTER_SANITIZE_EMAIL);

    $sql = $mysqli->prepare("CALL selectUserFromEmail(?)");
    $sql->bind_param("s", $mysqli->real_escape_string($userEmail));
    $sql->execute();
    $bruker = $sql->get_result()->fetch_assoc();

    if (empty($bruker)) {
        sleep(1);
        $logger->warning("Someone tried to reset a password to a non-existent email. Tried for: $userEmail");
        header("Location: reset-password.php?reset=request_sent");
    } else {
        //delete existing token of user inside db.
        $sql = "CALL deletePWDToken(?)";
        $stmt = mysqli_stmt_init($mysqli);
        //if fail - error
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo "ERRRORR";
            exit();
            //if no fail -> exw
        } else {
            mysqli_stmt_bind_param($stmt, "s", $userEmail);
            mysqli_stmt_execute($stmt);
        }


        $sql = "CALL createResetToken(?, ?, ?)";
        $stmt = mysqli_stmt_init($mysqli);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo "ERRRORR";
            exit();
        } else {
            //hash before transfer using default
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "sss", $userEmail, $selector, $hashedToken);
            mysqli_stmt_execute($stmt);
        }
        //closing all
        mysqli_stmt_close($stmt);
        //mysqli_close();



        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = 'datasikkerhetgr1@gmail.com';                     //SMTP username
            $mail->Password = 'bkhzwctgjerzexgu';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('datasikkerhetgr1@gmail.com', 'DXD');
            $mail->addAddress($userEmail);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Reset password ';
            $mail->Body = '<br>We have received a password reset request. The link to reset your password is below.</br>';
            $mail->Body .= '<a href="' . $url . '">' . $url . '</a></p>';
            //   $mail->Body .= '<a href=localhost/Steg1/create-new-password.php?"' . $url . '">' . $url . '</a></p>';
            $logger->info("Password reset request successfully sent email to $userEmail");
            $mail->send();

            header("Location: reset-password.php?reset=request_sent");
            mysqli_close($mysqli);
        } catch (Exception $e) {
            $emailerror =  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $logger->debug("$emailerror. Email tried = $userEmail.");
        }
    }
}
