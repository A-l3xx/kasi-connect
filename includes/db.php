<?php

$host = "sql201.infinityfree.com";
$dbname = "if0_42027382_kasiconnect";
$username = "if0_42027382";
$password = "AxelFon17";

try{
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(PDOException $e){

    die("Database connection failed: " . $e->getMessage());

}
?>