<?php
require_once("database.php");
require("check.php");

function doesCurrentUserHaveSubject($room, $userID, $guestSubjectID)
{
    $mysqli = getConnection(0,0);
    $valid = FALSE;

    if (isset($userID)) {
        // Utfører SQL spørring for å se etter brukerens emner
        $sql =
            "CALL doesUserHaveSubject({$userID}, {$room})";
        $row = $mysqli->query($sql);
        $valid = ($row->fetch_row()[0] == 1);

        $row->free_result();
        $mysqli->next_result();
        // For gjest_bruker med pin_kode. Henter allerede subject_id fra SESSION.
    } elseif (isset($guestSubjectID)) {
        if ($guestSubjectID == $room) {
            $valid = TRUE;
        }
    } else {
        header("Location: login.php");
    }
    return $valid;
}

?>