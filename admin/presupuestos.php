<?php
session_start();
if (!isset($_SESSION['user_id'])) {  // Cambia 'user_id' por la variable que uses para almacenar la sesión
    header("Location: login.php");   // Redirige al login si no hay sesión
    exit;
}

require '../fpdf/fpdf.php';

$host = "localhost";
$db = "clinica_odonto";
$user = "root";
$pass = "";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Eliminar presupuesto
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM presupuestos WHERE id=$id");
    header("Location: gestor_presupuestos.php");
    exit;
}

// Generar PDF


// Ordenar
$orden = "id";
$tipo = "ASC";
$allowed = ['id', 'nombre', 'total', 'fecha'];
if (isset($_GET['orden']) && in_array($_GET['orden'], $allowed)) {
    $orden = $_GET['orden'];
}
if (isset($_GET['tipo']) && in_array(strtoupper($_GET['tipo']), ['ASC', 'DESC'])) {
    $tipo = strtoupper($_GET['tipo']);
}

// Traer presupuestos
$presupuestos = [];
$result = $mysqli->query("SELECT * FROM presupuestos ORDER BY $orden $tipo");
if ($result) {
    $presupuestos = $result->fetch_all(MYSQLI_ASSOC);
}

$mysqli->close();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Presupuestos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center">
        <span class="font-bold text-xl">Admin Panel</span>
        <button id="menu-btn" class="md:hidden block focus:outline-none">
            <svg id="menu-icon" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="space-x-4 hidden md:flex">
            <a href="./admin.php" class="hover:underline">Inicio</a>
            <a href="#" class="hover:underline">Configuración</a>
            <a href="logout.php" class="block py-2 px-4 rounded hover:bg-blue-100">Cerrar Sesión</a>
        </div>
    </nav>

    <!-- Sidebar móvil -->
    <aside id="sidebar-mobile"
        class="fixed top-0 right-0 w-64 h-full bg-white shadow-md p-6 z-20 transform translate-x-full transition-transform duration-300 md:hidden">
        <ul class="space-y-4 mt-16">
            <li><a href="./admin.php" class="block py-2 px-4 rounded hover:bg-blue-100">Inicio</a></li>
            <li><a href="./presupuestos.php" class="block py-2 px-4 rounded hover:bg-blue-100">Presupuestos</a></li>
            <li><a href="#" class="block py-2 px-4 rounded hover:bg-blue-100">Ajustes</a></li>
            <li>
                <a href="logout.php" class="block py-2 px-4 rounded hover:bg-blue-100">Cerrar Sesión</a>
            </li>

        </ul>
    </aside>

    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 z-10 hidden md:hidden"></div>

    <div class="flex flex-col md:flex-row">

        <!-- Sidebar escritorio -->
        <aside class="hidden md:block w-64 bg-white shadow-md p-6">
            <ul class="space-y-4">
                <li><a href="./admin.php" class="block py-2 px-4 rounded hover:bg-blue-100">Usuarios</a></li>
                <li><a href="./presupuestos.php" class="block py-2 px-4 rounded hover:bg-blue-100">Presupuestos</a></li>
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-blue-100">Ajustes</a></li>
            </ul>
        </aside>

        <!-- Contenido principal -->
        <main class="flex-1 p-6">
            <h1 class="text-3xl font-bold mb-6">Presupuestos</h1>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow overflow-hidden table-auto">
                    <thead class="bg-gray-200">
                        <tr>
                            <?php
                            $columns = ['id' => 'ID', 'paciente' => 'Paciente', 'total' => 'Total', 'fecha' => 'Fecha'];
                            foreach ($columns as $col => $label):
                                $tipoLink = ($orden == $col && $tipo == 'ASC') ? 'DESC' : 'ASC';
                                ?>
                                <th class="px-4 py-2 text-left whitespace-nowrap text-sm sm:text-base">
                                    <a href="?orden=<?= $col ?>&tipo=<?= $tipoLink ?>" class="hover:underline">
                                        <?= $label ?>
                                        <?php if ($orden == $col)
                                            echo $tipo == 'ASC' ? '▲' : '▼'; ?>
                                    </a>
                                </th>
                            <?php endforeach; ?>
                            <th class="px-4 py-2 text-left whitespace-nowrap text-sm sm:text-base">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($presupuestos) > 0): ?>
                            <?php foreach ($presupuestos as $p): ?>
                                <tr class="border-t">
                                    <td class="px-4 py-2 whitespace-nowrap text-sm sm:text-base">
                                        <?= htmlspecialchars($p['id']) ?>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm sm:text-base">
                                        <?= htmlspecialchars($p['nombre']) ?>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm sm:text-base">
                                        $<?= number_format($p['total_con_descuento'], 2) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm sm:text-base">
                                        <?= htmlspecialchars($p['fecha']) ?>
                                    </td>
                                    <td class="px-4 py-2 flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                                        <a href="./genPdf.php?pdf=<?= $p['id'] ?>"
                                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-sm sm:text-base text-center"
                                            target="_blank">
                                            PDF
                                        </a>
                                        <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Eliminar presupuesto?')"
                                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm sm:text-base text-center">
                                            Eliminar
                                        </a>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-sm sm:text-base">No hay presupuestos.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

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
                sidebarMobile.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
                menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            } else {
                sidebarMobile.classList.add('translate-x-full');
                overlay.classList.add('hidden');
                menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
            }
        });

        overlay.addEventListener('click', () => {
            sidebarMobile.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
            sidebarOpen = false;
        });
    </script>

</body>

</html>