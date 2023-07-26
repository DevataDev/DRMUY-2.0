<?php
require_once 'conn/conn.php';

if (isset($_GET['channelId'])) {
    $channelId = $_GET['channelId'];
    $query = "SELECT pidm3u8 FROM canales WHERE id = :channelId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':channelId', $channelId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $pidM3U8 = $result['pidm3u8'];
    $isRunning = false;

    if ($pidM3U8) {
        $isRunning = file_exists("/proc/$pidM3U8");
    }
    echo $isRunning ? '1' : '0';
}
?>
