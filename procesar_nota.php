<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    // Usamos tu función limpiarTexto para seguridad
    $titulo = limpiarTexto($_POST['titulo']);
    $contenido = $mysqli->real_escape_string($_POST['contenido']);

    if (!empty($id)) {
        // Actualizar
        $sql = "UPDATE notas_administrativas SET titulo='$titulo', contenido='$contenido' WHERE id=$id";
    } else {
        // Insertar
        $sql = "INSERT INTO notas_administrativas (titulo, contenido) VALUES ('$titulo', '$contenido')";
    }

    if ($mysqli->query($sql)) {
        redirigir('notas.php'); // Usamos tu función redirigir
    } else {
        echo "Error: " . $mysqli->error;
    }
}
?>