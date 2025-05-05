<?php

$host = 'localhost';
$db = 'qrschools';
$user = 'private_info_user';
$password = 'privateuser';


try{

    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
    exit;

}
?>
