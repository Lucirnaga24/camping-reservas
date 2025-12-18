<?php
include 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM notas_administrativas WHERE id = $id";

    if ($mysqli->query($sql)) {
        redirigir('notas.php');
    } else {
        echo "Error al eliminar nota.";
    }
}
?>