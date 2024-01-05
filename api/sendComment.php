<?php
require(__DIR__ . "/../verifySubject.php");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once('../database.php');
    require_once '../logger.php';
    global $logger;
    $logger->info(print_r($_POST, true));
    $mysqli = getConnection(1, 0);
    require(__DIR__ . "/getUserFromToken.php");
    $bruker = getUserFromToken($mysqli);

    if (doesCurrentUserHaveSubject($_POST['room_id'], $bruker['user_id'], null)) {
        $text = htmlspecialchars($_POST['text']);
        if (empty($_POST['reply_id'])) {
            $sql = $mysqli->prepare("CALL createComment(?,?,?)");
            $sql->bind_param("sii", $text, $bruker['user_id'], $_POST['room_id']);
        } else {
            $sql = $mysqli->prepare("CALL reply(?,?,?)");
            $logger->info("Attempt insert reply with id: " . $_POST['reply_id']);
            $sql->bind_param("sis", $text, $bruker['user_id'], $_POST['reply_id']);
        }
        $sql->execute();
        echo "Successfully posted";
    } else {
        $logger->info("User attempted to send comment to unrelated subject: " . $_POST['room_id']);
    }
}
?>