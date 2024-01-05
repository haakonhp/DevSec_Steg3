<?php
function determineRole($mysqli, $guest_allow_as_user)
{
    if (isset($_SESSION['s_subject_id']) && $guest_allow_as_user == 1) {
        return 1;
    }
    if (isset($_SESSION['s_bruker_id'])) {
        $sql = $mysqli->prepare("CALL getUserRoles(?)");
        $sql->bind_param("i", $_SESSION['s_bruker_id']);
        $sql->execute();
        return $sql->get_result()->fetch_assoc()["role_id"];
    }
    return 0;
}

function createIFrame($rawHTML, $height, $class, $allow, $blockScrolling, $escapeDoubleQuotes)
{
    $addClass = (!(empty($class)) ? "class='$class'" : '');
    $blockScrolling = (!(empty($blockScrolling)) ? "scrolling='no'" : '');
    if($escapeDoubleQuotes) {
       $rawHTML = htmlspecialchars($rawHTML, ENT_COMPAT);
    }

    return "<iframe sandbox='$allow' height='$height' $addClass $blockScrolling
            srcdoc=\"
             <link rel='stylesheet' href='styles/water_local.css'>
             <link rel='stylesheet' href='styles/index_style.css'>
             <link rel='stylesheet' href='styles/emnestyles.css'>
            $rawHTML\"></iframe>";
}

function createNewButton() {
    return "<button class='createNewButton'><label for='createNewCheck' class='createNewLabel'>Create new comment</label></button>
            <input type='checkbox' id='createNewCheck' class='hidden'>";
}

// Text generation
function appendReplyForm($id, $room)
{
    return "<form hidden method='post' class='inputForm' id='replyform{$id}' action='emne_submit.php'>
                <input type='hidden' name='roomRedirect' value='{$room}'>
                <input type='hidden' name='reply_id' value='{$id}'>
                <input type='text' name='text'>
                <input type='submit' name='submit' value='Reply'>
            </form>";
}

function appendCloseButton($id)
{
    return "<button class='closeButton'><label for='closeButton{$id}'>x</label></button>
            <input type='radio' id='closeButton{$id}' name='button{$id}' class='closeButtonHidden'>";
}

function createMessage($comment_text, $comment_name, $depth, $img_link)
{
    return "<article class='message' style='margin-left: calc({$depth} * 50px);'>
            $img_link 
            <p>{$comment_name}: {$comment_text}</p>";
}


function createReplyButton($id, $onclick)
{
    $replyClick = "var hiddenValue = document.getElementById('replyform{$id}').hidden.valueOf();
            document.getElementById('replyform{$id}').hidden = !hiddenValue;
            document.getElementById('reportform{$id}').hidden = true;";
    if ($onclick == 1) {
        return "<button class='replyButton' id='replyButton{$id}' onclick=\"$replyClick\">Reply</button>";
    } else {
        return "<button class='replyButton'><label for='replyButton{$id}'>Reply</label></button>
            <input type='radio' id='replyButton{$id}' name='button{$id}' class='hidden'>";
    }
}

function createReportButton($id, $onclick)
{
    $reportClick = "var hiddenValue = document.getElementById('reportform{$id}').hidden.valueOf();
            document.getElementById('reportform{$id}').hidden = !hiddenValue;
            document.getElementById('replyform{$id}').hidden = true;";
    if ($onclick == 1) {
        return "<button class='reportButton' id='reportButton{$id}' onclick=\"$reportClick\">Report</button>";
    } else {
        return "<button class='reportButton'><label for='reportButton{$id}'>Report</label></button>
            <input type='radio' id='reportButton{$id}' name='button{$id}' class='hidden'>";
    }
}

function createReportForm($id, $room)
{
    return "<form hidden method='post' class='inputForm'  id='reportform{$id}' action='emne_submit.php'>
                <input type='hidden' name='roomRedirect' value='{$room}'>
                <input type='hidden' name='reply_id' value='{$id}'>
                <input type='text' name='text'>
                <input type='submit' name='submit' value='report'>
            </form>";
}

function getIPAddress() {
    //whether ip is from the share internet
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from the proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    //whether ip is from the remote address
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // Filter the IP address
    $ip = filter_var($ip, FILTER_VALIDATE_IP);
    return $ip;
}


?>