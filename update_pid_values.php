<?php
require_once 'conn/conn.php';
require_once 'auth.php';

if (isset($_GET['channelId'])) {
    $channelId = $_GET['channelId'];

    // Retrieve the latest PID M3U8 values from the server for the specified channel ID
    $stmt = $db->prepare("SELECT pidm3u8 FROM canales WHERE id = :channelId");
    $stmt->bindParam(':channelId', $channelId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Prepare the response as JSON
    $response = [
        'pidM3U8' => $row['pidm3u8']
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle the case when the channelId parameter is not provided
    echo 'Invalid request';
}
?>
