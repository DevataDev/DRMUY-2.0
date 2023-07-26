<?php
// Include the database connection file
include '../conn/conn.php';
//require_once '../auth.php';
// Check if the channel ID is provided
if (!isset($_POST['channelId'])) {
    // Handle the case when the channel ID is not provided
    echo 'Channel ID not provided.';
    exit();
}
// Get the channel ID from the POST data
$channelId = $_POST['channelId'];
// Consultar la información del canal en la base de datos
$stmt = $db->prepare("SELECT * FROM canales WHERE id = :channelId");
$stmt->bindParam(':channelId', $channelId);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener los valores de las columnas
$name = $row['name'];
$m3u8Dir = $row['m3u8Dir'];
$tmpDir = $row['tmpDir'];
$key = $row['keyU'];
$keyID = $row['keyID'];
$proxy = $row['proxy'];
$useProxy = $row['useProxy'];
$url = $row['url'];
$video = $row['video'];
$audio = $row['audio'];
$subtitle = $row['subtitle'];

// Obtener local
$binDir = __DIR__;

// Crear la carpeta de m3u8Dir si no existe

// Ejecutar el primer comando para descargar el archivo .ts
$command1 = 'RE_LIVE_PIPE_OPTIONS="-c copy -f hls -hls_time 8 -hls_flags append_list+delete_segments -hls_list_size 4 /www/wwwroot/goplaytv.fun/bin/m3u8/dazzzzzzzz/index.m3u8" ' .
    './N_m3u8DL-RE ' .
    '--live-real-time-merge ' .
    '--live-keep-segments false ' .
    '--live-wait-time 2 ' .
    '--use-system-proxy ' . $useProxy . ' ' .
    '--custom-proxy ' . $proxy . ' ' .
    '--use-shaka-packager ' .
    '--thread-count 4 ' .
    '--del-after-done ' .
    '--tmp-dir ' . $tmpDir . '/' . $name . ' ' .
    '--live-pipe-mux ' .
    '-sa ' . $audio . ' ' . 
    '-sv ' . $video . ' ' .   
    '-ss ' . $subtitle . ' ' .
    '"' . $url . '" ' .
    '--header "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36" ' .
    '--key ' . $key . ':' . $keyID . ' > /dev/null 2>&1 & echo $!';




$output1 = shell_exec($command1);
$pid1 = intval($output1);

// Get the current date and time
$currentDateTime = date('Y-m-d H:i:s');

// Update the PID and start time in the database
$stmt = $db->prepare("UPDATE canales SET pidm3u8 = :pidm3u8, time_started = :timeStarted WHERE id = :channelId");
$stmt->bindParam(':pidm3u8', $pid1);
$stmt->bindParam(':timeStarted', $currentDateTime);
$stmt->bindParam(':channelId', $channelId);
$stmt->execute();
echo $command1;
?>
