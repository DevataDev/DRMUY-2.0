<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-md w-96">
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    $dbhost = $_POST['dbhost'];
    $dbname = $_POST['dbname'];
    $dbuser = $_POST['dbuser'];
    $dbpass = $_POST['dbpass'];
    function testDbConnection($dbhost, $dbname, $dbuser, $dbpass) {
        try {
            $db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    $dbConnectionSuccess = testDbConnection($dbhost, $dbname, $dbuser, $dbpass);
    if ($dbConnectionSuccess) {
        $connFileContent = '<?php' . PHP_EOL;
        $connFileContent .= '$dbhost = "' . $dbhost . '";' . PHP_EOL;
        $connFileContent .= '$dbname = "' . $dbname . '";' . PHP_EOL;
        $connFileContent .= '$dbuser = "' . $dbuser . '";' . PHP_EOL;
        $connFileContent .= '$dbpass = "' . $dbpass . '";' . PHP_EOL;
        $connFileContent .= PHP_EOL;
        $connFileContent .= 'try {' . PHP_EOL;
        $connFileContent .= '    $db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);' . PHP_EOL;
        $connFileContent .= '    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);' . PHP_EOL;
        $connFileContent .= '} catch (PDOException $e) {' . PHP_EOL;
        $connFileContent .= '    die("Error connecting to the database: " . $e->getMessage());' . PHP_EOL;
        $connFileContent .= '}' . PHP_EOL;
        $connFilePath = '../conn/conn.php';
        $connFile = fopen($connFilePath, 'w');
        fwrite($connFile, $connFileContent);
        fclose($connFile);
        require_once $connFilePath;
        $createTablesQuery = "
            SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
            SET AUTOCOMMIT = 0;
            START TRANSACTION;
            SET time_zone = '+00:00';

            CREATE TABLE `canales` (
              `id` int(11) UNSIGNED NOT NULL,
              `name` varchar(255) NOT NULL,
              `m3u8Dir` varchar(255) NOT NULL,
              `tmpDir` varchar(255) NOT NULL,
              `keyU` varchar(255) NOT NULL,
              `keyID` varchar(255) NOT NULL,
              `proxy` varchar(255) NOT NULL,
              `useProxy` enum('true','false') NOT NULL,
              `url` varchar(255) NOT NULL,
              `pidm3u8` int(11) DEFAULT NULL,
              `time_started` datetime DEFAULT NULL,
              `video` varchar(50) DEFAULT NULL,
              `audio` varchar(50) DEFAULT NULL,
              `subtitle` varchar(50) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            CREATE TABLE `users` (
              `id` int(11) UNSIGNED NOT NULL,
              `username` varchar(255) NOT NULL,
              `usermail` varchar(255) NOT NULL,
              `password` varchar(255) NOT NULL,
              `permissions` varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            INSERT INTO `users` (`id`, `username`, `usermail`, `password`, `permissions`) VALUES
            (1, 'Admin', 'drm@drm.com', '$2y$10$mgPCjxp2i04PkS3RUyD40.7kT5WRdnMuci6eBCb0GY4I..G7kPLZy', 'admin');

            ALTER TABLE `canales`
              ADD PRIMARY KEY (`id`);

            ALTER TABLE `users`
              ADD PRIMARY KEY (`id`);

            ALTER TABLE `canales`
              MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

            ALTER TABLE `users`
              MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
            COMMIT;
        ";

       $db->exec($createTablesQuery);
            echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">';
            echo '<h1 class="font-bold text-xl mb-2">Installation completed</h1>';
            echo '<p class="mb-2">The database connection details have been saved in the conn.php file.</p>';
            echo '<p class="mb-2">Login details for the administrator account:</p>';
            echo '<ul class="mb-2">';
            echo '<li>Username: Admin</li>';
            echo '<li>Email: drm@drm.com</li>';
            echo '<li>Password: 22333265</li>';
            echo '</ul>';
            echo '</div>';
            echo '<a href="../index.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-4 focus:ring-blue-300">Go to Home</a>';
            } else {
                echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">';
                echo '<h1 class="font-bold text-xl mb-2">Error</h1>';
                echo '<p class="mb-2">Unable to connect to the database. Please check the connection details and try again.</p>';
                echo '</div>';
            }
}
?>
