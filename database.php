<?php
include_once("functions.php");

function getInitialConnection() {
    $host = "localhost";
    $dbname = "group_project_step_2";
    //Initial connection
    $username = "php_register_user";
    $password = "]g*-ylQer!6&{w:S]{Q5(@TdR%sios";
    return new mysqli(hostname: $host, username: $username, password: $password, database: $dbname);
}

function getConnection($isAPI, $guest_allow_as_user)
{
    $host = "localhost";
    $dbname = "group_project_step_2";


    if($isAPI == 1) {
        $username = 'php_exec_api';
        $password = '`&5|D|&7i$~g`B8xwb{]d/Ufez0;5;';
        return new mysqli(hostname: $host, username: $username, password: $password, database: $dbname);
    }


    switch (determineRole(getInitialConnection(), $guest_allow_as_user)) {
        case 0: {
            $username = "php_register_user";
            $password = "]g*-ylQer!6&{w:S]{Q5(@TdR%sios";
            break;
        }
        case 1:
        case 2:
        case 3:
        {
            $username = "php_exec_user";
            $password = 't7Cx7(EO{=n=$CijSjWe7~u]o<!(Yj';
            break;
        }
        case 4: {
            $username = "php_exec_admin";
            $password = "%mIg(c{:C[Ns6v0&)$[/NvmJH8[-j:d";
            break;
        }
        default:
            return null;
    }
    // Lag en ny connection
    $mysqli = new mysqli(hostname: $host, username: $username, password: $password, database: $dbname);
    // Dersom det kommer opp feil, spesifiser med error
    if ($mysqli->connect_error) {
        die("Connection error: " . $mysqli->connect_error);
    }
    return $mysqli;
}







