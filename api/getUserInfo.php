<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once('../database.php');
    require_once '../logger.php';
    global $logger;

    $mysqli = getConnection(1, 0);
    require(__DIR__ . "/getUserFromToken.php");
    $bruker = getUserFromToken($mysqli);

    if ($bruker) {
        $sql = $mysqli->prepare("CALL getUserFromID(?)");
        $sql->bind_param("i", $bruker["user_id"]);
        $sql->execute();
        $user_info = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($user_info);
    } else {
        if (isset($_POST["auth_token"])) {
            $logger->info("Error with token" . isset($_POST["auth_token"]));
        }
    }
}
?>