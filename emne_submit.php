<?php
require_once 'logger.php';
require("verifySubject.php");
if (doesCurrentUserHaveSubject($_POST['roomRedirect'], $_SESSION["s_bruker_id"], $_SESSION["s_subject_id"])) {
    $user_id = (!empty($_SESSION["s_bruker_id"])) ? $_SESSION["s_bruker_id"] : 1;
    $text = htmlspecialchars($_POST['text']);
    switch ($_POST['submit']) {
        case 'report':
        {
            $sql = $mysqli->prepare("CALL report(?,?,?)");
            $sql->bind_param("sis", $text, $user_id, $_POST['reply_id']);
            $result = $sql->execute();
            header("Location: emne.php?room={$_POST['roomRedirect']}&only-content=1");
            break;
        }
        case 'Reply':
        {
            $sql = $mysqli->prepare("CALL reply(?,?,?)");
            $sql->bind_param("sis", $text, $user_id, $_POST['reply_id']);
            $result = $sql->execute();
            header("Location: emne.php?room={$_POST['roomRedirect']}&only-content=1");

            break;
        }
        case 'Add top comment':
        {
            $sql = $mysqli->prepare("CALL createComment(?,?,?)");
            $sql->bind_param("sii", $text, $user_id, $_POST['roomRedirect']);
            $result = $sql->execute();
            header("Location: emne.php?room={$_POST['roomRedirect']}");
            break;
        }
        default:
        {
            die();
        }
    }
} else {
    print_r("Error matching subject");
}
?>