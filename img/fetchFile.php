<?php
require("../file_handler.php");
if (isset($_GET['UUID'])) {
    $uuid = htmlspecialchars($_GET['UUID']);
    getPicture($uuid);
}
?>