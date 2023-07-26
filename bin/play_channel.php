<?php
include '../conn/conn.php';

if (!isset($_POST['channelId'])) {
    echo 'Channel ID not provided.';
    exit();
}

$channelId = intval($_POST['channelId']);

$stmt = $db->prepare("SELECT * FROM canales WHERE id = :channelId");
$stmt->bindParam(':channelId', $channelId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo 'Channel not found.';
    exit();
}

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
$binDir = __DIR__;

$createDir = __DIR__ . '/' . $m3u8Dir;
if (!file_exists($createDir)) {
    mkdir($createDir, 0755, true); 
}

$command = 'rm -r ' . escapeshellarg($binDir . $tmpDir . '/' . $name . '/*');
shell_exec($command);

$command1 = 'RE_LIVE_PIPE_OPTIONS="-c copy -f hls -hls_time 8 -hls_flags append_list+delete_segments -hls_list_size 4 ' . $createDir  . '/index.m3u8" ' .
    './N_m3u8DL-RE ' .
    '--live-real-time-merge ' .
    '--live-keep-segments false ' .
    '--live-wait-time 8 ' .
    '--tmp-dir ' . $binDir .'/' . $tmpDir . '/' . $name . '/ ';

if ($useProxy === "true") {
    $command1 .= '--use-system-proxy ' . $useProxy . ' ' .
    '--custom-proxy ' . $proxy . ' ';
}

$command1 .= '--use-shaka-packager ' .
    '--thread-count 4 ' .
    '--del-after-done ' .
    '--live-pipe-mux ' .
    '--log-level WARN ' .
    '-sa ' . $audio . ' ' . 
    '-sv ' . $video . ' ' .   
    '-ss ' . $subtitle . ' ' .
    '"' . $url . '" ' .
    '--header "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36" ' .
    '--key ' . $key . ':' . $keyID . ' > /dev/null 2>&1 & echo $!';

$output1 = shell_exec($command1);
$pid1 = intval($output1);

$currentDateTime = date('Y-m-d H:i:s');
$stmt = $db->prepare("UPDATE canales SET pidm3u8 = :pidm3u8, time_started = :timeStarted WHERE id = :channelId");
$stmt->bindParam(':pidm3u8', $pid1, PDO::PARAM_INT);
$stmt->bindParam(':timeStarted', $currentDateTime);
$stmt->bindParam(':channelId', $channelId, PDO::PARAM_INT);
$stmt->execute();

echo $command1;
?>
