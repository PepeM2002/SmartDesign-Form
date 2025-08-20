<?php
session_start();

$mensaje = ""; // Variable para mostrar errores o mensajes
$showLoader = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = "localhost";
    $db = "clinica_odonto";
    $user = "root";
    $pass = "";

    $mysqli = new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_errno) {
        $mensaje = "Error de conexión: " . $mysqli->connect_error;
    } elseif (isset($_POST['email'], $_POST['password'])) {
        $email = $mysqli->real_escape_string($_POST['email']);
        $password = $_POST['password'];

        $sql = "SELECT id, nombre, password FROM usuarios WHERE email = '$email' LIMIT 1";
        $result = $mysqli->query($sql);

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nombre'] = $user['nombre'];
                $showLoader = true; // Mostrará loader antes de redirigir
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "Usuario no encontrado.";
        }
    } else {
        $mensaje = "Por favor, completa todos los campos.";
    }

    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3b82f6;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md text-center">
        <?php if ($showLoader): ?>
            <div class="loader"></div>
            <p class="text-lg font-semibold">Iniciando sesión...</p>
            <script>
                setTimeout(function () {
                    window.location.href = "../admin.php";
                }, 2000);
            </script>
        <?php else: ?>
            <h2 class="text-2xl font-semibold mb-6">Login</h2>

            <?php if ($mensaje): ?>
                <div class="mb-4 text-red-600"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
                <input type="email" name="email" placeholder="Correo" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="password" name="password" placeholder="Contraseña" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Entrar
                </button>

                <a href="./registro.html"
                    class="w-full inline-block text-center py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Registrarse
                </a>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>