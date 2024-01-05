<?php
require_once("database.php");
require_once("functions.php");
require_once 'logger.php';

$mysqli = getConnection(0,0);
$files_dir = __DIR__ . "/../img/";
function uploadImg($picture)
{
    global $files_dir;
    global $logger;
    $accepted_types = ['jpg', 'jpeg', 'png'];
    $accepted_MIMES = ['image/jpeg', 'image/png', 'image/jpg'];
    // Matches the current php.ini (2MB max), just redundancy checking
    // Sad thing is that the we cannot get the actual file size unless apache2's enforcing
    // fails (size = 0 if denied at apache2 boundary), but better safe than sorry, I suppose.
    $max_size = 2000000;
    $logger ->info("Upload with information: " . print_r($picture, true));
    $picture_extension = pathinfo($picture['name'], PATHINFO_EXTENSION);

    if($picture['size'] > $max_size) {
        $logger->info("Max size exceeded, observed size {$picture['size']}");
        return "fetchFile.php?UUID=default";
    }

    $mime_type = determineMIME($picture);
    if (in_array($picture_extension, $accepted_types) && in_array($mime_type, $accepted_MIMES)) {
        $picture_name = time() . uniqid(rand());
        $path_to_be_written = $files_dir . $picture_name . '.' . $picture_extension;
        rebuildIMG($mime_type, $picture["tmp_name"], -3, $path_to_be_written);
        $UUID = writeInfoToDB($picture['name'], $picture_name, $mime_type, $picture_extension);
        $photo_path = "fetchFile.php?UUID=" . $UUID;
    } else {
        $logger->info("Unaccepted image type was observed, claimed extension: {$picture_extension}, mime_type: {$mime_type}");
        $photo_path = "fetchFile.php?UUID=default";
    }
    return $photo_path;
}

/**
 * @param $picture
 * @return array
 */
function determineMIME($picture)
{
    if (!empty($picture['name']) && !empty($picture['type'])) {
        if ($finfo = finfo_open(FILEINFO_MIME_TYPE)) {
            $mime_type = finfo_file($finfo, $picture['tmp_name']);
        }
    } else {
        $mime_type = "unknown";
    }
    return $mime_type;
}

function writeInfoToDB($originalName, $generated_name, $mime_type, $claimed_extension)
{
    global $mysqli;
    $sql = $mysqli->prepare("CALL uploadPhoto(?,?,?,?)");
    $sql->bind_param("ssss", $generated_name, $originalName, $mime_type, $claimed_extension);
    $sql->execute();
    return $sql->get_result()->fetch_row()[0];
}

function getPicture($uuid)
{
    global $mysqli;
    global $files_dir;
    $sql = $mysqli->prepare("CALL getPictureInfoUUID(?)");
    $sql->bind_param("s", $uuid);
    $sql->execute();
    $picture = $sql->get_result()->fetch_assoc();
    $filepath = $files_dir . $picture['stored_name'] . '.' . $picture['claimed_type'];
    if (file_exists($filepath)) {
        rebuildIMG($picture['mime_type'], $filepath, 0, null);
    }
}

/**
 * @param $mime_type
 * @param $filepath
 * @return void
 */
function rebuildIMG($mime_type, $filepath, $shiftValue, $path_to_beWritten)
{
    switch ($mime_type) {
        case 'image/jpeg':
        {
            header('Content-type: image/jpg');
            $image = imagecreatefromjpeg($filepath);
            imagefilter($image, IMG_FILTER_BRIGHTNESS, $shiftValue);
            imagejpeg($image, $path_to_beWritten);
            imagedestroy($image);
            break;
        }
        case 'image/png':
        {
            header('Content-type: image/png');
            $image = imagecreatefrompng($filepath);
            imagefilter($image, IMG_FILTER_BRIGHTNESS, $shiftValue);
            imagepng($image, $path_to_beWritten);
            imagedestroy($image);
            break;
        }
    }
}

?>