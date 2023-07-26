<?php
// Incluir archivo de conexión a la base de datos
require_once 'conn/conn.php';

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los valores del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consultar la base de datos para verificar las credenciales del usuario
    $query = "SELECT * FROM users WHERE usermail = :email";
    $statement = $db->prepare($query);
    $statement->bindParam(':email', $email);
    $statement->execute();

    // Verificar si se encontró un usuario con el correo electrónico dado
    if ($statement->rowCount() > 0) {
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        $hashedPassword = $user['password'];

        // Verificar la contraseña
        if (password_verify($password, $hashedPassword)) {
            // Credenciales válidas, iniciar sesión y redireccionar al dashboard
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header('Location: index.php');
            exit;
        } else {
            // Contraseña incorrecta
            $error = 'Contraseña incorrecta. Por favor, intenta de nuevo.';
        }
    } else {
        // Usuario no encontrado
        $error = 'Usuario no encontrado. Por favor, verifica tus credenciales.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-900">
    <div class="flex items-center justify-center h-screen">
        <div class="max-w-2xl w-full mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="lg:flex">
                    <div class="lg:w-1/2 bg-cover" style="background-image:url('https://images.unsplash.com/photo-1546514714-df0ccc50d7bf?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=667&q=80')"></div>
                    <div class="w-full p-8 lg:w-1/2">
                        <h2 class="text-2xl font-semibold text-gray-700 text-center">DRMUY 1.0</h2>
                        <p class="text-xl text-gray-600 text-center">Welcome back!</p>
                        <form method="POST" action="login.php" class="mt-4">
                            <?php if (isset($error)): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4" role="alert">
                                <?php echo $error; ?>
                            </div>
                            <?php endif; ?>
                            <div class="mt-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                                <input name="email" class="bg-gray-200 text-gray-700 focus:outline-none focus:shadow-outline border border-gray-300 rounded py-2 px-4 block w-full appearance-none" type="email" required>
                            </div>
                            <div class="mt-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                                <input name="password" class="bg-gray-200 text-gray-700 focus:outline-none focus:shadow-outline border border-gray-300 rounded py-2 px-4 block w-full appearance-none" type="password" required>
                            </div>
                            <div class="mt-8">
                                <button type="submit" class="bg-gray-700 text-white font-bold py-2 px-4 w-full rounded hover:bg-gray-600">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tippy.js@6.3.1/dist/tippy-bundle.umd.min.js"></script>
</body>
</html>
