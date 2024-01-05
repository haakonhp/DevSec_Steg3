<?php
$title = "Registrer deg - Ansatt";
require_once("database.php");
$mysqli = getConnection(0,0);
include 'inc/header.php';

session_start();
session_unset();
?>

    <link rel="stylesheet" href="style.css">

    <h1>Registrer deg - Ansatt</h1>

    <form action="ansatt_process_signup.php" method="post" enctype="multipart/form-data" novalidate>
        <div>
            <label for="bilde">Last opp bilde:</label>
            <!--<input type="file" id="bilde" name="bilde" accept="image/png, image/jpeg">-->
            <!-- Image input file accepting png and jpeg, with default value if session bilde is set -->
            <input type="file" id="bilde" name="bilde" accept="image/png, image/jpeg">
        </div>

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
            <label for="password">Passord</label>
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
            <label for="subjects">Emne</label>
            <select name="subjects" id="subjects">
                <?php
                $sql = "CALL getSubjects()";
                $rows = $mysqli->query($sql);
                $subjects = mysqli_fetch_all($rows, MYSQLI_ASSOC);
                foreach ($subjects as $key => $value) {
                    echo "<option value='{$value['subject_id']}'>
                        {$value['subject_name']}
                    </option>";
                }
                ?>
            </select>
            <p class="error subjects_error">
                <?php
                if (isset($_GET['subjects_error'])) {
                    echo htmlspecialchars($_GET['subjects_error']);
                }
                ?>
            </p>
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