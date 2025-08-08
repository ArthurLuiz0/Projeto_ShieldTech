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

// Verificar e criar tabela de usuários se não existir
$check_usuarios = mysqli_query($conn, "SHOW TABLES LIKE 'tb_usuarios'");
if (mysqli_num_rows($check_usuarios) == 0) {
    $create_usuarios = "
    CREATE TABLE tb_usuarios (
        id_usuario INT AUTO_INCREMENT PRIMARY KEY,
        nome_completo VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        tipo_usuario ENUM('admin', 'funcionario', 'morador') DEFAULT 'morador',
        status ENUM('ativo', 'inativo', 'bloqueado') DEFAULT 'ativo',
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ultimo_login TIMESTAMP NULL,
        tentativas_login INT DEFAULT 0,
        token_reset VARCHAR(255) NULL,
        token_expira TIMESTAMP NULL,
        INDEX idx_email (email),
        INDEX idx_status (status),
        INDEX idx_tipo (tipo_usuario)
    )";
    mysqli_query($conn, $create_usuarios);
    
    // Inserir usuário administrador padrão
    $senha_admin = password_hash('admin123', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO tb_usuarios (nome_completo, email, senha, tipo_usuario, status) 
                        VALUES ('Administrador', 'admin@shieldtech.com', '$senha_admin', 'admin', 'ativo')");
}

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