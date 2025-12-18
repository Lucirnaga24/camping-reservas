<?php
require_once("conexion.php");

// ===============================
// ACTUALIZAR VENCIDOS
// ===============================
$hoy = date('Y-m-d');
$query_check_vencidos = "
    UPDATE campamentos 
    SET estado = 'Vencido' 
    WHERE fecha_vencimiento <= '$hoy' 
    AND estado = 'Activo'
";
$mysqli->query($query_check_vencidos);

// ===============================
// FILTRO BUSQUEDA
// ===============================
$filtro = "";
if (!empty($_GET['buscar'])) {
    $buscar = $mysqli->real_escape_string($_GET['buscar']);
    $filtro = "AND (apellido LIKE '%$buscar%' OR nombre LIKE '%$buscar%' OR dni LIKE '%$buscar%' OR patente LIKE '%$buscar%')";
}

// ===============================
// CONSULTA PRINCIPAL (Ordenada solo por Fecha)
// ===============================
$query = "
    SELECT * FROM campamentos 
    WHERE estado IN ('Activo', 'Vencido')
    $filtro
    ORDER BY fecha_vencimiento ASC, num_carpa ASC
";
$result = $mysqli->query($query);

// ===============================
// CONSULTA HISTORIAL
// ===============================
$query_historial = "
    SELECT * FROM campamentos 
    WHERE estado = 'Egreso'
    $filtro
    ORDER BY id DESC LIMIT 100
";
$result_historial = $mysqli->query($query_historial);

// ===============================
// PAGOS RENOVADOS
// ===============================
$query_total_pagos_renov = "
    SELECT SUM(monto) AS total_renovado 
    FROM pagos 
    WHERE campamento_id = ?
";
$stmt_pagos_renov = $mysqli->prepare($query_total_pagos_renov);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Campamentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#6C2E2C] text-black p-6 m-8">
<main>
<div class="max-w-7xl mx-auto">

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex gap-4">
        <a href="home.php" class="bg-[#FDF28F] px-6 py-3 rounded-full font-bold hover:brightness-110">
        INICIO
        </a>
        <a href="ingresar.php" class="bg-[#FDF28F] px-6 py-3 rounded-full font-medium hover:brightness-110">
            + REGISTRAR CARPA
        </a>
        <a href="reporte_diario.php" class="bg-[#FDF28F] px-6 py-3 rounded-full font-medium hover:brightness-110">
        REPORTE DIARIO
        </a>
    </div>

    <form method="GET" class="flex gap-2">
        <input type="text" name="buscar"
               placeholder="Buscar..."
               value="<?= $_GET['buscar'] ?? '' ?>"
               class="bg-[#F7F4BF] px-4 py-2 rounded-full w-64">
        <button class="bg-[#FDF28F] px-4 py-2 rounded-full font-medium hover:brightness-110">
            BUSCAR
        </button>
    </form>
</div>

