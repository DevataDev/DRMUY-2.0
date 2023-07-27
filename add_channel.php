<?php
require_once 'conn/conn.php';
require_once 'auth.php';

// Función para insertar un nuevo canal en la base de datos
function insertCanal($db, $name, $m3u8Dir, $tmpDir, $keyU, $keyID, $proxy, $useProxy, $url, $video, $audio, $subtitle) {
    $query = "INSERT INTO canales (name, m3u8Dir, tmpDir, keyU, keyID, proxy, useProxy, url, video, audio, subtitle) VALUES (:name, :m3u8Dir, :tmpDir, :keyU, :keyID, :proxy, :useProxy, :url, :video, :audio, :subtitle)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':m3u8Dir', $m3u8Dir, PDO::PARAM_STR);
    $stmt->bindValue(':tmpDir', $tmpDir, PDO::PARAM_STR);
    $stmt->bindValue(':keyU', $keyU, PDO::PARAM_STR);
    $stmt->bindValue(':keyID', $keyID, PDO::PARAM_STR);
    $stmt->bindValue(':proxy', $proxy, PDO::PARAM_STR);
    $stmt->bindValue(':useProxy', $useProxy, PDO::PARAM_STR);
    $stmt->bindValue(':url', $url, PDO::PARAM_STR);
    $stmt->bindValue(':video', $video, PDO::PARAM_STR);
    $stmt->bindValue(':audio', $audio, PDO::PARAM_STR);
    $stmt->bindValue(':subtitle', $subtitle, PDO::PARAM_STR);
    return $stmt->execute();
}

// Procesar el formulario de agregar canal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $m3u8Dir = $_POST['m3u8Dir'];
    $tmpDir = $_POST['tmpDir'];
    $keyU = $_POST['keyU'];
    $keyID = $_POST['keyID'];
    $proxy = $_POST['proxy'];
    $useProxy = $_POST['useProxy'];
    $url = $_POST['url'];
    $video = $_POST['video'];
    $audio = $_POST['audio'];
    $subtitle = $_POST['subtitle'];

    // Insertar el nuevo canal en la base de datos
    insertCanal($db, $name, $m3u8Dir, $tmpDir, $keyU, $keyID, $proxy, $useProxy, $url, $video, $audio, $subtitle);

    // Redireccionar a la página de lista de canales después de la inserción
    header("Location: all_channel.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRMUY</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-900">
    <?php include "menu.php"; ?>
    <div class="p-4 sm:ml-64">
        <div class="container mx-auto p-4">
            <div class="container mx-auto mt-8 text-gray-400">
                <h1 class="text-2xl font-bold mb-4">Agregar Canal</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-500 font-bold">Name</label>
                        <input type="text" name="name" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                    </div>
                    <div>
                        <label class="block text-gray-500 font-bold">M3U8 Directory</label>
                        <input type="text" name="m3u8Dir" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required value="m3u8" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-500 font-bold">Temporary Directory</label>
                        <input type="text" name="tmpDir" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required value="temp" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-500 font-bold">Proxy</label>
                        <input type="text" name="proxy" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                    </div>
                    <div>
                        <label class="block text-gray-500 font-bold">KeyID</label>
                        <input type="text" name="keyID" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                    </div>
                    <div>
                        <label class="block text-gray-500 font-bold">KeyU</label>
                        <input type="text" name="keyU" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                    </div>
                    <div>
                        <label class="block text-gray-500 font-bold">Use Proxy</label>
                        <select name="useProxy" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white">
                            <option value="true">True</option>
                            <option value="false">False</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-500 font-bold">URL</label>
                        <input type="text" name="url" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-500 font-bold">Video</label>
                        <input type="text" name="video" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required value="best">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-500 font-bold">Audio</label>
                        <input type="text" name="audio" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required value="all">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-500 font-bold">Subtitle</label>
                        <input type="text" name="subtitle" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required value="all">
                    </div>
                    <div class="col-span-2 flex justify-end">
                        <button type="submit" name="submit"  class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium focus:outline-none rounded-lg border border-gray-200 focus:z-10 focus:ring-4 focus:ring-gray-700 bg-gray-800 text-gray-400 border-gray-600 hover:text-white hover:bg-gray-700 w-full" >Add Canal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tippy.js@6.3.1/dist/tippy-bundle.umd.min.js"></script>
</body>
</html>
