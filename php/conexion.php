<?php

$dbserver="localhost";
$dbusername="root";
$dbpassword="";
$dbbasedatos="bd_restaurante";
try {
    $conexion = new PDO("mysql:host=$dbserver;dbname=$dbbasedatos", $dbusername, $dbpassword);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: ". $e->getMessage();
    die();
}