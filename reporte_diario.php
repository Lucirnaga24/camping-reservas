<?php
require_once("conexion.php");

// ==========================================
// 1. BLOQUE DE SEGURIDAD
// ==========================================
$password_correcta = "admin120";
$acceso_concedido = false;

if (isset($_POST['password']) && $_POST['password'] === $password_correcta) {
    $acceso_concedido = true;
}

if (!$acceso_concedido) {
?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Acceso Restringido</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-[#6C2E2C] flex items-center justify-center h-screen">
        <div class="bg-[#FDF28F] p-8 rounded-xl shadow-2xl w-full max-w-sm text-center">
            <h1 class="text-2xl font-bold mb-4 text-[#6C2E2C]">REPORTE DIARIO</h1>
            <p class="mb-4 text-m font-medium text-[#6C2E2C]">Ingrese la contraseña para ver caja</p>
            <form method="POST">
                <input type="password" name="password" placeholder="Contraseña" autofocus
                       class="w-full p-3 rounded-full mb-4 text-center focus:outline-none focus:ring-2 focus:ring-[#6C2E2C]">
                <?php if (isset($_POST['password'])) { ?>
                    <p class="text-red-600 font-bold text-sm mb-4">Contraseña Incorrecta</p>
                <?php } ?>
                <button type="submit" class="bg-[#6C2E2C] text-white px-6 py-2 rounded-full font-bold w-full hover:bg-red-900 transition">
                    VER REPORTE
                </button>
            </form>
            <div class="mt-4 text-sm">
                <a href="home.php" class="text-[#6C2E2C] underline font-semibold">← Volver al inicio</a>
            </div>
        </div>
    </body>
    </html>
<?php
    exit();
}

$hoy = date('Y-m-d');

// --- INGRESOS (Sumamos todos los pagos realizados hoy) ---
$query_ingresos = "SELECT SUM(monto) AS total FROM pagos WHERE fecha_pago = ?";
$stmt_i = $mysqli->prepare($query_ingresos);
$stmt_i->bind_param("s", $hoy);
$stmt_i->execute();
$ingreso_total_bruto = $stmt_i->get_result()->fetch_assoc()['total'] ?? 0;

// --- EGRESOS (Sumamos las señas originales de los que se fueron hoy y se les devolvió) ---
// La condición senia = 0 significa que en egreso.php elegiste "Devolver"
$query_egresos = "SELECT SUM(senia_original) AS total_egreso, COUNT(id) AS cant 
                  FROM campamentos 
                  WHERE estado = 'Egreso' AND senia = 0 AND fecha_egreso = ?";
$stmt_e = $mysqli->prepare($query_egresos);
$stmt_e->bind_param("s", $hoy);
$stmt_e->execute();
$res_e = $stmt_e->get_result()->fetch_assoc();

$total_egresos_reales = $res_e['total_egreso'] ?? 0;
$cantidad_egresos = $res_e['cant'] ?? 0;

// --- NETO FINAL ---
$ingreso_total_neto = $ingreso_total_bruto - $total_egresos_reales;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#6C2E2C] text-black p-4 m-2">
<main>
    <div class="max-w-3xl mx-auto bg-[#FDF28F] py-6 px-10 mt-12 rounded-xl shadow-2xl">
        <h1 class="text-2xl font-black mb-6 text-[#6C2E2C] text-center uppercase pb-2">
            REPORTE DIARIO (<?= date('d/m/Y') ?>)
        </h1>
        
        <div class="space-y-4">
            <div class="pt-4 bg-yellow-100 p-5 rounded-2xl border border-yellow-300">
                <p class="text-sm font-bold text-[#6C2E2C] uppercase">Total Dinero Ingresado:</p>
                <p class="text-4xl font-black text-[#8f2f2f]">$<?= formatoMoneda($ingreso_total_bruto) ?></p>
            </div>

            <div class="pt-4 bg-yellow-100 p-5 rounded-2xl border border-yellow-300">
                <p class="text-sm font-bold text-[#6C2E2C] uppercase">Devolución de Señas Reales (<?= $cantidad_egresos ?> pers.):</p>
                <p class="text-4xl font-black text-[#8f2f2f]">-$<?= formatoMoneda($total_egresos_reales) ?></p>
            </div>

            <div class="pt-6 bg-white p-6 rounded-2xl border-2 border-[#6C2E2C] shadow-inner">
                <p class="text-sm font-bold text-[#6C2E2C] uppercase">Saldo Neto en Efectivo:</p>
                <p class="text-3xl font-black text-green-800">$<?= formatoMoneda($ingreso_total_neto) ?></p>
            </div>
        </div>
        
        <div class="text-center mt-10">
            <a href="index.php" class="bg-[#6C2E2C] text-white px-10 py-4 rounded-full font-extrabold inline-block hover:bg-black transition duration-150 uppercase shadow-lg">
                ← Salir al Listado
            </a>
        </div>
    </div>
</main>
</body>
</html>