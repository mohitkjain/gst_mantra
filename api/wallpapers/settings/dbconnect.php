<?php

function connect_db()
{
    $host = "localhost";
    $user = "technetw_wall_e";
    $pass = "J3#Tn78!7[Y^";
    $db_name = "technetw_wallpapers";
    try
    {
        $connection = new PDO("mysql:host=$host;dbname=$db_name", $user, $pass);
        // set the PDO error mode to exception
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }
    catch(PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage();
    }
}