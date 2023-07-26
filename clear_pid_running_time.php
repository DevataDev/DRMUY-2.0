<?php
require_once 'conn/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['channelId']) && is_numeric($_POST['channelId'])) {
        $channelId = $_POST['channelId'];

        // Check if the channel exists in the database
        $stmt = $db->prepare('SELECT * FROM canales WHERE id = :channelId');
        $stmt->bindParam(':channelId', $channelId, PDO::PARAM_INT);
        $stmt->execute();
        $channel = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($channel) {
            // Clear pidm3u8 and time_started fields
            $stmt = $db->prepare('UPDATE canales SET pidm3u8 = NULL, time_started = NULL WHERE id = :channelId');
            $stmt->bindParam(':channelId', $channelId, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true]);
            exit;
        }
    }
}

// If the request is invalid or the channel doesn't exist, return an error response
echo json_encode(['success' => false, 'message' => 'Invalid channel ID']);
