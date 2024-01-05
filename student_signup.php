<?php
$title = "Registrer deg - Student";
require_once("database.php");
$mysqli = getConnection(0, 0);
include 'inc/header.php';

session_start();
session_unset();
?>

<link rel="stylesheet" href="style.css">

<h1>Registrer deg - Student</h1>

<form action="student_process_signup.php" method="post" novalidate>
    <div>
        <label for="name">Navn</label>
        <input type="text" id="name" name="name" value="<?= $_SESSION['form_data']['name'] ?? ''; ?>">
        <p class="error name_error">
            <?php
            if (isset($_GET['name_error'])) {
                echo htmlspecialchars($_GET['name_error']);
            }
            ?>
        </p>
    </div>

    <div>
        <label for="email">E-post</label>
        <input type="email" id="email" name="email" value="<?= $email = $_SESSION['form_data']['email'] ?? ''; ?>">
        <p class="error email_error">
            <?php
            if (isset($_GET['email_error'])) {
                echo htmlspecialchars($_GET['email_error']);
            }
            ?>
        </p>
    </div>

    <div>
        <label for="studie_retning">Studieretning</label>
        <select name="studie_retning" id="studie_retning" value="<?= $studie_retning = $_SESSION['studie_retning'] ?? ''; ?>">
            <?php
            $sql = "CALL getStudyFields()";
            $rows = $mysqli->query($sql);
            $study_field = mysqli_fetch_all($rows, MYSQLI_ASSOC);
            foreach ($study_field as $key => $value) {
                echo "<option value='${value['field_code']}'>
                {$value['study_name']}
            </option>";
                $mysqli->next_result();
            } ?>
        </select>
    </div>

    <div>
        <label for="studie_kull">Studiekull</label>
        <select name="studie_kull" id="studie_kull" value="<?= $studie_kull = $_SESSION['studie_kull'] ?? ''; ?>">
            <?php
            $sql = "CALL getStudySemesters()";
            $rows = $mysqli->query($sql);
            $study_semester = mysqli_fetch_all($rows, MYSQLI_ASSOC);
            foreach ($study_semester as $key => $value) {
                echo "<option value='{$value['semester_code']}'>
            {$value['semester_name']} </option>";
                $mysqli->next_result();
            } ?>
        </select>
    </div>
    <div>
        <label for=" password">Passord</label>
            <input type="password" id="password" name="password" pattern=".{8,}" title="Passordet må være 8 sifra">
            <p class="error password_error">
                <?php
                if (isset($_GET['password_error'])) {
                    echo htmlspecialchars($_GET['password_error']);
                }
                ?>
            </p>
    </div>

    <div>
        <label for="password_confirmation">Gjenta passord</label>
        <input type="password" id="password_confirmation" name="password_confirmation">
        <p class="error password_confirmation_error">
            <?php
            if (isset($_GET['password_confirmation_error'])) {
                echo htmlspecialchars($_GET['password_confirmation_error']);
            }
            ?>
        </p>
    </div>

    
    <div>
        <h3>Sikkerhetsspørsmål</h3>
        <p>Husk sikkerhetsspørsmålene og svarene du gir.</p>
        <br>
        <label for="security_question">Velg Sikkerhetsspørsmål 1:</label>
        <select name="security_question_1" id="security_question_1" value="<?= $security_question_1 = $_SESSION['security_question_1'] ?? ''; ?>">
            <?php
            $sql = "CALL getSecurityQuestions(1)";
            if ($rows = $mysqli->query($sql)) {
                $security_question_1 = mysqli_fetch_all($rows, MYSQLI_ASSOC);
                foreach ($security_question_1 as $key => $value) {
                    echo "<option value='{$value['id']}'>
                    {$value['text']}</option>";
                    $mysqli->next_result();
                }
            }
            ?>
        </select>
    </div>

    <div>
        <label for="answer_question_1">Svar:</label>
        <input type="text" id="answer_1" name="answer_1" value="<?= $answer_1 = $_SESSION['form_data']['answer_1'] ?? ''; ?>">
    </div>

    <div>
        <label for="security_question">Velg Sikkerhetsspørsmål 2:</label>
        <select name="security_question_2" id="security_question_2" value="<?= $security_question_2 = $_SESSION['security_question_2'] ?? ''; ?>">
            <?php
            $sql = "CALL getSecurityQuestions(2)";
            if ($rows = $mysqli->query($sql)) {
                $security_question_2 = mysqli_fetch_all($rows, MYSQLI_ASSOC);
                foreach ($security_question_2 as $key => $value) {
                    echo "<option value='{$value['id']}'>
                    {$value['text']}</option>";
                    $mysqli->next_result();
                }
            }
            ?>
        </select>
    </div>

    <div>
        <label for="answer_question_2">Svar:</label>
        <input type="text" id="answer_2" name="answer_2" value="<?= $answer_2 = $_SESSION['form_data']['answer_2'] ?? ''; ?>">
    </div>

    <p class="error">
        <?php
        if (isset($_GET['error'])) {
            echo htmlspecialchars($_GET['error']);
        }
        ?>
    </p>

    <input type="submit" name="signup" value="Registrer deg">

    <p>Har du allerede en bruker? <a href="login.php">Logg inn her</a>.</p>

</form>

<?php include 'inc/footer.php'; ?>