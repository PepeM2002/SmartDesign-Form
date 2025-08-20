<?php
session_start();
if (!isset($_SESSION['user_id'])) {  // Cambia 'user_id' por la variable que uses para almacenar la sesión
    header("Location: login.php");   // Redirige al login si no hay sesión
    exit;
}

require '../fpdf/fpdf.php';

$conn = new mysqli("localhost", "root", "", "clinica_odonto");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (isset($_GET['pdf'])) {
    $id = intval($_GET['pdf']);

    $result = $conn->query("SELECT * FROM presupuestos WHERE id=$id");
    if ($result && $p = $result->fetch_assoc()) {

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetAutoPageBreak(true, 10);

        // --- Encabezado ---
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 8, ("Nombre del paciente: " . ($p['nombre'] ?? '')), 1, 0, 'L');
        $pdf->Cell(95, 8, ("Fecha: " . ($p['fecha'] ?? '')), 1, 1, 'R');

        $pdf->Cell(95, 8, ("Expediente: " . ($p['expediente'] ?? '')), 1, 0, 'L');
        $pdf->Cell(95, 8, ("Correo electrónico: " . ($p['correo'] ?? '')), 1, 1, 'L');

        $pdf->MultiCell(190, 8, ("Observaciones: " . ($p['observaciones'] ?? '')), 1, 'L');

        $pdf->Ln(3);

        // --- Estado de dientes ---
        $estadoDientes = !empty($p['dientes']) ? json_decode($p['dientes'], true) : [];

        $odontograma = [
            "superior" => [
                "../assets/D18.png",
                "../assets/D17.png",
                "../assets/D16.png",
                "../assets/D15.png",
                "../assets/D14.png",
                "../assets/D13.png",
                "../assets/D12.png",
                "../assets/D11.png",
                "../assets/D21.png",
                "../assets/D22.png",
                "../assets/D23.png",
                "../assets/D24.png",
                "../assets/D25.png",
                "../assets/D26.png",
                "../assets/D27.png",
                "../assets/D28.png"
            ],
            "inferior" => [
                "../assets/D48.png",
                "../assets/D47.png",
                "../assets/D46.png",
                "../assets/D45.png",
                "../assets/D44.png",
                "../assets/D43.png",
                "../assets/D42.png",
                "../assets/D41.png",
                "../assets/D31.png",
                "../assets/D32.png",
                "../assets/D33.png",
                "../assets/D34.png",
                "../assets/D35.png",
                "../assets/D36.png",
                "../assets/D37.png",
                "../assets/D38.png"
            ]
        ];

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(190, 8, ("ODONTOGRAMA"), 1, 1, 'C');

        $startX = 9;
        $startY = $pdf->GetY() + 5;
        $boxW = 10;
        $boxH = 10;
        $gap = 2;

        // Dientes superiores
        $x = $startX;
        $y = $startY;
        $numerosSuperior = [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28];
        foreach ($numerosSuperior as $i => $diente) {
            $imgPath = $odontograma['superior'][$i] ?? null;
            if ($imgPath && file_exists($imgPath)) {
                $pdf->Image($imgPath, $x, $y, $boxW, $boxH);
            } else {
                $pdf->Rect($x, $y, $boxW, $boxH);
            }
            if (!empty($estadoDientes["diente-$diente"])) {
                $pdf->SetDrawColor(0, 0, 255);
                $pdf->SetLineWidth(0.8);
                $pdf->Rect($x, $y, $boxW, $boxH, 'D');
                $pdf->SetLineWidth(0.2);
            }
            $x += $boxW + $gap;
        }

        // Dientes inferiores
        $x = $startX;
        $y = $startY + $boxH + 5;
        $numerosInferior = [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38];
        foreach ($numerosInferior as $i => $diente) {
            $imgPath = $odontograma['inferior'][$i] ?? null;
            if ($imgPath && file_exists($imgPath)) {
                $pdf->Image($imgPath, $x, $y, $boxW, $boxH);
            } else {
                $pdf->Rect($x, $y, $boxW, $boxH);
            }
            if (!empty($estadoDientes["diente-$diente"])) {
                $pdf->SetDrawColor(0, 0, 255);
                $pdf->SetLineWidth(0.8);
                $pdf->Rect($x, $y, $boxW, $boxH, 'D');
                $pdf->SetLineWidth(0.2);
            }
            $x += $boxW + $gap;
        }

        $pdf->Ln(50);

        // Función para calcular la cantidad de líneas que ocupará un texto en MultiCell
        function calcularAlto($pdf, $ancho, $texto, $altoLinea = 8)
        {
            $cw = $pdf->GetStringWidth($texto);
            $numLineas = ceil($cw / $ancho);
            return $numLineas * $altoLinea;
        }

        // --- Presupuesto ---
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 8, ("Cant"), 1, 0, 'C');
        $pdf->Cell(100, 8, ("Servicio"), 1, 0, 'C');
        $pdf->Cell(40, 8, ("Precio"), 1, 0, 'C');
        $pdf->Cell(40, 8, ("Precio c/ desc."), 1, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $tratamientos = json_decode($p['tratamientos'] ?? '[]', true);

        if (!empty($tratamientos)) {
            foreach ($tratamientos as $t) {
                $cantidad = $t['cantidad'] ?? 0;
                $nombre = $t['nombre'] ?? '';
                $precio = $t['precio'] ?? 0;
                $descuento = $t['descuento'] ?? 0;
                $precioConDescuento = $precio - ($precio * $descuento / 100);

                // Calcular altura de la fila según el texto del servicio
                $altoFila = calcularAlto($pdf, 100, ($nombre), 8);

                // Cantidad
                $pdf->Cell(10, $altoFila, $cantidad, 1, 0, 'C');

                // Servicio
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(100, 8, ($nombre), 1, 'L');
                $pdf->SetXY($x + 100, $y);

                // Precio
                $pdf->Cell(40, $altoFila, "$" . number_format($precio, 2), 1, 0, 'C');

                // Precio con descuento
                $pdf->Cell(40, $altoFila, "$" . number_format($precioConDescuento, 2), 1, 1, 'C');
            }
        } else {
            $pdf->Cell(190, 8, ("No hay tratamientos registrados"), 1, 1, 'C');
        }



        // Totales
        $pdf->Cell(190, 8, ("Tipo de descuento: " . ($p['tipo_descuento'] ?? '')), 1, 1, 'C');
        $pdf->Cell(190, 8, ("Total sin descuento: $" . number_format(($p['total_sin_descuento'] ?? 0), 2)), 1, 1, 'C');
        $pdf->Cell(190, 8, ("Descuento aplicado: $" . number_format(($p['descuento_total'] ?? 0), 2)), 1, 1, 'C');
        $pdf->Cell(190, 8, ("Total con descuento: $" . number_format(($p['total_con_descuento'] ?? 0), 2)), 1, 1, 'C');

        // Forma de pago
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(190, 8, ("Forma de Pago"), 1, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(190, 6, (
            "- Al día del tratamiento: " . ($p['dia_tratamiento'] ?? '') . "\n" .
            "- Al inicio del tratamiento: " . ($p['inicio_tratamiento'] ?? '') . "\n" .
            "- Antes del final del tratamiento: " . ($p['antes_final_tratamiento'] ?? '') . "\n\n" .
            "En ningún caso el presente presupuesto representa un recibo de pago."
        ), 1, 'L');

        // Nota final
        $pdf->Ln(3); // Separación
        $pdf->SetFont('Arial', '', 8);
        $nota = "Nota: Estos presupuestos son de carácter informativo y pueden sufrir modificaciones por imprevistos que se presenten durante el tratamiento. Todas las cantidades de este presupuesto se expresan en moneda nacional y tienen una vigencia de 15 días a partir de su fecha de expedición.\n- En ningún caso el presente presupuesto representa un recibo de pago -";
        $pdf->MultiCell(190, 6, ($nota), 1, 'L');


        $pdf->Output();
        exit;
    }
}
?>