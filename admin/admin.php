<?php
session_start();

if (!isset($_SESSION['user_id'])) {  // Cambia 'user_id' por la variable que uses para almacenar la sesión
    header("Location: login.php");   // Redirige al login si no hay sesión
    exit;
}

$host = "localhost";
$db = "clinica_odonto";
$user = "root";
$pass = "";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Traer usuarios
$usuarios = [];
$result = $mysqli->query("SELECT id, nombre, email FROM usuarios");
if ($result) {
    $usuarios = $result->fetch_all(MYSQLI_ASSOC);
}

// Traer presupuestos
$presupuestos = [];
$result = $mysqli->query("SELECT id, nombre, total_con_descuento, fecha FROM presupuestos");
if ($result) {
    $presupuestos = $result->fetch_all(MYSQLI_ASSOC);
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .slide-in {
            transform: translateX(100%);
        }

        .slide-in.show {
            transform: translateX(0%);
        }

        .transition-transform {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col relative overflow-hidden">
        <nav class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center z-20">
            <span class="font-bold text-xl">Admin Panel</span>
            <button id="menu-btn" class="md:hidden block focus:outline-none z-30 relative">
                <svg id="menu-icon" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div class="space-x-4 hidden md:flex">
                <a href="#" class="hover:underline">Inicio</a>
                <a href="#" class="hover:underline">Configuración</a>
                <a href="logout.php" class="block py-2 px-4 rounded hover:bg-blue-100">Cerrar Sesión</a>
            </div>
        </nav>

        <aside id="sidebar-mobile"
            class="fixed top-0 right-0 w-64 h-full bg-white shadow-md p-6 z-20 md:hidden slide-in transition-transform hidden">
            <ul class="space-y-4 mt-16">
                <li><a href="#usuarios" class="block py-2 px-4 rounded hover:bg-blue-100">Usuarios</a></li>
                <li><a href="./presupuestos.php" class="block py-2 px-4 rounded hover:bg-blue-100">Presupuestos</a></li>
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-blue-100">Ajustes</a></li>
                <li>
                    <a href="logout.php" class="block py-2 px-4 rounded hover:bg-blue-100">Cerrar Sesión</a>
                </li>
            </ul>
        </aside>

        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 z-10 hidden md:hidden"></div>

        <div class="flex flex-1 flex-col md:flex-row">
            <aside class="hidden md:block w-64 bg-white shadow-md p-6">
                <ul class="space-y-4">
                    <li><a href="#usuarios" class="block py-2 px-4 rounded hover:bg-blue-100">Usuarios</a></li>
                    <li><a href="./presupuestos.php" class="block py-2 px-4 rounded hover:bg-blue-100">Presupuestos</a>
                    </li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-blue-100">Ajustes</a></li>
                </ul>
            </aside>

            <main class="flex-1 p-4 md:p-8">
                <h1 class="text-2xl md:text-3xl font-bold mb-4 md:mb-6">Panel de Administración</h1>

                <!-- Usuarios -->
                <div id="usuarios" class="bg-white rounded shadow p-4 md:p-6 mb-6 overflow-x-auto">
                    <h2 class="text-lg md:text-xl font-bold mb-4">Usuarios Registrados</h2>
                    <?php if (count($usuarios) > 0): ?>
                        <table class="w-full table-auto border-collapse min-w-[600px]">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border px-2 md:px-4 py-1 md:py-2 text-left">ID</th>
                                    <th class="border px-2 md:px-4 py-1 md:py-2 text-left">Nombre</th>
                                    <th class="border px-2 md:px-4 py-1 md:py-2 text-left">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td class="border px-2 md:px-4 py-1 md:py-2"><?= htmlspecialchars($u['id']) ?></td>
                                        <td class="border px-2 md:px-4 py-1 md:py-2"><?= htmlspecialchars($u['nombre']) ?></td>
                                        <td class="border px-2 md:px-4 py-1 md:py-2"><?= htmlspecialchars($u['email']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay usuarios registrados.</p>
                    <?php endif; ?>
                </div>

                <!-- Presupuestos -->
                <div id="presupuestos" class="bg-white rounded shadow p-4 md:p-6 mb-6 overflow-x-auto">
                    <h2 class="text-lg md:text-xl font-bold mb-4">Presupuestos</h2>
                    <?php if (count($presupuestos) > 0): ?>
                        <table class="w-full table-auto border-collapse min-w-[700px]">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border px-2 md:px-4 py-1 md:py-2 text-left">ID</th>
                                    <th class="border px-2 md:px-4 py-1 md:py-2 text-left">Paciente</th>
                                    <th class="border px-2 md:px-4 py-1 md:py-2 text-left">Total</th>
                                    <th class="border px-2 md:px-4 py-1 md:py-2 text-left">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($presupuestos as $p): ?>
                                    <tr>
                                        <td class="border px-2 md:px-4 py-1 md:py-2"><?= htmlspecialchars($p['id']) ?></td>
                                        <td class="border px-2 md:px-4 py-1 md:py-2"><?= htmlspecialchars($p['nombre']) ?></td>
                                        <td class="border px-2 md:px-4 py-1 md:py-2">
                                            $<?= number_format($p['total_con_descuento'], 2) ?></td>
                                        <td class="border px-2 md:px-4 py-1 md:py-2"><?= htmlspecialchars($p['fecha']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay presupuestos registrados.</p>
                    <?php endif; ?>
                </div>
            </main>

        </div>

        <footer class="bg-blue-700 text-white text-center py-4">
            &copy; 2025 Administrador. Todos los derechos reservados.
        </footer>
    </div>

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const sidebarMobile = document.getElementById('sidebar-mobile');
        const overlay = document.getElementById('overlay');
        const menuIcon = document.getElementById('menu-icon');

        let sidebarOpen = false;

        menuBtn.addEventListener('click', () => {
            sidebarOpen = !sidebarOpen;
            if (sidebarOpen) {
                sidebarMobile.classList.remove('hidden');
                setTimeout(() => sidebarMobile.classList.add('show'), 10);
                overlay.classList.remove('hidden');
                menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            } else {
                sidebarMobile.classList.remove('show');
                overlay.classList.add('hidden');
                menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
                setTimeout(() => sidebarMobile.classList.add('hidden'), 300);
            }
        });

        overlay.addEventListener('click', () => {
            sidebarMobile.classList.remove('show');
            overlay.classList.add('hidden');
            menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
            sidebarOpen = false;
            setTimeout(() => sidebarMobile.classList.add('hidden'), 300);
        });
    </script>
</body>

</html>