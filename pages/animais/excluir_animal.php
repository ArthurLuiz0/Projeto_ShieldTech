<?php
include("../../conectarbd.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    // Buscar o id do morador antes de excluir
    $buscar_morador = mysqli_query($conn, "SELECT id_morador FROM tb_animais WHERE id_animais = $id");
    $morador_data = mysqli_fetch_array($buscar_morador);
    $id_morador = $morador_data['id_morador'];
    
    $sql = "DELETE FROM tb_animais WHERE id_animais = $id";
    
    if (mysqli_query($conn, $sql)) {
        // Verificar se o morador ainda tem outros animais
        $outros_animais = mysqli_query($conn, "SELECT id_animais FROM tb_animais WHERE id_morador = $id_morador");
        if (mysqli_num_rows($outros_animais) == 0) {
            // Se não tem mais animais, atualizar status para "Não possui"
            mysqli_query($conn, "UPDATE tb_moradores SET animais = 'Não possui' WHERE id_moradores = $id_morador");
        }
        echo "<script>alert('Animal excluído com sucesso!'); window.location = 'consultar_animais.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir animal: " . mysqli_error($conn) . "'); window.location = 'consultar_animais.php';</script>";
    }
} else {
    echo "<script>alert('ID inválido!'); window.location = 'consultar_animais.php';</script>";
}

mysqli_close($conn);
?>