<?php
// Include the database connection file
include 'conn/conn.php';
require_once 'auth.php';

// Obtener el ID del canal a eliminar
$channelId = $_GET['channelId']; // Suponiendo que se pasará el ID del canal como parámetro en la URL

// Eliminar el canal de la base de datos
$stmt = $db->prepare("DELETE FROM canales WHERE id = :channelId");
$stmt->bindParam(':channelId', $channelId);
$stmt->execute();
// Redirigir a la página de canales después de eliminar el canal (puedes ajustar la ruta según tus necesidades)
header("Location: all_channel.php");
exit();
?>
