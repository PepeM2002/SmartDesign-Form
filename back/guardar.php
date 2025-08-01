<?php
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