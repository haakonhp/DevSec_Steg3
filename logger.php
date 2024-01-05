<?php

if (isset($_SESSION["s_bruker_id"])) {
    $bruker_id = $_SESSION["s_bruker_id"];
} else {
    $bruker_id = "Unauthenticated";
}


require __DIR__ . '/vendor/autoload.php';
require_once("functions.php");

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\GelfHandler;
use Gelf\Message;
use Monolog\Formatter\GelfMessageFormatter;

$logger = new Logger('sikkerhet');

/* Legge til info i alle records */

$logger->pushProcessor(function ($record) use ($bruker_id) {
    $record['extra']['ip'] = getIpAddress();
    $record['extra']['user_id'] = $bruker_id;
    return $record;
});

/* Graylog / gelf */

$transport = new Gelf\Transport\UdpTransport("127.0.0.1", 12201 /*, Gelf\Transport\UdpTransport::CHUNK_SIZE_LAN*/);
$publisher = new Gelf\Publisher($transport);
$handler = new GelfHandler($publisher,Logger::DEBUG);

$logger->pushHandler($handler);

?>