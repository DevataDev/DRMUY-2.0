<?php
// Incluir el archivo de conexión a la base de datos
require_once 'conn/conn.php';
require_once 'auth.php';
// Verificar si se ha recibido el parámetro channelId
if (isset($_GET['channelId'])) {
    $channelId = $_GET['channelId'];

    // Obtener la información del canal de la base de datos
    $stmt = $db->prepare("SELECT time_started FROM canales WHERE id = :channelId");
    $stmt->bindParam(':channelId', $channelId);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró el canal en la base de datos
    if ($row) {
        $timeStarted = $row['time_started']; // Obtener el valor de time_started
        
        if ($timeStarted) {
            // Convertir el valor de time_started a un timestamp de Unix
            $timeStartedTimestamp = strtotime($timeStarted);
    
            // Obtener el tiempo actual y calcular el tiempo transcurrido
            $currentTime = time();
            $runningTime = $currentTime - $timeStartedTimestamp;
    
            // Calcular los días, horas, minutos y segundos a partir del tiempo transcurrido
            $days = floor($runningTime / (60 * 60 * 24));
            $hours = floor(($runningTime % (60 * 60 * 24)) / (60 * 60));
            $minutes = floor(($runningTime % (60 * 60)) / 60);
            $seconds = $runningTime % 60;
    
            // Formatear la variable de tiempo transcurrido como "hh:mm:ss"
            $timeRunning = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    
            // Devolver el tiempo transcurrido como respuesta
            echo $timeRunning;
        } else {
            // Si time_started es nulo, devolver un mensaje de tiempo no disponible
            echo "Not available";
        }
    } else {
        // Si no se encuentra el canal en la base de datos, devolver un mensaje de error
        echo "Channel not found";
    }
} else {
    // Si no se ha recibido el parámetro channelId, devolver un mensaje de error
    echo "Invalid request";
}
?>
