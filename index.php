<?php
require_once 'conn/conn.php';
require_once 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>DRMUY</title>

      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
      <link rel="stylesheet" href="style/style.css">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />

   </head>
   <body class="bg-gray-900">
      <?php include "menu.php"; ?>
 
<div class="p-4 sm:ml-64">
  <div class="container mx-auto p-4">
    <div class="flex flex-wrap -mx-2 mb-4">
      <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/3 xl:w-1/4 px-2 mb-4">
        <div class="flex justify-between">
          <div class="w-1/3 bg-gray-800 hover:bg-gray-900 rounded-lg p-4 text-center min-w-200 h-44 m-2">
            <h2 class="text-2xl text-white mb-2">Network Speed</h2>
            <p id="networkSpeed" class="text-4xl text-white"></p>
          </div>
          <div class="w-1/3 bg-gray-800 hover:bg-gray-900 rounded-lg p-4 text-center min-w-200 h-44 m-2">
            <h2 class="text-2xl text-white mb-2">RAM  Usage</h2>
            <p id="ramUsage" class="text-4xl text-white"></p>
          </div>
          <div class="w-1/3 bg-gray-800 hover:bg-gray-900 rounded-lg p-4 text-center min-w-200 h-44 m-2">
            <h2 class="text-2xl text-white mb-2">CPU  Usage</h2>
            <p id="cpuUsage" class="text-4xl text-white"></p>
          </div>
        </div>
      </div>
      <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/3 xl:w-1/4 px-2 mb-4">
        <div class="flex justify-between">
          <div class="w-1/3 bg-gray-800 hover:bg-gray-900 rounded-lg p-4 text-center min-w-200 h-44 m-2">
            <h2 class="text-2xl text-white mb-2">Disk Utilization</h2>
            <p id="diskUtilization" class="text-4xl text-white"></p>
          </div>
          <div class="w-1/3 bg-gray-800 hover:bg-gray-900 rounded-lg p-4 text-center min-w-200 h-44 m-2">
            <h2 class="text-2xl text-white mb-2">Watching Users</h2>
            <p id="onlineUsers" class="text-4xl text-white"></p>
          </div>
          <div class="w-1/3 bg-gray-800 hover:bg-gray-900 rounded-lg p-4 text-center min-w-200 h-44 m-2">
            <h2 class="text-2xl text-white mb-2">Channels Played</h2>
            <div id="channelsPlayed" class="flex flex-col items-center">
              <p id="channelsPlayedCount" class="text-4xl text-white"></p>
              <p class="text-sm text-gray-400">Online Channels</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>







      <script>
      
         // Function to update the online users count
         function updateOnlineUsersCount(data) {
            var onlineUsersElem = document.getElementById('onlineUsers');
            var onlineUsersCount = data.onlineUsersCount;
            onlineUsersElem.textContent = onlineUsersCount;
         }

         // Function to update the channels played count
         function updateChannelsPlayedCount(data) {
            var channelsPlayedCountElem = document.getElementById('channelsPlayedCount');
            var channelsPlayedCount = data.channelsPlayedCount;
            var totalChannelsCount = data.totalChannelsCount;

            channelsPlayedCountElem.textContent = channelsPlayedCount + ' / ' + totalChannelsCount;
         }

         // Function to update the network speed
         function updateNetworkSpeed(data) {
            var networkSpeedElem = document.getElementById('networkSpeed');
            var networkUsage = data.network.toFixed(1);
            networkSpeedElem.textContent = networkUsage + ' M/s';
         }

         // Function to update the RAM usage
         function updateRamUsage(data) {
            var ramUsageElem = document.getElementById('ramUsage');
            var ramUsage = data.ram.toFixed(1); // Limit to 1 decimal place
            ramUsageElem.textContent = ramUsage + '%';
            ramUsageElem.style.color = getUsageColor(ramUsage);
         }

         // Function to update the CPU usage
         function updateCpuUsage(data) {
            var cpuUsageElem = document.getElementById('cpuUsage');
            var cpuUsage = data.cpu.toFixed(1); // Limit to 1 decimal place
            cpuUsageElem.textContent = cpuUsage + '%';
            cpuUsageElem.style.color = getUsageColor(cpuUsage);
         }

         // Function to update the disk utilization
         function updateDiskUtilization(data) {
            var diskUtilizationElem = document.getElementById('diskUtilization');
            var diskUtilization = data.diskUtilization.toFixed(1); // Limit to 1 decimal place
            diskUtilizationElem.textContent = diskUtilization + '%';
            diskUtilizationElem.style.color = getUsageColor(diskUtilization);
         }

         // Function to get the color based on the usage value
         function getUsageColor(usage) {
            if (usage <= 35) {
               return 'green';
            } else if (usage <= 70) {
               return 'yellow';
            } else {
               return 'red';
            }
         }

         // Function to fetch the data and update the divs
         function fetchDataAndUpdate() {
            $.getJSON('monitor.php', function(data) {
               updateNetworkSpeed(data);
               updateRamUsage(data);
               updateCpuUsage(data);
               updateDiskUtilization(data);
               updateChannelsPlayedCount(data);
            });
         }

         // Fetch the initial data and update the divs
         fetchDataAndUpdate();

         // Update the divs every 1 second
         setInterval(fetchDataAndUpdate, 1000);
      </script>
   </body>
</html>
