<?php
include("../../conectarbd.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    $sql = "DELETE FROM tb_animais WHERE id_animais = $id";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Animal excluído com sucesso!'); window.location = 'consultar_animais.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir animal: " . mysqli_error($conn) . "'); window.location = 'consultar_animais.php';</script>";
    }
} else {
    echo "<script>alert('ID inválido!'); window.location = 'consultar_animais.php';</script>";
}

mysqli_close($conn);
?>