<?php
$dbhost = 'localhost';
$dbname = 'canales';
$dbuser = 'canales';
$dbpass = '22333265Andre';

try {
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

?>