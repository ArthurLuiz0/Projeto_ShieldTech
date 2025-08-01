<?php
include("../../conectarbd.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    // Buscar o id do morador antes de excluir
    $buscar_morador = mysqli_query($conn, "SELECT id_morador FROM tb_veiculos WHERE id_veiculos = $id");
    $morador_data = mysqli_fetch_array($buscar_morador);
    $id_morador = $morador_data['id_morador'];
    
    $sql = "DELETE FROM tb_veiculos WHERE id_veiculos = $id";
    
    if (mysqli_query($conn, $sql)) {
        // Verificar se o morador ainda tem outros veículos
        $outros_veiculos = mysqli_query($conn, "SELECT id_veiculos FROM tb_veiculos WHERE id_morador = $id_morador");
        if (mysqli_num_rows($outros_veiculos) == 0) {
            // Se não tem mais veículos, atualizar status para "Não possui"
            mysqli_query($conn, "UPDATE tb_moradores SET veiculo = 'Não possui' WHERE id_moradores = $id_morador");
        }
        echo "<script>alert('Veículo excluído com sucesso!'); window.location = 'consultar_veiculos.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir veículo: " . mysqli_error($conn) . "'); window.location = 'consultar_veiculos.php';</script>";
    }
} else {
    echo "<script>alert('ID inválido!'); window.location = 'consultar_veiculos.php';</script>";
}

mysqli_close($conn);
?>