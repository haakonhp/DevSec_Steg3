<?php
$title = "Emne";
require("inc/header.php");
require("functions.php");
require("check.php");
require_once 'logger.php';

?>
    <link rel="stylesheet" href="styles/index_style.css">
    <link rel="stylesheet" href="styles/emnestyles.css">
<?php
$full_content = "";

// User blir definert senere dersom bruker blir registrert som gjest eller ordentlig bruker.
$user = "";

// Endrer room verdien til en int, det er ingen annen info den skal være.
// Kunne endret dette til en variabel og endret alle referanser,
// men vi trenger ikke den uescapede verdien til noe.
$_GET["room"] = intval($_GET["room"]);
$_GET["only-content"] = intval($_GET["only-content"]);
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
if ($valid === TRUE) {
    $sql = $mysqli->prepare("CALL getSubjectDataFromID(?)");
    $sql->bind_param("i", $_GET["room"]);
    $sql->execute();
    $emne_detaljer = ($sql->get_result()->fetch_assoc());
    $mysqli->next_result();


    $full_content .= "<h1>{$emne_detaljer["subject_id"]} - {$emne_detaljer["subject_name"]} </h1>";
    $full_content .= "<section class='top_grid'>";
    $full_content .= createIFrame("<p class='one'>Hei " . htmlspecialchars($user["name"]) . '!</p>', "40 px", "", "", true, true);
    $full_content .= "<p class='two'><a href='index.php'>Hjem</a></p>";
    $full_content .= "<p class='three'><a href='logout.php'>Logg ut</a></p>";
    $full_content .= "</section>";

    $full_content .= "<h2>Kommentarer:</h2>";
    // Mulighet for å skrive en topp nivå kommentar.
    $full_content .= createNewButton();
    $full_content .= "<form hidden method='post' class='inputForm' id='createNew' action='emne_submit.php'>
        <input type='text' name='text'>
        <input type='hidden' name='roomRedirect' value='{$_GET['room']}'>
        <input type='submit' name='submit' value='Add top comment'>
        </form>";


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

    $chat_content = "";
    foreach ($study_field as $key => $value) {
        // Dekoder før escaping, dette er slik at innholdet bevarer sin verdi uansett om innholdet er escapet før (som ved innsetning,)
        $comment_name = htmlspecialchars(htmlspecialchars_decode($value['name']));
        $comment_text = htmlspecialchars(htmlspecialchars_decode($value['text']));
        $img_html = !empty($value["photo_path"]) ? "<img src='img/{$value["photo_path"]}' alt='profilbilde' width = '80' height = '80'>" : "";

        $chat_content .= createMessage($comment_text, $comment_name, $value['depth'], $img_html);
        $chat_content .= createReplyButton($value['id'], 0);
        $chat_content .= appendReplyForm($value['id'], $_GET['room']);
        $chat_content .= appendCloseButton($value['id']);
        $chat_content .= createReportButton($value['id'], 0);
        $chat_content .= createReportForm($value['id'], $_GET['room']);
        $chat_content .= appendCloseButton($value['id']);
        $chat_content .= "</article>";
    }
    $full_content .= createIFrame($chat_content, "80%", "primaryContent", "allow-forms", false, true);
    $full_content .= "<br>Trykk <a href='index.php'>her </a> for å gå tilbake til hovedsiden";
    // Returner kun tekst hvis innholdet hentes internt, som det skjer ved "refresh" når brukeren submitter informasjon.
    // Dette gjør at iframes som reloader ikke får en ny kopi av den fulle siden.
    if ($_GET['only-content'] == "1") {
        echo $chat_content;
        die();
    }
    print_r($full_content);
} // Dersom brukeren ikke har emnet som tilsvar denne nettsiden, vil de bli nektet adgang
else {
    $logger->warning("EMNE: User attempted to enter page without valid authentication");
    echo "<h1> Nektet tilgang </h1>";
    echo "<strong>Du har ikke tilgang til dette emnet</strong><br><br>";
    echo "Trykk <a href='index.php'>her </a> for å gå tilbake til hovedsiden";
}
?>

<?php include 'inc/footer.php'; ?>