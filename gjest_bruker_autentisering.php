<?php
$title = "Angi kode - Gjest";
include 'inc/header.php';
require_once ('database.php');
require_once ('functions.php');
require_once 'logger.php';
?>
<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" and strlen($_POST["pin"]) > 0 and strlen($_POST["subject_id"]) > 0)  {
    //finner emne med pin-koden
    $mysqli = getConnection(0,0);
    $sql = $mysqli->prepare("CALL getSubjectWithPIN(?,?)");
    $sql->bind_param("ii", $mysqli->real_escape_string($_POST["pin"]), $mysqli->real_escape_string($_POST["subject_id"]));
    $sql->execute();
    $emne = $sql->get_result()->fetch_assoc()["subject_id"];

    //hvis emne med pin-koden finnes
    if($emne)
    {
        session_start();

        // Lagrer pin for videre validering når bruker ankommer rommet.
        $_SESSION["s_pin_code"] = $_POST["pin"];
        $_SESSION["s_subject_id"] = $_POST["subject_id"];

        $logger->info("GUEST AUTHENTICATION: SUCCESSFULL for SUBJECTID {$_POST['subject_id']}");

        // Send til emne-siden
        header(sprintf("Location: emne.php?room=%d",$emne));
        exit;
    } else {
        $logger->warning("GUEST AUTHENTICATION: FAILED. PIN tried: {$_POST['pin']} and subject tried: {$_POST['subject_id']}");
        $is_invalid = true;
    }
}
?>

<h1>Angi emne PIN-koden din:</h1>

<?php if ($is_invalid): ?>
    <em style="color:red;">Emnekode og PIN kode passer ikke sammen.</em>
<?php endif; ?>

<form method="post">
    <label for="subject">Emnekode</label>
    <input type="number" id="subject_id" name="subject_id">
    <label for="pin">PIN-kode</label>
    <input type="number" id="pin" name="pin">
    <button>Valider</button>
</form>
<p>Trykk <a href="index.php"> her</a> for å gå tilbake til hovedsiden.</p>
<?php include 'inc/footer.php'; ?>
