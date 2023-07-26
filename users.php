<?php
require_once 'conn/conn.php';
require_once 'auth.php';

// Función para obtener todos los usuarios de la base de datos
function getUsers($db) {
    $query = "SELECT * FROM users";
    $stmt = $db->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para insertar un nuevo usuario en la base de datos
function insertUser($db, $username, $usermail, $password, $permissions) {
    $query = "INSERT INTO users (username, usermail, password, permissions) VALUES (:username, :usermail, :password, :permissions)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':usermail', $usermail, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt->bindValue(':permissions', $permissions, PDO::PARAM_STR);
    return $stmt->execute();
}

// Función para eliminar un usuario de la base de datos
function deleteUser($db, $userId) {
    $query = "DELETE FROM users WHERE id = :userId";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    return $stmt->execute();
}

// Obtener todos los usuarios de la base de datos
$users = getUsers($db);

// Procesar el formulario de agregar usuarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = $_POST['username'];
    $usermail = $_POST['usermail'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $permissions = $_POST['permissions'];

    // Insertar el nuevo usuario en la base de datos
    insertUser($db, $username, $usermail, $password, $permissions);

    // Recargar la página para mostrar la tabla actualizada
    header("Location: {$_SERVER['PHP_SELF']}");
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
    <link href="https://vjs.zencdn.net/7.15.4/video-js.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-900">
<?php include "menu.php"; ?>
<div class="p-4 sm:ml-64">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4 text-gray-500">Users</h1>
        <div class="bg-gray-800 p-4 rounded-lg shadow-md mb-4">
            <h2 class="text-xl font-bold mb-2 text-gray-500">Add User</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-500 font-bold">Username</label>
                    <input type="text" name="username" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                </div>
                <div>
                    <label class="block text-gray-500 font-bold">Email</label>
                    <input type="email" name="usermail" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                </div>
                <div>
                    <label class="block text-gray-500 font-bold">Password</label>
                    <input type="password" name="password" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                </div>
                <div>
                    <label class="block text-gray-500 font-bold">Permissions</label>
                    <select name="permissions" class="w-full border rounded-lg py-2 px-3 bg-gray-700 text-white">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <button type="submit" name="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Add User
                    </button>
                </div>
            </form>
        </div>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-lg text-center text-white">
        <thead class="text-sm text-gray-700 uppercase bg-gray-700 text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 text-white">
                    ID
                </th>
                <th scope="col" class="px-6 py-3 text-white">
                    Username
                </th>
                <th scope="col" class="px-6 py-3 text-white">
                    Email
                </th>
                <th scope="col" class="px-6 py-3 text-white">
                    Permissions
                </th>
                <th scope="col" class="px-6 py-3 text-white">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) { ?>
                <tr class="border-b bg-gray-800 border-gray-700 hover:bg-gray-600" data-user-id="<?php echo $user['id']; ?>">
                    <td class="px-6 py-4  whitespace-nowrap">
                        <?php echo $user['id']; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php echo $user['username']; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php echo $user['usermail']; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php echo $user['permissions']; ?>
                    </td>
                    <td class="px-6 py-4 text-center"> <!-- Ajustamos el estilo aquí -->
                        <button class="text-blue-500 hover:underline mx-2" onclick="editUser(<?php echo $user['id']; ?>)"> <!-- Ajustamos el estilo aquí -->
                            Edit
                        </button>
                        <button class="text-red-500 hover:underline mx-2" onclick="deleteUser(<?php echo $user['id']; ?>)"> <!-- Ajustamos el estilo aquí -->
                            Delete
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal for editing user -->
<div id="editUserModal" class="hidden fixed inset-0 z-50 overflow-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="relative p-8 mx-auto my-4 max-w-lg max-h-full w-full">
        <div class="bg-gray-800 rounded-lg text-left overflow-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl">
            <form id="editUserForm" class="p-4">
                <h3 class="text-lg leading-6 font-medium text-gray-500">Edit User</h3>
                <div class="mt-4 space-y-4">
                    <input type="hidden" id="editUserId" value="">
                    <div class="flex items-center space-x-4">
                        <label class="text-gray-500 font-bold w-24">Username:</label>
                        <input type="text" id="editUsername" class="flex-1 border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                    </div>
                    <div class="flex items-center space-x-4">
                        <label class="text-gray-500 font-bold w-24">Email:</label>
                        <input type="email" id="editUsermail" class="flex-1 border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                    </div>
                    <div class="flex items-center space-x-4">
                        <label class="text-gray-500 font-bold w-24">Password:</label>
                        <input type="password" id="editPassword" class="flex-1 border rounded-lg py-2 px-3 bg-gray-700 text-white" required>
                    </div>
                    <div class="flex items-center space-x-4">
                        <label class="text-gray-500 font-bold w-24">Permissions:</label>
                        <select id="editPermissions" class="flex-1 border rounded-lg py-2 px-3 bg-gray-700 text-white">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Save Changes
                    </button>
                    <button type="button" class="ml-4 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" onclick="closeEditUserModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function deleteUser(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this user!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send a request to delete the user
                $.ajax({
                    url: 'delete_user.php', // Replace with the PHP file to handle user deletion
                    type: 'POST',
                    data: { userId: userId },
                    success: function (response) {
                        // Reload the page after successful deletion
                        window.location.reload();
                    },
                    error: function () {
                        // Show an error message if deletion fails
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong. Unable to delete the user.',
                        });
                    }
                });
            }
        });
    }

 function editUser(userId) {
        // Get user data from the table
        const username = document.querySelector(`tr[data-user-id="${userId}"] td:nth-child(2)`).innerText;
        const usermail = document.querySelector(`tr[data-user-id="${userId}"] td:nth-child(3)`).innerText;
        const permissions = document.querySelector(`tr[data-user-id="${userId}"] td:nth-child(4)`).innerText;

        // Populate the edit user form with the data
        document.getElementById('editUserId').value = userId;
        document.getElementById('editUsername').value = username;
        document.getElementById('editUsermail').value = usermail;
        document.getElementById('editPermissions').value = permissions;

        // Clear the password field before showing the modal
        document.getElementById('editPassword').value = '';

        // Show the modal
        document.getElementById('editUserModal').classList.remove('hidden');
    }

    function closeEditUserModal() {
        // Close the modal
        document.getElementById('editUserModal').classList.add('hidden');
        // Clear the password field when closing the modal
        document.getElementById('editPassword').value = '';
    }

    // Submit the edit user form
    document.getElementById('editUserForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const userId = document.getElementById('editUserId').value;
        const username = document.getElementById('editUsername').value;
        const usermail = document.getElementById('editUsermail').value;
        const permissions = document.getElementById('editPermissions').value;

        // Send a request to update the user data
        $.ajax({
            url: 'update_user.php', // Replace with the PHP file to handle user data update
            type: 'POST',
            data: {
                userId: userId,
                username: username,
                usermail: usermail,
                permissions: permissions
            },
            success: function (response) {
                // Reload the page after successful update
                window.location.reload();
            },
            error: function () {
                // Show an error message if update fails
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong. Unable to update the user.',
                });
            }
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tippy.js@6.3.1/dist/tippy-bundle.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://vjs.zencdn.net/7.15.4/video.min.js"></script>
</body>
</html>
