<?php

//MySQL connection variables
$hostname = 'localhost';
$user = 'rradmin';
$pw = 'adminpw';
$database = 'rhythmrandomizer';

//Connect to database
try {
    $db = new PDO('mysql:host=' . $hostname . ';dbname=' . $database,$user,$pw);
} catch(PDOException $e) {
    echo $e->getMessage();
    die();
}

?>
