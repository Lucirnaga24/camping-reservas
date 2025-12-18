<?php
require_once("conexion.php");

// Función de respaldo en caso de que no esté definida en conexion.php
if (!function_exists('formatoMoneda')) {
    function formatoMoneda($valor) {
        return number_format($valor, 0, ',', '.');
    }
}

// Lógica de búsqueda
$filtro = "";
$busqueda_actual = $_GET['buscar'] ?? '';

if (!empty($busqueda_actual)) {
    $buscar = $mysqli->real_escape_string($busqueda_actual);
    // Filtramos por nombre del producto O por categoría
    $filtro = "WHERE nombre LIKE '%$buscar%' OR categoria LIKE '%$buscar%'"; 
}

// Ordenamos por categoría para asegurar el agrupamiento
$query = "SELECT * FROM productos $filtro ORDER BY categoria ASC, nombre ASC";
$result = $mysqli->query($query);

// Agrupar los productos por categoría
$productos_agrupados = [];
if ($result) {
    while ($fila = $result->fetch_assoc()) {
        $productos_agrupados[$fila['categoria']][] = $fila;
    }
}

// Definimos las categorías para los botones rápidos
$categorias_rapidas = ['bebidas', 'helados', 'almacen', 'higiene', 'varios'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Precios | Camping</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body class="bg-[#6C2E2C] text-black px-4 md:px-20 py-12">
<main>
    <div class="max-w-7xl mx-auto">       
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div class="flex flex-wrap gap-4">
                <a href="home.php" class="bg-[#8f2f2f] text-white font-medium px-6 py-3 rounded-full hover:bg-red-800 transition-colors">
                    ← INICIO
                </a>
                <a href="ingresar_producto.php" class="bg-[#FDF28F] text-black font-medium px-6 py-3 rounded-full hover:brightness-110 transition-all">
                    + NUEVO PRODUCTO
                </a>
            </div>

            <form method="GET" class="flex gap-2 w-full md:w-auto">
                <input type="text" name="buscar" placeholder="Buscar producto o categoría"
                       class="bg-[#F7F4BF] text-black px-4 py-2 rounded-full w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-[#FDF28F]"
                       value="<?= htmlspecialchars($busqueda_actual) ?>">
                <button type="submit" class="bg-[#FDF28F] text-black font-medium px-4 py-2 rounded-full hover:brightness-110 transition-all">
                    BUSCAR
                </button>
            </form>
        </div>

        <div class="flex flex-wrap justify-center gap-3 mb-10">
            <a href="?" class="px-5 py-2 rounded-full font-bold text-xs transition-all <?= empty($busqueda_actual) ? 'bg-white text-[#6C2E2C]' : 'bg-[#8f2f2f] text-white hover:bg-red-700' ?>">
                VER TODO
            </a>

            <?php foreach ($categorias_rapidas as $cat): ?>
                <a href="?buscar=<?= urlencode($cat) ?>" 
                   class="px-5 py-2 rounded-full font-bold text-xs uppercase transition-all 
                   <?= (strtolower($busqueda_actual) === $cat) ? 'bg-white text-[#6C2E2C] ring-2 ring-[#FDF28F]' : 'bg-[#FDF28F] text-black hover:brightness-110' ?>">
                    <?= $cat ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($productos_agrupados)) {
            echo "<div class='bg-white/10 p-10 rounded-2xl text-center border-2 border-dashed border-[#FDF28F]/30'>
                    <p class='text-[#FDF28F] text-xl font-medium'>No se encontraron productos en esta categoría o búsqueda.</p>
                    <a href='?' class='text-white underline mt-4 inline-block'>Mostrar todos los productos</a>
                  </div>";
        } else { ?>

            <?php foreach ($productos_agrupados as $categoria => $productos) { ?>
                
                <div class="bg-white p-4 md:p-8 rounded-2xl shadow-2xl mb-10 overflow-hidden">
                    
                    <h2 class="text-xl font-extrabold text-[#6C2E2C] mb-6 border-b-2 border-gray-100 pb-3 uppercase flex items-center">
                        <span class="mr-2">🛒</span> <?= htmlspecialchars($categoria) ?>
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full table-fixed min-w-[700px] divide-y divide-gray-200">
                            <thead>
                                <tr class="text-gray-400 uppercase text-[10px] font-bold tracking-widest">
                                    <th class="w-24 px-4 py-3 text-left">Imagen</th>
                                    <th class="w-auto px-4 py-3 text-left">Producto</th>
                                    <th class="w-48 px-4 py-3 text-left">Categoría</th>
                                    <th class="w-32 px-4 py-3 text-left">Precio</th>
                                    <th class="w-40 px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php foreach ($productos as $fila) { ?>
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <?php 
                                            $imagen_path = 'img_productos/' . $fila['imagen']; 
                                            if (!empty($fila['imagen']) && file_exists($imagen_path)) {
                                                echo "<img src='$imagen_path' alt='{$fila['nombre']}' class='w-16 h-16 object-cover rounded-lg shadow-sm group-hover:scale-105 transition-transform'>";
                                            } else {
                                                echo "<div class='w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-[10px] text-gray-400 border border-dashed border-gray-300'>Sin foto</div>";
                                            }
                                            ?>
                                        </td>

                                        <td class="px-4 py-4">
                                            <div class="text-lg font-bold text-gray-800 text-truncate" title="<?= htmlspecialchars($fila['nombre']) ?>">
                                                <?= htmlspecialchars($fila['nombre']) ?>
                                            </div>
                                        </td>

                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-[10px] font-bold uppercase">
                                                <?= htmlspecialchars($fila['categoria']) ?>
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            <div class="text-xl font-black text-green-600">
                                                $<?= formatoMoneda($fila['precio']) ?>
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 text-right text-sm font-medium">
                                            <a href="editar_producto.php?id=<?= $fila['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</a>
                                            <a href="borrar_producto.php?id=<?= $fila['id'] ?>" class="text-red-600 hover:text-red-800 borrar-producto">Borrar</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>

        <?php } ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const borrarLinks = document.querySelectorAll('.borrar-producto');
    borrarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
</body>
</html>