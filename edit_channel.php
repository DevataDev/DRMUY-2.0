<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRMUY</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-900">
<?php
require_once 'conn/conn.php';
require_once 'auth.php';
include "menu.php";
if (isset($_GET['channelId'])) {
    $channelId = $_GET['channelId'];
} else {
    echo "ID NOT IN URL.";
    exit;
}
$stmt = $db->prepare("SELECT * FROM canales WHERE id = ?");
$stmt->bindParam(1, $channelId);
$stmt->execute();
$channel = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$channel) {
    echo "Channel are not in the DB.";
    exit;}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $m3u8Dir = $_POST['m3u8Dir'];
    $tmpDir = $_POST['tmpDir'];
    $keyU = $_POST['keyU'];
    $keyID = $_POST['keyID'];
    $proxy = empty($_POST['proxy']) ? null : $_POST['proxy'];
    $useProxy = $_POST['useProxy'];
    $url = $_POST['url'];
    $pidm3u8 = empty($_POST['pidm3u8']) ? null : $_POST['pidm3u8']; 
    $time_started = empty($_POST['time_started']) ? null : $_POST['time_started']; 
    $video = $_POST['video'];
    $audio = $_POST['audio'];
    $subtitle = $_POST['subtitle'];

    $stmt = $db->prepare("UPDATE canales SET name=?, m3u8Dir=?, tmpDir=?, keyU=?, keyID=?, proxy=?, useProxy=?, url=?, pidm3u8=?, time_started=?, video=?, audio=?, subtitle=? WHERE id=?");
    $stmt->execute([$name, $m3u8Dir, $tmpDir, $keyU, $keyID, $proxy, $useProxy, $url, $pidm3u8, $time_started, $video, $audio, $subtitle, $channelId]);
    header("Location: all_channel.php");
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    exit;
}
if($channel['useProxy'] === 'true'){
    $command = './bin/yt-dlp --list-formats --allow-unplayable-formats --proxy ' . $channel['proxy'] .' ' . $channel['url'] ;
}else{
    $command = './bin/yt-dlp --list-formats --allow-unplayable-formats ' . $channel['url'] ;
}

$output = shell_exec($command);
$matches = [];
$pattern = '/(\w+)\s+(\w+)\s+(\w+)\s+(\w+)\s+\|\s+(\d+k)\s+(dash)/';
preg_match_all($pattern, $output, $matches, PREG_SET_ORDER);
?>
<div class="p-4 sm:ml-64">
    <div class="container mx-auto p-4 grid grid-cols-2 gap-4">
        <div class="col-span-2 sm:col-span-1">
            <h1 class="text-2xl font-bold mb-4 text-gray-500">Edit Channel></h1>
            <div class="bg-gray-800 p-4 rounded-lg shadow-md">
                <form method="POST">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-300">Name:</label>
                        <input type="text" name="name" value="<?php echo $channel['name']; ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="m3u8Dir" class="block text-gray-300">m3u8Dir:</label>
                        <input type="text" name="m3u8Dir" value="<?php echo $channel['m3u8Dir']; ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="tmpDir" class="block text-gray-300">tmpDir:</label>
                        <input type="text" name="tmpDir" value="<?php echo $channel['tmpDir']; ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="keyU" class="block text-gray-300">keyU:</label>
                        <input type="text" name="keyU" value="<?php echo $channel['keyU']; ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="keyID" class="block text-gray-300">keyID:</label>
                        <input type="text" name="keyID" value="<?php echo $channel['keyID']; ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="proxy" class="block text-gray-300">Proxy:</label>
                        <input type="text" name="proxy" value="<?php echo $channel['proxy']; ?>"  class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="useProxy" class="block text-gray-300">Use Proxy:</label>
                        <select name="useProxy" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                            <option value="true" <?php if ($channel['useProxy'] === 'true') echo 'selected'; ?>>True</option>
                            <option value="false" <?php if ($channel['useProxy'] === 'false') echo 'selected'; ?>>False</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="url" class="block text-gray-300">URL:</label>
                        <input type="text" name="url" value="<?php echo $channel['url']; ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="pidm3u8" class="block text-gray-300">pidm3u8:</label>
                        <input type="number" name="pidm3u8" value="<?php echo $channel['pidm3u8']; ?>"  class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="time_started" class="block text-gray-300">Time Started:</label>
                        <input type="text" name="time_started" value="<?php echo $channel['time_started'] ? date('Y-m-d H:i:s', strtotime($channel['time_started'])) : ''; ?>" class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="video" class="block text-gray-300">Video:</label>
                        <input type="text" name="video" value="<?php echo htmlspecialchars($channel['video']); ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="audio" class="block text-gray-300">Audio:</label>
                        <input type="text" name="audio" value="<?php echo htmlspecialchars($channel['audio']); ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="subtitle" class="block text-gray-300">Subtitle:</label>
                        <input type="text" name="subtitle" value="<?php echo htmlspecialchars($channel['subtitle']); ?>" required class="w-full bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-gray-300 focus:outline-none focus:border-blue-500">
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Save</button>
                </form>
            </div>
        </div>
        <div class="col-span-2 sm:col-span-1">
            <h1 class="text-2xl font-bold mb-4 text-gray-500">Channel Details</h1>
            <div class="bg-gray-800 p-4 rounded-lg shadow-md">
                <div class="w-full bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="w-1/6 px-4 py-3 text-left text-gray-300">ID</th>
                                <th class="w-1/6 px-4 py-3 text-left text-gray-300">EXT</th>
                                <th class="w-1/6 px-4 py-3 text-left text-gray-300">RESOLUTION</th>
                                <th class="w-1/6 px-4 py-3 text-left text-gray-300">TBR</th>
                                <th class="w-1/6 px-4 py-3 text-left text-gray-300">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($matches)) {
                                foreach ($matches as $match) {
                                    echo "<tr class='bg-gray-700'>
                                        <td class='px-4 py-3 text-gray-300'>{$match[1]}</td>
                                        <td class='px-4 py-3 text-gray-300'>{$match[2]}</td>
                                        <td class='px-4 py-3 text-gray-300'>{$match[3]}</td>
                                        <td class='px-4 py-3 text-gray-300'>{$match[5]}</td>
                                        <td class='px-4 py-3'>";
                                    if (strtolower($match[3]) === 'audio') {
                                        echo "<button class='select-button px-3 py-1 bg-blue-900 text-white rounded-md hover:bg-gray-600 focus:outline-none' data-value='{$match[1]}'>SEND</button>";
                                    } else {
                                        echo "<button class='select-button px-3 py-1 bg-gray-900 text-white rounded-md hover:bg-gray-600 focus:outline-none' data-value='{$match[1]}'>SEND</button>";
                                    }
                                    echo "</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='px-4 py-3 text-gray-300'>No data find - check proxy or url.</td></tr>";
                            }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const selectButtons = document.querySelectorAll('.select-button');
    selectButtons.forEach(button => {
        button.addEventListener('click', () => {
            const value = button.getAttribute('data-value');
            if (button.classList.contains('bg-blue-900')) {
                document.querySelector('input[name="audio"]').value = `"${value}"`;
            } else {
                document.querySelector('input[name="video"]').value = `"${value}"`;
            }
        });
    });
</script>
</script>
</body>
</html>