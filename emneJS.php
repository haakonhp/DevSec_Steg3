<?php
$title = "Emne";
require("inc/header.php");
require("functions.php");
require("check.php"); ?>

    <link rel="stylesheet" href="styles/index_style.css">
    <link rel="stylesheet" href="styles/emnestyles.css">
    <!-- Dersom brukeren er funnet og er set med session beskrevet ovenfor, får vi tilgang til innholdet under.-->
<?php
// User blir definert senere dersom bruker blir registrert som gjest eller ordentlig bruker.
$user = "";
// Endrer room verdien til en int, det er ingen annen info den skal være.
// Kunne endret dette til en variabel og endret alle referanser,
// men vi trenger ikke den uescapede verdien til noe.
$_GET["room"] = intval($_GET["room"]);
$valid = FALSE;
if (isset($bruker)) {
    // Setter opp spørring.
    $sql = $mysqli->prepare("CALL doesUserHaveSubject(?, ?)");
    $sql->bind_param("si", $_SESSION["s_bruker_id"], $_GET["room"]);
    $sql->execute();
    // Henter resultat.
    $valid = ($sql->get_result()->fetch_row()[0] == 1);
    $user = $bruker;
    $mysqli->next_result();
} elseif (isset($emne)) { // For gjest_bruker med pin_kode. Henter allerede subject_id fra SESSION.
    if ($emne == $_GET["room"]) {
        $valid = TRUE;
        $user = array(
            "name" => "Gjest"
        );
    }
} else {
    header("Location: login.php");
}

// Dersom brukeren har emnet som tilsvarer denne siden, vil de få tilgang til innholdet som skrives under.
if ($valid === TRUE) {
    $sql = $mysqli->prepare("CALL getSubjectDataFromID(?)");
    $sql->bind_param("i", $_GET["room"]);
    $sql->execute();
    $emne_detaljer = ($sql->get_result()->fetch_assoc());
    $mysqli->next_result();

    // Henter Subject name?>
    <h1><?= $emne_detaljer["subject_id"] ?> - <?= $emne_detaljer["subject_name"]; ?></h1>

    <section class="top_grid">
        <?php echo createIFrame("<p class='one'>Hei " . htmlspecialchars($user['name']) . '!</p>', '65px', "", "", false) ?>
        <p class="two"><a href='index.php'>Hjem</a></p>
        <p class="three"><a href="logout.php">Logg ut</a></p>
    </section>

    <?php
    echo "<h2>Kommentarer:</h2>";
    // Mulighet for å skrive en topp nivå kommentar.
    echo "
        <button id='createNewButton' onclick='
        var hiddenValue = document.getElementById(\"createNew\").hidden.valueOf();
        document.getElementById(\"createNew\").hidden = !hiddenValue;'>Create new comment</button>

        <form hidden method='post' id='createNew' action='emne_submit.php'>
        <input type='text' name='text'>
        <input type='hidden' name='roomRedirect' value='{$_GET['room']}'>
        <input type='submit' name='submit' value='Add top comment'>
        </form>
        ";

    $user_id = (!empty($_SESSION["s_bruker_id"])) ? $_SESSION["s_bruker_id"] : 1;
    $role_sql = $mysqli->prepare("CALL getUserRoles(?)");
    $role_sql->bind_param("i", $user_id);
    $role_sql->execute();
    $role = $role_sql->get_result()->fetch_assoc()["role_name"];
    $mysqli->next_result();

    if ($role == "Administrator") {
        $sql = "CALL getCommentChainAsAdmin(?)";
    } else {
        $sql = "CALL getConversationInChatRoomAnonymous(?)";
    }
    $fetch_sql = $mysqli->prepare($sql);
    $fetch_sql->bind_param("i", $_GET["room"]);
    $fetch_sql->execute();
    $study_field = $fetch_sql->get_result()->fetch_all(MYSQLI_ASSOC);
    $content = "";

    foreach ($study_field as $key => $value) {
        $comment_name = htmlspecialchars($value['name']);
        $comment_text = htmlspecialchars($value['text']);
        $height = ($role == 'Administrator' ? "150px" : "70px");
        $img_html = !empty($value["photo_path"]) ? "<img src='img/{$value["photo_path"]}' alt='profilbilde' width = '80' height = '80'>" : "";

        $content .= "<article style='margin-left: calc({$value['depth']} * 35px)'>";
        $content .= createIFrame("$img_html <p>{$comment_name}: {$comment_text}</p>", $height, "", "", false);
        $content .= createReplyButton($value['id'], 1);
        $content .= createReportButton($value['id'], 1);
        $content .= appendReplyForm($value['id'], $_GET['room']);
        $content .= createReportForm($value['id'], $_GET['room']);
        $content .= "</article>";
    }
    echo $content;
    echo "<br>Trykk <a href='index.php'>her </a> for å gå tilbake til hovedsiden";
} // Dersom brukeren ikke har emnet som tilsvar denne nettsiden, vil de bli nektet adgang
else {
    echo "<h1> Nektet tilgang </h1>";
    echo "<strong>Du har ikke tilgang til dette emnet</strong><br><br>";
    echo "Trykk <a href='index.php'>her </a> for å gå tilbake til hovedsiden";
}
?>

<?php include 'inc/footer.php'; ?>