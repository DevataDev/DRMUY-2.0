<?php
// Include the database connection file
include '../conn/conn.php';
require_once '../auth.php';

// Check if the channel ID is provided
if (!isset($_POST['channelId'])) {
    // Handle the case when the channel ID is not provided
    echo 'Channel ID not provided.';
    exit();
}

// Get the channel ID from the POST data
$channelId = $_POST['channelId'];

// Query the database to fetch the PID values and the name column
$stmt = $db->prepare("SELECT pidm3u8, name FROM canales WHERE id = :channelId");
$stmt->bindParam(':channelId', $channelId);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$pidM3U8 = $row['pidm3u8'];
$name = $row['name']; // Get the value of the name column

// Check if the PIDs are valid
if ($pidM3U8) {
    // Execute the command to stop the N_m3u8DL-RE process
    $commandM3U8 = 'kill ' . $pidM3U8;
    exec($commandM3U8);
    
    // Update the database to clear the PID values and time_started
    $stmt = $db->prepare("UPDATE canales SET pidm3u8 = NULL, time_started = NULL WHERE id = :channelId");
    $stmt->bindParam(':channelId', $channelId);
    $stmt->execute();
    
    // Remove all temp files
    $binDir = __DIR__; // Get the absolute path of the current directory
    $folderPath = $binDir . '/../bin/temp/' . $name . '/'; // Construct the folder path using $name variable
    deleteFiles($folderPath);
}

// Function to recursively delete files and subdirectories
function deleteFiles($path) {
    foreach (glob(rtrim($path, "/") . '/*') as $file) {
        if (is_dir($file)) {
            deleteFiles($file);
        } else {
            unlink($file);
        }
    }
    rmdir($path);
}
?>
