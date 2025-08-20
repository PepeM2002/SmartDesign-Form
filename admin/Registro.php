<?php
session_start();

// Si ya hay sesión iniciada, redirigir al panel
if (isset($_SESSION['user_id'])) {  // Cambia 'user_id' por la variable que uses en sesión
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Registro</h2>

        <!-- Formulario de registro -->
        <form id="registroForm" action="./back/registro.php" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="nombre">Nombre</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    type="text" id="nombre" name="nombre" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="email">Correo electrónico</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    type="email" id="email" name="email" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="password">Contraseña</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition mb-2">
                Registrarse
            </button>

            <a href="./login.php"
                class="w-full inline-block text-center py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                Ir a inicio de sesión
            </a>
        </form>

        <!-- Mensaje de respuesta -->
        <div id="mensaje" class="mt-4 text-center text-red-600"></div>

        <script>
            const registroForm = document.getElementById('registroForm');
            const mensaje = document.getElementById('mensaje');

            registroForm.addEventListener('submit', async function (event) {
                event.preventDefault();

                // Crear objeto FormData
                const formData = new FormData(registroForm);

                // Enviar datos al PHP vía fetch
                const response = await fetch(registroForm.action, {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();
                mensaje.innerHTML = text; // Mostrar mensaje desde PHP
            });
        </script>
    </div>
</body>

</html>