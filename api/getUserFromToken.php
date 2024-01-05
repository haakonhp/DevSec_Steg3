<?php
function getUserFromToken($mysqli) {
    $sql = $mysqli->prepare("CALL getUserFromToken(?)");
    $sql->bind_param("s", $_POST["auth_token"]);
    $sql->execute();
    $bruker = $sql->get_result()->fetch_assoc();
    $mysqli->next_result();
    return $bruker;
}


?>