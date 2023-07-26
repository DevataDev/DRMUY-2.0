<?php
require_once 'conn/conn.php';
require_once 'auth.php';

// Function to get user data from the database
function getUserData($db, $userId) {
    $query = "SELECT * FROM users WHERE id = :userId";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get all available permissions
function getAllPermissions() {
    return ['user', 'admin', 'superadmin'];
}

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Redirect to the login page
    header('Location: login.php');
    exit;
}

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Get the user data from the database
$user = getUserData($db, $userId);

// Function to update username, password, and permissions
function updateUser($db, $userId, $username, $password, $permissions) {
    $query = "UPDATE users SET username = :username, password = :password, permissions = :permissions WHERE id = :userId";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);
    $stmt->bindValue(':permissions', $permissions, PDO::PARAM_STR);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form was submitted
    if (isset($_POST['edit_username']) && isset($_POST['edit_password']) && isset($_POST['edit_permissions'])) {
        // Get the new username, password, and permissions
        $newUsername = $_POST['edit_username'];
        $newPassword = $_POST['edit_password'];
        $newPermissions = $_POST['edit_permissions'];

        // Update the user data in the database
        updateUser($db, $userId, $newUsername, $newPassword, $newPermissions);

        // Refresh the page to show the updated data
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRMUY - Perfil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link href="https://vjs.zencdn.net/7.15.4/video-js.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-900 text-white">
    <?php include "menu.php"; ?>
    <div class="p-4 sm:ml-64">
        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4 text-gray-500">Perfil</h1>
            <div class="max-w-md bg-gray-800 p-4 rounded-lg shadow-md">
                <div class="mb-4">
                    <p class="text-lg font-bold">User ID:</p>
                    <p><?php echo $user['id']; ?></p>
                </div>
                <div class="mb-4">
                    <p class="text-lg font-bold">Username:</p>
                    <p><?php echo $user['username']; ?></p>
                </div>
                <div class="mb-4">
                    <p class="text-lg font-bold">Email:</p>
                    <p><?php echo $user['usermail']; ?></p>
                </div>
                <div class="mb-4">
                    <p class="text-lg font-bold">Permissions:</p>
                    <p><?php echo $user['permissions']; ?></p>
                </div>
                <!-- Button to edit password -->
                <button id="editUserDataBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mt-4">
                    Edit User Data
                </button>
            </div>
        </div>
    </div>

    <!-- Edit User Data Popup -->
    <div id="editUserDataPopup" class="hidden fixed top-0 left-0 w-screen h-screen flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-gray-800 p-4 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4 text-white">Edit User Data</h2>
            <form id="editUserDataForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="mb-4">
                    <label for="edit_username" class="block text-gray-500 font-bold">Username</label>
                    <input type="text" id="edit_username" name="edit_username" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" value="<?php echo $user['username']; ?>">
                </div>
                <div class="mb-4">
                    <label for="edit_password" class="block text-gray-500 font-bold">Password</label>
                    <input type="password" id="edit_password" name="edit_password" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white">
                </div>
                <div class="mb-4">
                    <label for="edit_permissions" class="block text-gray-500 font-bold">Permissions</label>
                    <select id="edit_permissions" name="edit_permissions" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white">
                        <?php
                        $allPermissions = getAllPermissions();
                        foreach ($allPermissions as $permission) {
                            echo '<option value="' . $permission . '"';
                            if ($user['permissions'] === $permission) {
                                echo ' selected';
                            }
                            echo '>' . ucfirst($permission) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="text-right">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Save
                    </button>
                    <button id="cancelEditUserDataBtn" type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tippy.js@6.3.1/dist/tippy-bundle.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://vjs.zencdn.net/7.15.4/video.min.js"></script>

    <script>
        // Function to show the edit user data popup
        document.getElementById('editUserDataBtn').addEventListener('click', function () {
            document.getElementById('editUserDataPopup').classList.remove('hidden');
        });

        // Function to hide the edit user data popup
        document.getElementById('cancelEditUserDataBtn').addEventListener('click', function () {
            document.getElementById('editUserDataPopup').classList.add('hidden');
        });
    </script>
</body>

</html>
