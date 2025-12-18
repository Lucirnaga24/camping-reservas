<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("conexion.php");

// ==========================================================
// LÓGICA DE PROCESAMIENTO
// ==========================================================
$msj = "";

if (isset($_POST['apellido'])) {

    $num_carpa = intval($_POST['num_carpa'] ?? 0);
    $dni = $mysqli->real_escape_string($_POST['dni'] ?? "");
    $apellido = $mysqli->real_escape_string(limpiarTexto($_POST['apellido'] ?? ""));
    $nombre = $mysqli->real_escape_string(limpiarTexto($_POST['nombre'] ?? ""));
    $localidad = $mysqli->real_escape_string(limpiarTexto($_POST['localidad'] ?? ""));
    $telefono = $mysqli->real_escape_string($_POST['telefono'] ?? "");
    $patente = $mysqli->real_escape_string(strtoupper($_POST['patente'] ?? ""));
    $categoria = $mysqli->real_escape_string($_POST['categoria'] ?? "");
    $mayores = intval($_POST['mayores'] ?? 0);
    $menores = intval($_POST['menores'] ?? 0);
    $fecha_ingreso = $_POST['fecha_ingreso'] ?? "";
    $fecha_egreso = $_POST['fecha_vencimiento'] ?? "";
    $senia_cobrada = isset($_POST['senia_cobrada']) ? floatval($_POST['senia_cobrada']) : SENIA;

    $ok = true;

    if ($num_carpa <= 0) {
        $ok = false;
        $msj = "Número de carpa inválido.";
    }

    if ($ok && $fecha_ingreso && $fecha_egreso) {
        try {
            $ingreso = new DateTime($fecha_ingreso);
            $egreso = new DateTime($fecha_egreso);
            $interval = $ingreso->diff($egreso);
            $noches = max(0, $interval->days);

            if ($noches < 1) {
                $ok = false;
                $msj = "Debe pagar al menos una noche.";
            }

            if ($ok) {
                $precio_noche = calcularPrecioNoche($categoria, $mayores, $menores);
                $monto_total_inicial = $precio_noche * $noches;

                $mysqli->begin_transaction();

                // ================= INSERT CAMPAMENTO =================
                $query_campamento = "
                    INSERT INTO campamentos 
                    (num_carpa, dni, apellido, nombre, localidad, patente, telefono, 
                     mayores, menores, categoria, fecha_ingreso, fecha_vencimiento, senia, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Activo')
                ";

                $stmt = $mysqli->prepare($query_campamento);
                $stmt->bind_param(
                    "issssssiisssd",
                    $num_carpa,
                    $dni,
                    $apellido,
                    $nombre,
                    $localidad,
                    $patente,
                    $telefono,
                    $mayores,
                    $menores,
                    $categoria,
                    $fecha_ingreso,
                    $fecha_egreso,
                    $senia_cobrada
                );
                $stmt->execute();

                $campamento_id = $mysqli->insert_id;

                // ================= INSERT PAGO =================
                $query_pago = "
                    INSERT INTO pagos 
                    (campamento_id, fecha_pago, monto, noches_pagadas, precio_noche_unitario)
                    VALUES (?, CURDATE(), ?, ?, ?)
                ";

                $stmt_pago = $mysqli->prepare($query_pago);
                $stmt_pago->bind_param(
                    "iddi",
                    $campamento_id,
                    $monto_total_inicial,
                    $noches,
                    $precio_noche
                );
                $stmt_pago->execute();

                $mysqli->commit();

                header("Location: index.php");
                exit;
            }
        } catch (Exception $e) {
            $mysqli->rollback();
            $msj = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Campamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#692828] flex flex-col items-center min-h-screen">
<main>

<?php if (!empty($msj)) { ?>
    <div class="bg-red-200 text-red-800 rounded p-3 mb-4 max-w-[900px] mx-auto text-center font-semibold">
        <?= ($msj) ?>
    </div>
<?php } ?> 

<div class="bg-[#f8eb87] px-10 py-6 rounded-xl shadow-md w-[900px] flex flex-col gap-6 m-12">

    <form method="post" class="grid grid-cols-2 gap-6">
        <input type="hidden" name="password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">

        <div class="col-span-2 flex justify-between items-start">
            <a href="home.php" class="bg-[#8f2f2f] text-white px-6 py-3 rounded-full hover:bg-[#6e1f1f] cursor-pointer font-extrabold transition duration-150"> ← INICIO </a>
            <a href="index.php" class="bg-[#8f2f2f] text-white px-6 py-3 rounded-full hover:bg-[#6e1f1f] cursor-pointer font-extrabold transition duration-150"> VER FICHAS</a>
            <div class="text-right">
                <label class="block text-sm mb-1 font-semibold text-red-950 mr-4">NÚM CARPA</label>
                <input type="number" name="num_carpa" id="num_carpa" placeholder="Carpa Nº" required min="1"
                       value="<?= $_POST['num_carpa'] ?? '' ?>"
                       class="border rounded-full px-4 py-2 w-48 text-right bg-[#fffbe6] focus:ring-red-500">
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="text-sm font-semibold text-red-950 ml-4">DNI</label>
                <input type="text" name="dni" id="dni" required value="<?= $_POST['dni'] ?? '' ?>" class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
            </div>
            <div>
                <label class="text-sm font-semibold text-red-950 ml-4">APELLIDO</label>
                <input type="text" name="apellido" id="apellido" required value="<?= $_POST['apellido'] ?? '' ?>" class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
            </div>
            <div>
                <label class="text-sm font-semibold text-red-950 ml-4">NOMBRE</label>
                <input type="text" name="nombre" id="nombre" required value="<?= $_POST['nombre'] ?? '' ?>" class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
            </div>
            <div>
                <label class="text-sm font-semibold text-red-950 ml-4">LOCALIDAD</label>
                <input type="text" name="localidad" id="localidad" required value="<?= $_POST['localidad'] ?? '' ?>" class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
            </div>
            <div>
                <label class="text-sm font-semibold text-red-950 ml-4">TELÉFONO</label>
                <input type="text" name="telefono" id="telefono" required value="<?= $_POST['telefono'] ?? '' ?>" class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
            </div>
            <div>
                <label class="text-sm font-semibold text-red-950 ml-4">PATENTE</label>
                <input type="text" name="patente" id="patente" required value="<?= $_POST['patente'] ?? '' ?>" class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex gap-4 items-center">
                <div>
                    <label class="text-sm font-semibold text-red-950 ml-4">CATEGORÍA</label>
                    <select name="categoria" id="categoria" required class="border rounded-full px-4 py-2 bg-[#fffbe6]">
                        <option value="Carpa" selected>Carpa</option>
                        <option value="Minirodante">Minirodante</option>
                        <option value="Casarodante">Casarodante</option>
                        <option value="Motorhome">Motorhome</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-red-950 ml-2">MAYORES</label>
                    <input type="number" name="mayores" id="mayores" min="1" required value="<?= $_POST['mayores'] ?? 1 ?>" class="border rounded-full px-4 py-2 w-20 bg-[#fffbe6] text-center">
                </div>
                <div>
                    <label class="text-sm font-semibold text-red-950 ml-2">MENORES</label>
                    <input type="number" name="menores" id="menores" min="0" required value="<?= $_POST['menores'] ?? 0 ?>" class="border rounded-full px-4 py-2 w-20 bg-[#fffbe6] text-center">
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-sm font-semibold text-red-950 ml-4">INGRESO</label>
                    <input type="date" name="fecha_ingreso" id="fecha_ingreso" required value="<?= date('Y-m-d') ?>" class="border rounded-full px-4 py-2 bg-gray-200 cursor-not-allowed">
                </div>
                <div>
                    <label class="text-sm font-semibold text-red-950 ml-4">VENCE HASTA</label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" required value="<?= $_POST['fecha_vencimiento'] ?? date('Y-m-d', strtotime('+1 day')) ?>" class="border rounded-full px-4 py-2 bg-[#fffbe6]">
                </div>
            </div>

            <div class="bg-yellow-100 p-4 rounded-xl border border-yellow-400">
                <div class="flex items-center gap-2 mb-2">
                    <label class="text-l font-semibold text-red-950 ml-4">SEÑA ($):</label>
                    <input type="number" name="senia_cobrada" id="senia_cobrada" value="<?= SENIA ?>" step="100" class="border rounded-full px-3 py-1 w-32 bg-[#fffbe6] font-bold text-red-950">
                </div>
                <p class="text-l mb-2 font-semibold text-red-950 ml-4">PRECIO X NOCHE: <span id="precioPorNoche" class="font-bold">$0</span></p>
                <p class="text-xl font-bold text-red-950 ml-4">TOTAL: <span id="precioTotal" class="font-bold">$0</span></p>
            </div>

            <div>
                <input type="submit" value="REGISTRAR Y COBRAR" class="bg-[#8f2f2f] text-white px-6 py-3 rounded-full hover:bg-[#6e1f1f] cursor-pointer font-extrabold w-full transition duration-150">
            </div>
        </div>
    </form>
</div>
</main>

<script>
function calcularPrecio() {
    const mayores = parseInt(document.getElementById('mayores').value) || 0;
    const menores = parseInt(document.getElementById('menores').value) || 0;
    const categoria = document.getElementById('categoria').value;
    const fechaIngreso = document.getElementById('fecha_ingreso').value;
    const fechaEgreso = document.getElementById('fecha_vencimiento').value;

    let noches = 0;
    if (fechaIngreso && fechaEgreso) {
        const ingreso = new Date(fechaIngreso);
        const egreso = new Date(fechaEgreso);
        noches = Math.floor((egreso.getTime() - ingreso.getTime()) / (1000 * 60 * 60 * 24));
        if (noches < 0) noches = 0;
    }

    const PRECIO_MAYOR = 15000;
    const PRECIO_MENOR = 12000;
    const ADICIONAL_CASARODANTE = 10000;
    const ADICIONAL_MINIRODANTE = 8000;
    const ADICIONAL_MOTORHOME = 12000;

    let precioNoche = (mayores * PRECIO_MAYOR) + (menores * PRECIO_MENOR);
    if (categoria === 'Minirodante') precioNoche += ADICIONAL_MINIRODANTE;
    else if (categoria === 'Casarodante') precioNoche += ADICIONAL_CASARODANTE;
    else if (categoria === 'Motorhome') precioNoche += ADICIONAL_MOTORHOME;

    const precioTotal = precioNoche * noches;
    const formatoMoneda = (monto) => monto.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

    document.getElementById('precioPorNoche').textContent = `$${formatoMoneda(precioNoche)}`;
    document.getElementById('precioTotal').textContent = `$${formatoMoneda(precioTotal)}`;
}

['mayores','menores','categoria','fecha_ingreso','fecha_vencimiento'].forEach(id => {
    document.getElementById(id).addEventListener('input', calcularPrecio);
});
window.onload = calcularPrecio;
</script>
</body>
</html>