<?php
$servidor = "localhost";
$dbusuario = "root";
$dbsenha = "1234";
$dbname = "db_shieldtech";

$conn = mysqli_connect($servidor, $dbusuario, $dbsenha, $dbname);

if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}



// Incluir validador de CPF
include_once("php/cpf-validator.php");

// Verificar e criar colunas necessárias se não existirem
$check_reservas = mysqli_query($conn, "SHOW COLUMNS FROM tb_reservas LIKE 'id_morador'");
if (mysqli_num_rows($check_reservas) == 0) {
    mysqli_query($conn, "ALTER TABLE tb_reservas ADD COLUMN id_morador INT");
    mysqli_query($conn, "ALTER TABLE tb_reservas ADD FOREIGN KEY (id_morador) REFERENCES tb_moradores(id_moradores)");
}

$check_animais = mysqli_query($conn, "SHOW COLUMNS FROM tb_animais LIKE 'id_morador'");
if (mysqli_num_rows($check_animais) == 0) {
    mysqli_query($conn, "ALTER TABLE tb_animais ADD COLUMN id_morador INT");
    mysqli_query($conn, "ALTER TABLE tb_animais ADD FOREIGN KEY (id_morador) REFERENCES tb_moradores(id_moradores)");
}

$check_veiculos = mysqli_query($conn, "SHOW COLUMNS FROM tb_veiculos LIKE 'id_morador'");
if (mysqli_num_rows($check_veiculos) == 0) {
    mysqli_query($conn, "ALTER TABLE tb_veiculos ADD COLUMN id_morador INT");
    mysqli_query($conn, "ALTER TABLE tb_veiculos ADD FOREIGN KEY (id_morador) REFERENCES tb_moradores(id_moradores)");
}

// Verificar e criar coluna id_morador na tabela tb_encomendas se não existir
$check_encomendas = mysqli_query($conn, "SHOW COLUMNS FROM tb_encomendas LIKE 'id_morador'");
if (mysqli_num_rows($check_encomendas) == 0) {
    mysqli_query($conn, "ALTER TABLE tb_encomendas ADD COLUMN id_morador INT");
    mysqli_query($conn, "ALTER TABLE tb_encomendas ADD FOREIGN KEY (id_morador) REFERENCES tb_moradores(id_moradores)");
}

// Verificar e criar coluna nome_morador na tabela tb_encomendas se não existir
$check_nome_morador = mysqli_query($conn, "SHOW COLUMNS FROM tb_encomendas LIKE 'nome_morador'");
if (mysqli_num_rows($check_nome_morador) == 0) {
    mysqli_query($conn, "ALTER TABLE tb_encomendas ADD COLUMN nome_morador VARCHAR(100)");
}

// Verificar e criar coluna email na tabela tb_encomendas se não existir
$check_email_encomendas = mysqli_query($conn, "SHOW COLUMNS FROM tb_encomendas LIKE 'email'");
if (mysqli_num_rows($check_email_encomendas) == 0) {
    mysqli_query($conn, "ALTER TABLE tb_encomendas ADD COLUMN email VARCHAR(100)");
}

// Verificar se a coluna veiculo na tb_moradores existe e alterar para indicar se possui veículo
$check_morador_veiculo = mysqli_query($conn, "SHOW COLUMNS FROM tb_moradores LIKE 'veiculo'");
if (mysqli_num_rows($check_morador_veiculo) > 0) {
    // Verificar se é VARCHAR(45) e alterar para indicar apenas se possui ou não
    $column_info = mysqli_fetch_array($check_morador_veiculo);
    if (strpos($column_info['Type'], 'varchar') !== false) {
        // Manter como está para compatibilidade, mas usar para indicar "Possui" ou "Não possui"
    }
}

// Verificar e criar coluna email na tabela tb_moradores se não existir
$check_email_moradores = mysqli_query($conn, "SHOW COLUMNS FROM tb_moradores LIKE 'email'");
if (mysqli_num_rows($check_email_moradores) == 0) {
    mysqli_query($conn, "ALTER TABLE tb_moradores ADD COLUMN email VARCHAR(100)");
}

// Verificar e ajustar estrutura da tabela tb_animais conforme banco de dados
$check_animais_structure = mysqli_query($conn, "SHOW COLUMNS FROM tb_animais");
$columns = [];
while ($col = mysqli_fetch_array($check_animais_structure)) {
    $columns[] = $col['Field'];
}

// Verificar se a coluna id_morador existe na tb_animais
if (!in_array('id_morador', $columns)) {
    mysqli_query($conn, "ALTER TABLE tb_animais ADD COLUMN id_morador INT");
    mysqli_query($conn, "ALTER TABLE tb_animais ADD FOREIGN KEY (id_morador) REFERENCES tb_moradores(id_moradores)");
}
?>