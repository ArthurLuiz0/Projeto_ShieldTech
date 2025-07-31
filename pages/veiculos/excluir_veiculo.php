<?php
include("../../conectarbd.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    $sql = "DELETE FROM tb_veiculos WHERE id_veiculos = $id";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Veículo excluído com sucesso!'); window.location = 'consultar_veiculos.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir veículo: " . mysqli_error($conn) . "'); window.location = 'consultar_veiculos.php';</script>";
    }
} else {
    echo "<script>alert('ID inválido!'); window.location = 'consultar_veiculos.php';</script>";
}

mysqli_close($conn);
?>