<?php
// db.php - conexión a SQLite
$db_file = '/var/www/html/cartridgeanddisc/db/cartridgeanddisc.db';

try {
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
