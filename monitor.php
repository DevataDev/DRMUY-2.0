<?php
// Function to get CPU utilization percentage

include_once "conn/conn.php";
function getCPUUsage() {
   $cpuPercentage = trim(shell_exec('top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk \'{print 100 - $1}\''));
   $cpuPercentage = is_numeric($cpuPercentage) ? floatval($cpuPercentage) : 0.0;
   return $cpuPercentage;
}

// Function to get RAM usage in percentage
function getRAMUsage() {
   $ramTotal = trim(shell_exec("free | awk '/Mem:/ {print $2}'"));
   $ramUsed = trim(shell_exec("free | awk '/Mem:/ {print $3}'"));

   $ramTotal = is_numeric($ramTotal) ? floatval($ramTotal) : 0.0;
   $ramUsed = is_numeric($ramUsed) ? floatval($ramUsed) : 0.0;

   if ($ramTotal == 0) {
      return 0.0;
   }

   $ramPercentage = ($ramUsed / $ramTotal) * 100;
   return $ramPercentage;
}

// Function to calculate the network speed
function calculateNetworkSpeed() {
   $networkFile = '/sys/class/net/eth0/statistics/rx_bytes';
   $interval = 2; // Update interval in seconds

   $bytesStart = trim(file_get_contents($networkFile));
   sleep($interval);
   $bytesEnd = trim(file_get_contents($networkFile));

   $bytesDiff = $bytesEnd - $bytesStart;
   $speedMbps = round($bytesDiff * 8 / $interval / 1024 / 1024, 2); // Calculate speed in Mbps

   return $speedMbps;
}

// Function to get the disk utilization
function getDiskUtilization($directory) {
   $diskTotal = disk_total_space($directory);
   $diskFree = disk_free_space($directory);

   if ($diskTotal === false || $diskFree === false) {
      return 0.0;
   }

   $diskUtilization = ($diskTotal - $diskFree) / $diskTotal * 100;
   return $diskUtilization;
}

// Function to get the actual directory path and append "/bin" to it
function getActualBinDirectory() {
   $phpFileDir = __DIR__;
   $binDirectory = $phpFileDir . '/bin';
   return $binDirectory;
}

// Function to get the number of channels played
function getChannelsPlayedCount() {
   global $db;

   $query = "SELECT COUNT(*) AS played_count FROM canales WHERE pidm3u8 IS NOT NULL";
   $result = $db->query($query);
   $row = $result->fetch(PDO::FETCH_ASSOC);

   return $row['played_count'];
}

// Function to get the total number of channels
function getTotalChannelsCount() {
   global $db;

   $query = "SELECT COUNT(*) AS total_count FROM canales";
   $result = $db->query($query);
   $row = $result->fetch(PDO::FETCH_ASSOC);

   return $row['total_count'];
}

// Function to get the number of online users in specific folders
function getOnlineUsersCount() {
   $folderPath = __DIR__ . '/bin/m3u8/';
   $onlineUsersCount = 0;

   $folders = glob($folderPath . '*', GLOB_ONLYDIR);

   foreach ($folders as $folder) {
      $folderFiles = glob($folder . '/*.m3u8');

      foreach ($folderFiles as $file) {
         $fileContent = file_get_contents($file);
         preg_match_all('/#EXT-X-SESSION-KEY/', $fileContent, $matches);
         $onlineUsersCount += count($matches[0]);
      }
   }

   return $onlineUsersCount;
}

// Create an array to store the data
$data = array(
   'label' => date('Y-m-d H:i:s'),
   'cpu' => getCPUUsage(),
   'ram' => getRAMUsage(),
   'network' => calculateNetworkSpeed(),
   'diskUtilization' => getDiskUtilization(getActualBinDirectory()),
   'channelsPlayedCount' => getChannelsPlayedCount(),
   'totalChannelsCount' => getTotalChannelsCount(),
   'onlineUsersCount' => getOnlineUsersCount()
);

// Encode the data as JSON and output it
header('Content-Type: application/json');
echo json_encode($data);
exit(); // Add this line to exit the script
?>
