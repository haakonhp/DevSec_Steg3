<?php
$title = "Admin Panel";
require("inc/header.php");
require("check.php");
require_once 'logger.php';?>



    <link rel="stylesheet" href="styles/admin_styles.css">


<?php
if ($is_admin) {
    $logger->info("ADMIN PANEL: UserID: {$bruker["user_id"]} - {$bruker["name"]} accessed the admin panel.");
    echo "<h1> Admin Panel </h1>";
    echo "<h3> Uverifiserte lærere </h3>";
    $sql =
        "CALL adminGetUnverifiedTeachers()";
    $rows = $mysqli->query($sql);
    $unverified_teachers = mysqli_fetch_all($rows, MYSQLI_ASSOC);

    if (count($unverified_teachers) > 0) {
        foreach ($unverified_teachers as $key => $value) {
            $teacher_name = htmlspecialchars($value["name"]);
            echo "<article class='admin_grid'>
            <p class='name'>{$teacher_name}</p>        

            <form class='form' method='post' id='{$value['user_id']}' action='validate_teacher.php'>
            <input type='hidden' name='executeType' value='validate'>
            <input type='hidden' name='user_id' value='{$value['user_id']}'>
            <input class='endre' type='submit' name='godkjenn' value='Godkjenn'>
            <input class='slett' type='submit' name='slett' value='Avvis'>
            </form>
            </article>";
        }
    } else {
        echo "Det er for øyeblikket ingen ubekreftede forelesere.";
    }

    $mysqli->next_result();

    echo "<h3> Studenter </h3>";
    $student_role = 1;
    $sql = $mysqli->prepare("CALL getUsersWithRole(?)");
    $sql->bind_param("i", $student_role);
    $sql->execute();
    $students = $sql->get_result()->fetch_all(MYSQLI_ASSOC);

    if (count($students) > 0) {
        foreach ($students as $key => $value) {
            $student_name = htmlspecialchars($value["name"]);
            echo "<article class='admin_grid'>
            <p class='name'>{$student_name}</p>        

            <form class='form' method='post' id='{$value['user_id']}' action='admin_action.php'>
            <input type='hidden' name='executeType' value='validate'>
            <input type='hidden' name='user_id' value='{$value['user_id']}'>
            <input class='endre' type='submit' name='endre' value='Endre'>
            <input class='slett' type='submit' name='slett' value='Slett'>
            </form>
            </article>";
        }
    } else {
        echo "Det er for øyeblikket ingen registrerte studenter.";
    }

    $mysqli->next_result();

    echo "<h3> Forelesere </h3>";
    $verified_teacher_role = 3;
    $sql = $mysqli->prepare("CALL getUsersWithRole(?)");
    $sql->bind_param("i", $verified_teacher_role);
    $sql->execute();
    $teachers = $sql->get_result()->fetch_all(MYSQLI_ASSOC);

    if (count($teachers) > 0) {
        foreach ($teachers as $key => $value) {
            $teacher_name = htmlspecialchars($value["name"]);
            echo "<article class='admin_grid'>
            <p class='name'>{$teacher_name}</p>        

            <form class='form' method='post' id='{$value['user_id']}' action='admin_action.php'>
            <input type='hidden' name='executeType' value='validate'>
            <input type='hidden' name='user_id' value='{$value['user_id']}'>
            <input class='endre' type='submit' name='endre' value='Endre'>
            <input class='slett' type='submit' name='slett' value='Slett'>
            </form>
            </article>";
        }
    } else {
        echo "Det er for øyeblikket ingen registrerte forelesere.";
    }

    $mysqli->next_result();

    $sql =
        "CALL getReports()";
    $rows = $mysqli->query($sql);
    $reports = mysqli_fetch_all($rows, MYSQLI_ASSOC);

    $count = count($reports);

    echo "<h3> Rapporterte meldinger ($count)</h3>";

    if ($count > 0) {
        foreach ($reports as $key => $value) {
            $reporter_name = htmlspecialchars($value["reporter_name"]);
            $reporter_comment = htmlspecialchars($value["report_text"]);

            $reported_name = htmlspecialchars($value["reported_name"]);
            $reported_comment = htmlspecialchars($value["comment_text"]);

            echo "<article class='admin_grid'>

            <div class='reported' style='border: 2px solid'>
                <p class>Kommentar</p>
                <p class='name'>{$reported_name}</p>
                <p class='name'>{$reported_comment}</p>
            </div>
            <div class='reported' style='border: 2px solid'>
                <p class>Rapportert av</p>
                <p class='name'>{$reporter_name}</p>
                <p class='name'>{$reporter_comment}</p>
           </div>
           
           <form method='post' action='emne_delete_comment.php' style='margin: 0 auto;'>
                <input type='hidden' name='roomRedirect' value='{$_GET['room']}'>
                <input type='hidden' name='comment_id' value='{$value['comment_id']}'>
                <input type='submit' name='delete-comment' value='Delete comment'>
            </form>
            
            <form method='post' action='emne_delete_comment.php' style='margin: 0 auto;'>
                <input type='hidden' name='roomRedirect' value='{$_GET['room']}'>
                <input type='hidden' name='report_id' value='{$value['report_id']}'>
                <input type='submit' name='delete-report' value='Delete report'>
            </form>
            
            </article>";
        }
    } else {
        echo "Det er for øyeblikket ingen rapporterte meldinger.";
    }

    $mysqli->next_result();
    echo "<br>";
    echo "Trykk <a href='index.php'>her </a> for å gå tilbake til hovedsiden";

} else {
    if ($bruker) {
        $logger->warning("ADMIN PANEL: UserID: {$bruker["user_id"]} {$bruker["name"]} tried to access admin page without valid authentication.");
    } else {
        $logger->warning("ADMIN PANEL: Unauthenticated user tried to access admin page without valid authentication.");
    }
    // Nektet tilgang tilsendes rett til index.php
    header("Location: index.php");
}

include 'inc/footer.php'; ?>