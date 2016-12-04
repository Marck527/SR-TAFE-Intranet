<?php

    try {
        $oConn = new PDO("mysql:host=localhost; dbname=IntranetDBSchema; charset=utf8", 'root', 'Password1', array(PDO::ATTR_PERSISTENT=>TRUE));
    }
    catch(PDOException $e) {
        die("A database error has occured. " . $e->getMessage());
    }


//function dbConnect() {
//
//    $db_host = "127.0.0.1";
//    $db_name = "IntranetDBSchema";
//    $db_user = "root";
//    $db_password = "Password1";
//
//    $db_instance = null; //Holds the instance of the PDO object (database).
//
//    if ($db_instance == null) { //If the db_instance is empty, create a new instance of the database.
//
//        try {
//            $db_instance = new PDO("mysql:host=$db_host; dbname=$db_name; charset=utf8", $db_user, $db_password, array(PDO::ATTR_PERSISTENT=>TRUE));
//        }
//        catch(PDOException $e) {
//            die("A database error has occured. " . $e->getMessage());
//        }
//
//    }
//
//    return $db_instance; //if the there's already a database instance, return that instance.
//
//}
//
//$oConn = dbConnect();