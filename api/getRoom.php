<?php
require(__DIR__ . "/../verifySubject.php");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once('../database.php');
    require_once '../logger.php';
    global $logger;
    $mysqli = getConnection(1, 0);
    require(__DIR__ . "/getUserFromToken.php");
    $bruker = getUserFromToken($mysqli);

    if ($bruker) {
        if (doesCurrentUserHaveSubject($_POST['room'], $bruker['user_id'], null)) {
            $sql = $mysqli->prepare("CALL getConversationInChatRoomAnonymous(?)");
            $sql->bind_param("i", $_POST['room']);
            $sql->execute();
            $conversations = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
            echo json_encode($conversations);
        } else {
            if (isset($_POST["auth_token"])) {
                $logger->info("Error with token: " . isset($_POST["auth_token"]));
            }
        }
    }
}
?>