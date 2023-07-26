<?php
require_once 'conn/conn.php';
require_once 'auth.php';
$query = "SELECT * FROM canales";
$stmt = $db->query($query);
$canales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRMUY</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link href="https://vjs.zencdn.net/7.15.4/video-js.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-900">
<?php include "menu.php"; ?>
<div class="p-4 sm:ml-64">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4 text-gray-500">Users</h1>
        <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
        <div class="flex items-center justify-center bg-gray-900">
            <div class="col-span-12">

            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tippy.js@6.3.1/dist/tippy-bundle.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://vjs.zencdn.net/7.15.4/video.min.js"></script>
</body>
</html>
