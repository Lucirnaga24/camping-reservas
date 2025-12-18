<?php
require_once("conexion.php");

$msj = "";
$categorias = ['bebidas', 'helados', 'varios', 'higiene', 'almacen'];

if (isset($_POST['nombre'])) {
    $nombre = $mysqli->real_escape_string(limpiarTexto($_POST['nombre'] ?? ""));
    $precio = floatval($_POST['precio'] ?? 0);
    $categoria = $_POST['categoria'] ?? 'varios'; // Asume 'varios' si no se selecciona
    $nombre_imagen = null;

    // Validación de categoría
    if (!in_array($categoria, $categorias)) {
        $categoria = 'varios';
    }

    if (empty($nombre) || $precio <= 0) {
        $msj = "El nombre y el precio son obligatorios.";
    } else {
        // Lógica de subida de archivo
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $carpeta_destino = "img_productos/";
            
            if (!is_dir($carpeta_destino)) {
                mkdir($carpeta_destino, 0777, true);
            }
            
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombre_imagen = time() . "_" . uniqid() . "." . $extension;
            $ruta_completa = $carpeta_destino . $nombre_imagen;
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa)) {
                // Archivo movido con éxito
            } else {
                $msj = "Error al subir la imagen.";
                $nombre_imagen = null;
            }
        }
        
        if (empty($msj)) {
            // Se actualiza la query para incluir la categoría y quitar el stock
            $query = "INSERT INTO productos (nombre, precio, categoria, imagen) VALUES (?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("sdss", $nombre, $precio, $categoria, $nombre_imagen);

            if ($stmt->execute()) {
                redirigir("productos.php?msj=" . urlencode("Producto '$nombre' ingresado correctamente."));
            } else {
                $msj = "Error al ingresar el producto: " . $mysqli->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingresar Producto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#692828] flex flex-col items-center min-h-screen">
<main>

<?php if (!empty($msj)) { ?>
    <div class="bg-red-200 text-red-800 rounded p-3 mb-4 max-w-[600px] mx-auto text-center font-semibold">
        <?= ($msj) ?>
    </div>
<?php } ?>

<div class="bg-[#f8eb87] px-10 py-6 rounded-xl shadow-md w-[600px] flex flex-col gap-6 m-12">
    <h1 class="text-3xl font-bold text-[#6C2E2C] text-center"> + INGRESAR NUEVO PRODUCTO</h1>

    <form method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="text-sm font-semibold text-red-950 ml-4">NOMBRE DEL PRODUCTO</label>
            <input type="text" name="nombre" placeholder="Ej: Gaseosa 1.5L" required
                   value="<?= $_POST['nombre'] ?? '' ?>"
                   class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
        </div>
        
        <div class="flex gap-4">
            <div class="w-1/2">
                <label class="text-sm font-semibold text-red-950 ml-4">PRECIO ($)</label>
                <input type="number" step="0.01" name="precio" placeholder="Precio de venta" required
                       value="<?= $_POST['precio'] ?? '' ?>"
                       class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
            </div>
            <div class="w-1/2">
                <label class="text-sm font-semibold text-red-950 ml-4">CATEGORÍA</label>
                <select name="categoria" class="w-full border rounded-full px-4 py-2 bg-[#fffbe6]">
                    <?php 
                    $current_cat = $_POST['categoria'] ?? 'varios';
                    foreach ($categorias as $cat) {
                        $selected = ($cat === $current_cat) ? 'selected' : '';
                        echo "<option value='{$cat}' {$selected}>" . ucfirst($cat) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div>
            <label class="text-sm font-semibold text-red-950 ml-4">IMAGEN (JPG, PNG)</label>
            <input type="file" name="imagen" accept=".jpg, .jpeg, .png"
                   class="w-full border rounded-full px-4 py-2 bg-[#fffbe6] text-sm">
        </div>

        <input type="submit" value="GUARDAR PRODUCTO"
               class="bg-[#8f2f2f] text-white px-6 py-3 rounded-full hover:bg-[#6e1f1f] cursor-pointer font-extrabold w-full transition duration-150">
    </form>
    
    <div class="text-center">
        <a href="productos.php" class="text-[#8f2f2f] underline font-semibold">← Volver al Listado de Precios</a>
    </div>
</div>
</main>
</body>
</html>