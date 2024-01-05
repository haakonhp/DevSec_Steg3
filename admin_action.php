<?php
$title = "Admin Panel - Endre Profil";
require("inc/header.php");
require("check.php");
require_once 'logger.php';


if ($is_admin) {
$logger->info("ADMIN ACTIONS: {$bruker["user_id"]} - {$bruker["name"]} accessed the admin action panel.");
    // Lagrer <select> form i variabel for studie_kull som kan brukes senere
$studie_kull_form = '<select name="i_semester" id="i_semester">
<option value="' . ($studie_kull = $_SESSION["studie_kull"] ?? "") . '"></option>';


$sql = "CALL getStudySemesters()";
$rows = $mysqli->query($sql);
$study_semester = mysqli_fetch_all($rows, MYSQLI_ASSOC);
foreach ($study_semester as $key => $value) {
$studie_kull_form .= "<option value='{$value["semester_code"]}'>
{$value["semester_name"]} </option>";
$mysqli->next_result();
}

$studie_kull_form .= '</select>';


// Lagrer <select> form i variabel for studieretning som kan brukes senere
$studie_retning_form = '<select name="i_study_field" id="i_study_field">
<option value="' . ($studie_retning = $_SESSION["studie_retning"] ?? "") . '"></option>';


$sql = "CALL getStudyFields()";
$rows = $mysqli->query($sql);
$study_field = mysqli_fetch_all($rows, MYSQLI_ASSOC);
foreach ($study_field as $key => $value) {
$studie_retning_form .= "<option value='{$value["field_code"]}'>
{$value["study_name"]} </option>";
$mysqli->next_result();
}


$studie_retning_form .= '</select>';

    if (isset($_POST['endre'])) {
        $sql = $mysqli->prepare("CALL getUserFromID(?)");
        $sql->bind_param("i", $_POST['user_id']);
        $sql->execute();
        $bruker = $sql->get_result()->fetch_assoc();

        $name = $bruker['name'];
        $email = $bruker['email'];
        if (is_numeric($bruker['semester_code'])){
            $semester_code = $bruker['semester_code'];
        }else{
            $semester_code = 0;
        }

        if (is_numeric($bruker['study_field'])){
            $field_code = $bruker['study_field'];
        }else{
            $field_code = 0;
        }

        $mysqli->next_result();

        // Sjekker om profilen er student
        $student_role = 1;
        $sql = $mysqli->prepare("CALL doesUserHaveRoleQuery(?, ?)");
        $sql->bind_param("ii", $bruker['user_id'], $student_role);
        $sql->execute();
        $is_student = ($sql->get_result()->fetch_row()[0] == 1);
        $mysqli->next_result();

        // Sjekker om profilen er lærer
        $teacher_role = 3;
        $sql = $mysqli->prepare("CALL doesUserHaveRoleQuery(?, ?)");
        $sql->bind_param("ii", $bruker['user_id'], $teacher_role);
        $sql->execute();
        $is_teacher = ($sql->get_result()->fetch_row()[0] == 1);
        $mysqli->next_result();
        
        echo "<h1> Se profil for : $name </h1>
        <p>Trykk <a href='admin.php'>her</a> for å gå tilbake til Admin Panel</p>
        <br><br>";

        if ($is_student){
            ?> <link rel="stylesheet" href="styles/admin_action_styles.css"> <?php
            $sql = $mysqli->prepare("CALL getSpecificStudySemester(?)");
            $sql->bind_param("i", $semester_code);
            $sql->execute();
            // Lagrer study_semester variabel
            $result = $sql->get_result()->fetch_assoc();
            $study_semester = $result["semester_name"];
            $mysqli->next_result();

            // Lagrer study_field variabel
            $sql = $mysqli->prepare("CALL getSpecificStudyField(?)");
            $sql->bind_param("i", $field_code);
            $sql->execute();
            $result = $sql->get_result()->fetch_assoc();
            $study_field = $result["study_name"];

            $mysqli->next_result();
                // Viser formen

            echo "
            <p> Denne brukeren er en student </p>
            <section class='endre_grid'>
            <article class='info_field'>
            <p class='s_name'> NAVN</p>
            <p class='s_email'> EMAIL</p>
            <p class='s_semester'> SEMESTER</p>
            <p class='s_study_field'> STUDIERETNING</p>
            <p class='s_password'> PASSORD </p>
            </article>
            <article class = 'tekst_article'>
            <p class='name'> $name</p>
            <p class='email'> $email</p>
            <p class='semester'> $study_semester</p>
            <p class='study_field'> $study_field</p>
            <p class='password'> HIDDEN </p>
            </article>
            <form class='form_fields' method='post' id='{$bruker['user_id']}' action='proccess_change.php'>
            <input type='hidden' name='user_id' value='{$bruker['user_id']}'>
            <input type='text' name='i_name'>
            <input class='c_name' type='submit' name='c_name' value='Endre'>
            <input type='text' name='i_email'>
            <input class='c_email' type='submit' name='c_email' value='Endre'>
            $studie_kull_form
            <input class='c_semester' type='submit' name='c_semester' value='Endre'>
            $studie_retning_form
            <input class='c_study_field' type='submit' name='c_study_field' value='Endre'>
            <input type='password' name='i_password'>
            <input class='c_password' type='submit' name='c_password' value='Endre'>
            </form>";

            // Dersom profilen er lærer
        }elseif($is_teacher){
            // Lagrer study_field variabel
            $sql = $mysqli->prepare("CALL getUserSubjects(?)");
            $sql->bind_param("i",$_POST['user_id']);
            $sql->execute();
            $result = $sql->get_result()->fetch_assoc();
            $user_subjects = $result["subject_name"];
            ?> <link rel="stylesheet" href="styles/admin_action_teacher.css"> <?php
            echo "
            <p> Denne brukeren er en foreleser </p>
            <section class='endre_grid'>

            <article class='info_field'>
            <p class='s_name'>NAVN</p>
            <p class='s_email'>EMAIL</p>
            <p class='s_password'>PASSORD</p>
            <p class='s_subjects'>FORELESER FOR</p>
            </article>

            <article class = 'tekst_article'>
            <p class='name'> $name</p>
            <p class='email'> $email</p>
            <p class='password'> HIDDEN </p>
            <p class='subjects'> $user_subjects </p>
            </article>

            <form class='form_fields' method='post' id='{$bruker['user_id']}' action='proccess_change.php'>
            <input type='hidden' name='user_id' value='{$bruker['user_id']}'>
            <input type='text' name='i_name'>
            <input class='c_name' type='submit' name='c_name' value='Endre'>
            <input type='text' name='i_email'>
            <input class='c_email' type='submit' name='c_email' value='Endre'>
            <input type='password' name='i_password'>
            <input class='c_password' type='submit' name='c_password' value='Endre'>
            <input type='text' name='i_subjects'>
            <input class='c_subjects' type='submit' name='c_subjects' value='Endre'>
            </form>";
        }

    }elseif (isset($_POST['slett'])) {
        $sql = $mysqli->prepare("CALL adminDeleteUser(?)");
        $sql->bind_param("i", $_POST['user_id']);
        $sql->execute();
        $result = $sql->get_result();
        $logger->info("ADMIN ACTIONS: {$bruker["user_id"]} {$bruker["name"]} deleted a user from the database.");
        header("Location: admin.php");
    }
} else {
    if ($bruker) {
        $logger->warning("ADMIN ACTIONS: {$bruker["user_id"]} {$bruker["name"]} tried to access admin action page without valid authentication.");
    } else {
        $logger->warning("ADMIN ACTIONS: Unauthenticated user tried to access admin action page without valid authentication.");
    }
    // Nektet tilgang tilsendes rett til index.php
    header("Location: index.php");
}

?>
<?php include 'inc/footer.php'; ?>