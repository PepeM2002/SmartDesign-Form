<?php
<<<<<<< HEAD
// back/guardar.php
header("Content-Type: text/html; charset=utf-8");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clinica_odonto";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// ✅ Datos del paciente
$nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
$fecha = $conn->real_escape_string($_POST['fecha'] ?? '');
$doctor = $conn->real_escape_string($_POST['doctor'] ?? '');
$expediente = $conn->real_escape_string($_POST['expediente'] ?? '');
$correo = $conn->real_escape_string($_POST['correo'] ?? '');
$observaciones = $conn->real_escape_string($_POST['observaciones'] ?? '');

// ✅ Fechas de pago
$dia_tratamiento = $conn->real_escape_string($_POST['dia_tratamiento'] ?? '');
$inicio_tratamiento = $conn->real_escape_string($_POST['inicio_tratamiento'] ?? '');
$antes_final_tratamiento = $conn->real_escape_string($_POST['antes_final_tratamiento'] ?? '');
$tipo_descuento = $conn->real_escape_string($_POST['tipo-descuento'] ?? '');

// ✅ Odontograma (checkboxes en JSON)
$dientes = [];
foreach ($_POST as $key => $value) {
    if (strpos($key, "diente-") === 0) {
        $dientes[$key] = 1;
    }
}
$dientes_json = json_encode($dientes, JSON_UNESCAPED_UNICODE);

// ✅ Tratamientos (ya vienen como JSON desde el input oculto)
$tratamientos_json = $_POST['tratamientos_json'] ?? '[]';
$tratamientos = json_decode($tratamientos_json, true);

// Calcular totales
$total_sin_descuento = 0;
$total_con_descuento = 0;
$descuento_total = 0;

if (is_array($tratamientos)) {
    foreach ($tratamientos as $t) {
        $precio = floatval($t['precio']);
        $cantidad = intval($t['cantidad']);
        $descuento = floatval($t['descuento']);

        $subtotal = $precio * $cantidad;
        $desc = min($descuento, $subtotal);
        $total = max($subtotal - $desc, 0);

        $total_sin_descuento += $subtotal;
        $descuento_total += $desc;
        $total_con_descuento += $total;
    }
}

// ✅ Insertar en MySQL
$sql = "INSERT INTO presupuestos 
(nombre, fecha, doctor, expediente, correo, observaciones, dientes, tratamientos, 
 total_sin_descuento, descuento_total, total_con_descuento, tipo_descuento, 
 dia_tratamiento, inicio_tratamiento, antes_final_tratamiento) 
VALUES (
    '$nombre',
    '$fecha',
    '$doctor',
    '$expediente',
    '$correo',
    '$observaciones',
    '" . $conn->real_escape_string($dientes_json) . "',
    '" . $conn->real_escape_string($tratamientos_json) . "',
    '$total_sin_descuento',
    '$descuento_total',
    '$total_con_descuento',
    '$tipo_descuento',
    '$dia_tratamiento',
    '$inicio_tratamiento',
    '$antes_final_tratamiento'
)";

if ($conn->query($sql) === TRUE) {
    header("Location: ../confirmacion.php");
} else {
    echo "❌ Error al guardar: " . $conn->error;
}

$conn->close();
=======
$conn = new mysqli("localhost", "root", "", "clinica_odonto");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$nombre = $_POST['nombre'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$doctor = $_POST['doctor'] ?? '';
$expediente = $_POST['expediente'] ?? '';
$correo = $_POST['correo'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';
$tipo_descuento = $_POST['tipo-descuento'] ?? '';
$dia_tratamiento = $_POST['dia_tratamiento'] ?? '';
$inicio_tratamiento = $_POST['inicio_tratamiento'] ?? '';
$antes_final_tratamiento = $_POST['antes_final_tratamiento'] ?? '';

$dientes = [];
for ($i = 11; $i <= 48; $i++) {
    if (isset($_POST["diente-$i"])) {
        $dientes[] = $i;
    }
}
$dientes_str = implode(",", $dientes);

$tratamientos_json = $_POST['tratamientos_json'] ?? '[]';

$stmt = $conn->prepare("
    INSERT INTO presupuestos (
        nombre, fecha, doctor, expediente, correo, observaciones,
        dientes, tratamientos, tipo_descuento,
        dia_tratamiento, inicio_tratamiento, antes_final_tratamiento
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssssssssss",
    $nombre,
    $fecha,
    $doctor,
    $expediente,
    $correo,
    $observaciones,
    $dientes_str,
    $tratamientos_json,
    $tipo_descuento,
    $dia_tratamiento,
    $inicio_tratamiento,
    $antes_final_tratamiento
);

if ($stmt->execute()) {
    header("Location: ../confirmacion.html");
    exit();
} else {
    echo "Error al guardar los datos: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
>>>>>>> 335e78a6195129d93cf4b17562b9f8ab2c49f2d1
