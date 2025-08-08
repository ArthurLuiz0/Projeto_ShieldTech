/*
  # Criação da tabela de usuários para sistema de login

  1. Nova Tabela
    - `tb_usuarios`
      - `id_usuario` (int, primary key, auto increment)
      - `nome_completo` (varchar 100, nome do usuário)
      - `email` (varchar 100, unique, email para login)
      - `senha` (varchar 255, senha criptografada)
      - `tipo_usuario` (enum, tipo: admin, funcionario, morador)
      - `status` (enum, status: ativo, inativo, bloqueado)
      - `data_criacao` (timestamp, data de criação)
      - `ultimo_login` (timestamp, último acesso)
      - `tentativas_login` (int, controle de tentativas)
      - `token_reset` (varchar 255, token para reset de senha)
      - `token_expira` (timestamp, expiração do token)

  2. Segurança
    - Email único para evitar duplicatas
    - Controle de tentativas de login
    - Sistema de reset de senha
    - Diferentes tipos de usuário
*/

CREATE TABLE IF NOT EXISTS tb_usuarios (
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
);

-- Inserir usuário administrador padrão
INSERT INTO tb_usuarios (nome_completo, email, senha, tipo_usuario, status) 
VALUES ('Administrador', 'admin@shieldtech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ativo')
ON DUPLICATE KEY UPDATE email = email;

-- Comentário: A senha padrão é "password" (criptografada com password_hash do PHP)