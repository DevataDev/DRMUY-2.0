<?php
require_once 'conn/conn.php';

// Function to check if the PID is running
function isPIDRunning($pidM3U8)
{
    // Check if the PID exists and is running
    exec('ps -p ' . $pidM3U8, $output);
    return count($output) > 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['channelId']) && is_numeric($_GET['channelId'])) {
        $channelId = $_GET['channelId'];

        // Check if the channel exists in the database
        $stmt = $db->prepare('SELECT * FROM canales WHERE id = :channelId');
        $stmt->bindParam(':channelId', $channelId, PDO::PARAM_INT);
        $stmt->execute();
        $channel = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($channel) {
            $pidM3U8 = $channel['pidm3u8'];
            $timeStarted = $channel['time_started'];

            if ($pidM3U8 && isPIDRunning($pidM3U8)) {
                // If the PID exists and is running, return status as "running"
                $currentTime = time();
                $timeStartedTimestamp = strtotime($timeStarted);
                $runningTime = $currentTime - $timeStartedTimestamp;
                $hours = floor($runningTime / (60 * 60));
                $minutes = floor(($runningTime % (60 * 60)) / 60);
                $seconds = $runningTime % 60;
                $timeRunning = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                echo json_encode(['status' => 'running', 'pid' => $pidM3U8, 'runningTime' => $timeRunning]);
                exit;
            } else {
                // If the PID is not running or doesn't exist, return status as "stopped"
                echo json_encode(['status' => 'stopped', 'pid' => null, 'runningTime' => null]);
                exit;
            }
        }
    }
}

// If the request is invalid or the channel doesn't exist, return an error response
echo json_encode(['status' => 'error', 'message' => 'Invalid channel ID']);
