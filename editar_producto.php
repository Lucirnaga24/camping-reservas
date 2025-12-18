<?php
require_once("conexion.php");

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
$msj = "";
$categorias = ['bebidas', 'helados', 'varios', 'higiene', 'almacen'];

// 1. Obtener datos del producto
$query_data = "SELECT * FROM productos WHERE id = ?";
$stmt_data = $mysqli->prepare($query_data);
$stmt_data->bind_param("i", $id);
$stmt_data->execute();
$result_data = $stmt_data->get_result();

if ($result_data->num_rows === 0) {
    die("Producto no encontrado.");
}
$producto = $result_data->fetch_assoc();


if (isset($_POST['nombre'])) {
    $nombre = $mysqli->real_escape_string(limpiarTexto($_POST['nombre'] ?? ""));
    $precio = floatval($_POST['precio'] ?? 0);
    $categoria = $_POST['categoria'] ?? 'varios';
    $nombre_imagen = $producto['imagen']; // Mantener la imagen actual por defecto

    // Validación de categoría
    if (!in_array($categoria, $categorias)) {
        $categoria = 'varios';
    }

    if (empty($nombre) || $precio <= 0) {
        $msj = "El nombre y el precio son obligatorios.";
    } else {
        // Lógica de subida y reemplazo de archivo
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0 && !empty($_FILES['imagen']['name'])) {
            $carpeta_destino = "img_productos/";
            
            // Eliminar la imagen anterior si existe
            if ($producto['imagen'] && file_exists($carpeta_destino . $producto['imagen'])) {
                unlink($carpeta_destino . $producto['imagen']);
            }
            
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombre_imagen = time() . "_" . uniqid() . "." . $extension;
            $ruta_completa = $carpeta_destino . $nombre_imagen;
            
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa)) {
                $msj = "Error al subir la nueva imagen.";
                $nombre_imagen = $producto['imagen']; // Revertir si hay error
            }
        }
        
        if (empty($msj)) {
            // Se actualiza la query para incluir la categoría y quitar el stock
            $query_update = "UPDATE productos SET nombre = ?, precio = ?, categoria = ?, imagen = ? WHERE id = ?";
            $stmt_update = $mysqli->prepare($query_update);
            $stmt_update->bind_param("sdssi", $nombre, $precio, $categoria, $nombre_imagen, $id);

            if ($stmt_update->execute()) {
                // Recargar los datos actualizados para mostrarlos en el formulario
                $stmt_data->execute();
                $producto = $stmt_data->get_result()->fetch_assoc();
                $msj = "Producto actualizado correctamente.";
            } else {
                $msj = "Error al actualizar el producto: " . $mysqli->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#692828] flex flex-col items-center min-h-screen">
<main>

<?php if (!empty($msj)) { ?>
    <div class="bg-green-200 text-green-800 rounded p-3 mb-4 max-w-[600px] mx-auto text-center font-semibold">
        <?= ($msj) ?>
    </div>
<?php } ?>

<div class="bg-[#f8eb87] px-10 py-6 rounded-xl shadow-md w-[600px] flex flex-col gap-6 m-12">
    <h1 class="text-3xl font-bold text-[#6C2E2C] text-center">✏️ EDITAR PRODUCTO</h1>

    <form method="post" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="id" value="<?= $producto['id'] ?>">

        <div class="text-center">
            <?php 
            $imagen_path = 'img_productos/' . $producto['imagen']; 
            if ($producto['imagen'] && file_exists($imagen_path)) {
                echo "<img src='$imagen_path' alt='{$producto['nombre']}' class='w-32 h-32 object-cover rounded-md mx-auto mb-2'>";
                echo "<p class='text-xs text-gray-600'>Imagen actual</p>";
            } else {
                echo "<div class='w-32 h-32 bg-gray-200 rounded-md mx-auto mb-2 flex items-center justify-center text-sm'>Sin imagen</div>";
            }
            ?>
        </div>
        
        <div>
            <label class="text-sm font-semibold text-red-950 ml-4">NOMBRE DEL PRODUCTO</label>
            <input type="text" name="nombre" placeholder="Nombre del producto" required
                   value="<?= htmlspecialchars($producto['nombre']) ?>"
                   class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
        </div>
        
        <div class="flex gap-4">
            <div class="w-1/2">
                <label class="text-sm font-semibold text-red-950 ml-4">PRECIO ($)</label>
                <input type="number" step="0.01" name="precio" placeholder="Precio de venta" required
                       value="<?= $producto['precio'] ?>"
                       class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
            </div>
            <div class="w-1/2">
                <label class="text-sm font-semibold text-red-950 ml-4">CATEGORÍA</label>
                <select name="categoria" class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
                    <?php 
                    $current_cat = $_POST['categoria'] ?? $producto['categoria'] ?? 'varios';
                    foreach ($categorias as $cat) {
                        $selected = ($cat === $current_cat) ? 'selected' : '';
                        echo "<option value='{$cat}' {$selected}>" . ucfirst($cat) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div>
            <label class="text-sm font-semibold text-red-950 ml-4">REEMPLAZAR IMAGEN</label>
            <input type="file" name="imagen" accept=".jpg, .jpeg, .png"
                   class="w-full border rounded-full px-4 py-2 bg-[#fffbe6] text-sm">
            <p class="text-xs text-gray-600 ml-4 mt-1">Si subes una nueva imagen, reemplazará la anterior.</p>
        </div>

        <input type="submit" value="GUARDAR CAMBIOS"
               class="bg-[#8f2f2f] text-white px-6 py-3 rounded-full hover:bg-[#6e1f1f] cursor-pointer font-extrabold w-full transition duration-150">
    </form>
    
    <div class="text-center">
        <a href="productos.php" class="text-[#8f2f2f] underline font-semibold">← Volver al Listado de Precios</a>
    </div>
</div>
</main>
</body>
</html>