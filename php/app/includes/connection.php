<?php    
    date_default_timezone_set('Asia/Manila');
    // Change this based on the config of your db in server
    $dbHostname = "db";
    $dbUsername = "user";
    $dbPassword = "password";
    $dbDatabaseName = "clientmsdb";

    $conn = new mysqli($dbHostname, $dbUsername, $dbPassword, $dbDatabaseName) or die ("Mysql Error : Could not connect to the database !!");
?>
