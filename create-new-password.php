<?php
$title = "Create new password";
require("inc/header.php");
require("reset-request.php");
//2form for nytt passord + repeat
?>
    <main>
      <div class="wrapper-main">
        <section class="section-default">


            <?php
            //check tokens in url, validator check token in db
             $selector = $_GET["selector"];
             $validator = $_GET["validator"];


             //check if token is there. no token, no validate
            if(empty($selector) || empty($validator)) {
                echo("missing token here");

            } else {
                //check if selector and validator is valid hexa format
              if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false) {

                 //password x 2
                ?>
                 <form action="reset-password2.php" method="post">
                     <input type="hidden" name="selector" value="<?php echo $selector ?>">
                     <input type="hidden" name="validator" value="<?php echo $validator ?>">

                     <input type="password" name="pwd" placeholder="Skriv inn nytt pasord..">
                     <input type="password" name="pwd-repeat" placeholder="Gjenta nytt passord..">
                     <button type="submit" name="reset-password-submit">Bytt passord</button>
                 </form>
                 <?php

                }
            }

            ?>


        </section>
    </div>
</main>

    <?php
    require "inc/footer.php";

    ?>
