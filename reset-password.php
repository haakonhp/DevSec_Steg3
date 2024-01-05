<?php

include 'inc/header.php';

?>


<main>
    <div class="wrapper-main">
        <section class="section-default">
            <h1>Glemt passord?</h1>
            <p> Email will be yeeted in your general direction.</p>
            <form action="reset-request.php" method="post">
                <input type="text" name="email" placeholder="Skriv inn din email.">
                <button type="submit" name="reset-request-submit">Få tilsendt link</button>
            </form>
            <?php
              if (isset($_GET["reset"])) {
                if ($_GET["reset"] == "request_sent") {
                    echo '<p class="signupsuccess"> Hvis brukeren eksisterer, vil du få en e-post.<br>Det kan ta opp til 3 minutter.</p>';
                }


              }
            ?>


        </section>
    </div>
</main>

<?php
require "inc/footer.php";