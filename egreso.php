<?php
require_once("conexion.php");

$id = intval($_GET['id'] ?? 0);

// Obtenemos los datos del campamento incluyendo la senia_original
$query = "SELECT * FROM campamentos WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$campamento = $stmt->get_result()->fetch_assoc();

if (!$campamento) { die("Registro no encontrado."); }

if (isset($_POST['confirmar_egreso'])) {
    $opcion = $_POST['manejo_senia']; // 'devolver' o 'retener'
    
    // Si se devuelve, la seña actual pasa a 0.
    // senia_original NO se toca, queda guardada para el reporte.
    $senia_final = ($opcion === 'devolver') ? 0 : $campamento['senia_original'];

    $query_update = "UPDATE campamentos SET estado = 'Egreso', fecha_egreso = CURDATE(), senia = ? WHERE id = ?";
    $stmt_upd = $mysqli->prepare($query_update);
    $stmt_upd->bind_param("di", $senia_final, $id);
    $stmt_upd->execute();
    
    header("Location: index.php?msj=" . urlencode("Egreso procesado correctamente"));
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Egreso</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#6C2E2C] flex items-center justify-center h-screen">
    <div class="bg-[#FDF28F] p-8 rounded-xl shadow-2xl w-full max-w-lg">
        <h1 class="text-2xl font-bold mb-4 text-[#6C2E2C] text-center uppercase">CONFIRMAR EGRESO</h1>
        
        <div class="mb-6 bg-white/50 p-4 rounded-lg">
            <p class="text-[#6C2E2C]"><strong>Cliente:</strong> <?= $campamento['apellido'] . " " . $campamento['nombre'] ?></p>
            <p class="text-[#6C2E2C]"><strong>Carpa Nº:</strong> <?= $campamento['num_carpa'] ?></p>
            <p class="text-xl mt-2 font-extrabold text-red-800">SEÑA REGISTRADA: $<?= formatoMoneda($campamento['senia_original']) ?></p>
        </div>

        <form method="POST">
            <div class="flex flex-col gap-3 mb-6">
                <label class="flex items-center gap-3 bg-green-100 p-4 rounded-full border border-green-500 cursor-pointer hover:bg-green-200 transition">
                    <input type="radio" name="manejo_senia" value="devolver" checked class="w-5 h-5">
                    <span class="text-green-800 font-bold uppercase">Devolver Seña ($<?= formatoMoneda($campamento['senia_original']) ?>)</span>
                </label>
            </div>

            <button type="submit" name="confirmar_egreso" class="w-full bg-[#6C2E2C] text-white py-4 rounded-full font-extrabold hover:bg-red-950 transition uppercase shadow-lg">
                DAR DE BAJA
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="index.php" class="text-[#6C2E2C] underline font-semibold text-sm">← Volver al Listado</a>
        </div>
    </div>
</body>
</html>