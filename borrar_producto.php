<?php
require_once("conexion.php");

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $mysqli->begin_transaction();
        
        // 1. Obtener nombre de la imagen para borrarla
        $query_get_img = "SELECT imagen FROM productos WHERE id = ?";
        $stmt_get_img = $mysqli->prepare($query_get_img);
        $stmt_get_img->bind_param("i", $id);
        $stmt_get_img->execute();
        $imagen_data = $stmt_get_img->get_result()->fetch_assoc();

        if ($imagen_data && $imagen_data['imagen']) {
            $ruta_imagen = 'img_productos/' . $imagen_data['imagen'];
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }
        
        // 2. Eliminar el registro del producto
        $query_delete = "DELETE FROM productos WHERE id = ?";
        $stmt_delete = $mysqli->prepare($query_delete);
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();

        if ($stmt_delete->affected_rows > 0) {
            $mysqli->commit();
            redirigir("productos.php?msj=" . urlencode("Producto eliminado correctamente."));
        } else {
            throw new Exception("No se encontró el producto para borrar.");
        }

    } catch (Exception $e) {
        $mysqli->rollback();
        redirigir("productos.php?msj=" . urlencode("Error al borrar el producto: " . $e->getMessage()));
    }
} else {
    redirigir("productos.php");
}
?>