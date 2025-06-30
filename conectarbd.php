<?php
$servidor = "localhost";
$dbusuario = "root";
$dbsenha = "";
$dbname = "db_shieldtech";

$conn = mysqli_connect($servidor, $dbusuario, $dbsenha, $dbname);

if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Configurar charset para UTF-8
mysqli_set_charset($conn, "utf8");
?>