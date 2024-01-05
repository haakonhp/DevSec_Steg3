<?php
$title = "Index";
require("inc/header.php");
require_once 'logger.php';?>

<link rel="stylesheet" href="styles/index_style.css">
<?php
// Sjekker session og setter bruker. Dersom bruker er admin, får de også admin tilgang til siden.
require("check.php");

?>
<h1>Hjem</h1>

<?php if (isset($bruker)): ?>
    <section class="top_grid">
        <?php echo createIFrame("<p class='one'>Hei " . htmlspecialchars($bruker["name"]) . '!</p>', "40 px", "", "", true, true); ?>
    <p class="two"><a class="two" href="byttpassord.php">Bytt passord</a></p>
    <p class="three"><a class="three" href="logout.php">Logg ut</a></p>
    </section>
    <img src="img/<?php echo $bruker["photo_path"]?>" alt="profilbilde" width = "150">
    <h2>Dine emner:</h2>
    <?php

    // Utfører SQL spørring for å se etter brukerens emner
    // Dersom brukeren er admin, får de alle emner. Dersom ikke, får de kun emner basert på deres ID.
    if($is_admin){
        $sql = "CALL getSubjects()";
        $rows = $mysqli->query($sql);
        $emner = mysqli_fetch_all($rows, MYSQLI_ASSOC);
        $mysqli -> next_result();
    } else{
        $sql = $mysqli->prepare("CALL getUserSubjects(?)");
        $sql->bind_param("i", $_SESSION["s_bruker_id"]);
        $sql->execute();
        $emner = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        $mysqli -> next_result();
    }

    // Lister opp emner på siden.
    if(count($emner) > 0){
        foreach ($emner as $key => $value) {
            $path = "emne.php?room=" . $value["subject_id"];
            ?> <a href="<?php echo $path?>"><?php echo$value['subject_name']?></a> <br> <?php
        }
    }else{
        echo "Du har ingen emner tilgjengelig.";
    }
    ?>

    <h2>Dine roller:</h2>
    <?php

    // Sjekker om bruker er admin, vil de få tilgang til Admin panel lenke i rollen sin.
    if($is_admin){
        ?><p><a href="admin.php">Administrator</a></p><?php

    // Dersom bruker ikke er admin, vil de få listet opp sin rolle vanlig    
    }else{
        $sql = $mysqli->prepare("CALL getUserRoles(?)");
        $sql->bind_param("i", $_SESSION["s_bruker_id"]);
        $sql->execute();
        $role_result = $sql->get_result();
        $mysqli -> next_result();

        if ($role_result->num_rows > 0) {
            while($row = $role_result->fetch_assoc()) {
                echo $row["role_name"];
            }
        }
    }
    ?>

<!--Dersom ingen bruker er logget inn med session, blir de sendt til login.php siden, slik at de slipper å snoope i kilden-->
<?php else: ?>
    <?php $logger->info("INDEX PAGE: Someone attempted to access page without valid authentication"); ?>
    <?php header("Location: login.php");?>
<?php endif; ?>


<?php include 'inc/footer.php'; ?>