<?php
if ($result->num_rows <= 0) {
    echo "<p class='text-[#FDF28F] mb-10'>No hay campamentos registrados.</p>";
} else {
    $fechaGrupo = "";

    while ($fila = $result->fetch_assoc()) {
        $fechaIngreso = date('d/m/Y', strtotime($fila['fecha_ingreso']));
        $fechaVenc = date('d/m/Y', strtotime($fila['fecha_vencimiento']));
        $fechaGrupoActual = $fila['fecha_vencimiento'];

        // Obtener pagos
        $total_renovado = 0;
        $stmt_pagos_renov->bind_param("i", $fila['id']);
        $stmt_pagos_renov->execute();
        $resPago = $stmt_pagos_renov->get_result()->fetch_assoc();
        $total_renovado = $resPago['total_renovado'] ?? 0;

        // SEPARACIÓN POR DÍA (BANNER BLANCO/CREMA)
        if ($fechaGrupo !== $fechaGrupoActual) {
            if ($fechaGrupo !== "") echo "</div>";
            echo "<h4 class='mb-4 mt-4 text-xl font-bold text-[#FDF28F] inline-block px-6 py-2 rounded-full shadow-lg'>
                    VENCIMIENTO: $fechaVenc
                  </h4>";
            echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6'>";
            $fechaGrupo = $fechaGrupoActual;
        }

        // TODO EN AMARILLO (bg-[#FDF28F])
        echo "
        <div class='bg-[#FDF28F] p-6 rounded-3xl shadow-md hover:shadow-xl transition'>
            <p class='font-bold text-3xl text-[#6C2E2C] mb-2'>Nº {$fila['num_carpa']}</p>
            <p class='font-bold text-xl'>{$fila['apellido']}, {$fila['nombre']}</p>
            <p class='font-bold text-l mb-1'>DNI: {$fila['dni']} | Patente: {$fila['patente']}</p>
            <p class='font-bold text-l'>{$fila['localidad']}</p>
            <p class='font-bold text-l mt-2'>Ingreso: $fechaIngreso</p>
            <p class='font-bold text-l'>Mayores: {$fila['mayores']} • Menores: {$fila['menores']}</p>
            <p class='font-bold text-l mt-4 uppercase'>{$fila['categoria']}
            <p class='italic text-xl mt-2 text-gray-700'>Notas: {$fila['nota']}</p>

            <div class='mt-4 pt-3 border-t border-black/10'>
                <p class='text-m font-semibold'>Vence el:</p>
                <p class=' text-2xl font-bold text-[#6C2E2C]'>$fechaVenc</p>
                
                <div class='flex justify-between mt-2 text-m font-semibold'>
                    <span>Seña: \$" . (isset($fila['senia']) ? formatoMoneda($fila['senia']) : '0') . "</span>
                    <span class='text-xl'>Pagos: \$" . formatoMoneda($total_renovado) . "</span>
                </div>
            </div>

            <div class='mt-5 flex gap-2'>
                <a href='gestion_campamento.php?id={$fila['id']}' class='bg-[#8B2522] text-white px-4 py-2 rounded-full text-xs font-bold hover:bg-red-950'>RENOVAR</a>
                <a href='egreso.php?id={$fila['id']}' class='bg-[#8B2522] text-white px-4 py-2 rounded-full text-xs font-bold hover:bg-red-950''>DAR BAJA</a>
            </div>
        </div>";
    }
    echo "</div>";
}
?>

<h2 class="text-xl font-bold mt-24 mb-6 text-[#FDF28F]/50 border-t border-white/10 pt-10 uppercase tracking-tighter">
    Historial de Bajas (Últimos 100)
</h2>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-20">
    <?php
    if ($result_historial && $result_historial->num_rows > 0) {
        while ($h = $result_historial->fetch_assoc()) {
            $fVencHist = date('d/m/Y', strtotime($h['fecha_vencimiento']));
            echo "
            <div class='bg-white/5 p-4 rounded-3xl border border-white/10 text-white/60'>
                <p class='font-bold text-2xl text-[#FDF28F]/80'>#{$h['num_carpa']}</p>
                <p class='font-bold text-sm uppercase truncate'>{$h['apellido']}</p>
                <p class='text-[10px]'>Venció: $fVencHist</p>
                <div class='mt-3 text-center'>
                    <a href='gestion_campamento.php?id={$h['id']}' class='text-[#FDF28F] text-[10px] hover:underline uppercase font-bold'>VER FICHA</a>
                </div>
            </div>";
        }
    }
    ?>
</div>

</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const msj = params.get('msj_ok');
    if (msj) {
        alert(msj);
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

document.querySelectorAll('.borrar').forEach(btn => {
    btn.addEventListener('click', e => {
        if (!confirm('¿Seguro que querés dar de BAJA este campamento?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php if (isset($stmt_pagos_renov)) $stmt_pagos_renov->close(); ?>
</body>
</html>