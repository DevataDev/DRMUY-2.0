<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        echo "success";
    } else {
        echo "error";
    }
}
?>
