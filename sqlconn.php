<?php

//MySQL connection variables
$hostname = 'localhost';
$user = 'rradmin';
$pw = 'adminpw';
$database = 'rhythmrandomizer';

//Connect to database
$mysqli = new mysqli($hostname, $user, $pw, $database);

if ($mysqli->connect_errno) {
  echo 'Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error;
}
