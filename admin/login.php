<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: admin.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-column items-center justify-center min-h-screen">


    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-semibold text-center mb-6">Login</h2>

        <form id="loginForm" action="./back/login.php" method="POST" class="space-y-4">
            <input type="email" name="email" placeholder="Correo" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="password" name="password" placeholder="Contraseña" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Entrar
            </button>

            <a href="./registro.php"
                class="w-full inline-block text-center py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                Registrarse
            </a>
            <a href="../index.html"
                class="w-full inline-block text-center py-2 mt-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                Ir al formulario
            </a>
        </form>

        <div id="mensaje" class="mt-4 text-center text-red-600"></div>


    </div>
</body>

</html>