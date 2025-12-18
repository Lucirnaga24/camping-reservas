<?php
// Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); 
define('DB_PASS', ''); 
define('DB_NAME', 'arroyo'); 

// Precios Fijos
define('PRECIO_MAYOR', 15000);
define('PRECIO_MENOR', 12000);
define('ADICIONAL_MINIRODANTE', 8000);
define('ADICIONAL_CASARODANTE', 10000);
define('ADICIONAL_MOTORHOME', 12000);
define('SENIA', 10000.00);

// Conexión
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Error de conexión a la base de datos: " . $mysqli->connect_error);
}

// ==========================================
// CONFIGURACIÓN DE FECHA ARGENTINA
// ==========================================
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Sincronizar la zona horaria de MySQL con la de Argentina
$now = new DateTime();
$mins = $now->getOffset() / 60;
$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;
$offset = sprintf('%+03d:%02d', $hrs * $sgn, $mins);
$mysqli->query("SET time_zone='$offset'");
// ==========================================

// Establecer el conjunto de caracteres a UTF-8
$mysqli->set_charset("utf8mb4");

/**
 * Calcula el precio base por noche según la categoría y el número de personas.
 * @param string $categoria
 * @param int $mayores
 * @param int $menores
 * @return float
 */
function calcularPrecioNoche($categoria, $mayores, $menores) {
    $precio = ($mayores * PRECIO_MAYOR) + ($menores * PRECIO_MENOR);
    
    if (strtolower($categoria) === 'minirodante') {
        $precio += ADICIONAL_MINIRODANTE;
    } elseif (strtolower($categoria) === 'motorhome') {
        $precio += ADICIONAL_MOTORHOME;
    } elseif (strtolower($categoria) === 'casarodante') {
            $precio += ADICIONAL_CASARODANTE;
    }
    return $precio;
}

/**
 * Limpia y formatea un texto para la base de datos.
 * @param string $texto
 * @return string
 */
function limpiarTexto($texto) {
    global $mysqli;
    $texto = trim($texto);
    // Aplicamos escape para evitar errores de SQL y formateamos
    $textoClean = $mysqli->real_escape_string($texto);
    return ucfirst(strtolower($textoClean));
}

/**
 * Redirige a una URL y termina la ejecución.
 * @param string $url
 */
function redirigir($url) {
    header("Location: $url");
    exit;
}

/**
 * Formatea un número como moneda (ej: 12.000).
 * @param float $monto
 * @return string
 */
function formatoMoneda($monto) {
    return number_format($monto, 0, ',', '.');
}