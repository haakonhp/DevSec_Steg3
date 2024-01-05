<?php
declare(strict_types=1);
require __DIR__ .'/vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler(__DIR__.'/log_file.log', Logger::DEBUG));

// add records to the log
$log->info('It works!');
