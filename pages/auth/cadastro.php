<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/validation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    session_start();
    include("../../conectarbd.php");
    
    $erro = '';
    $sucesso = '';
    
    // Processar cadastro
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'cadastro') {
        $nome_completo = mysqli_real_escape_string($conn, trim($_POST["nome_completo"]));
        $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
        $senha = $_POST["senha"];
        $confirmar_senha = $_POST["confirmar_senha"];
        $tipo_usuario = mysqli_real_escape_string($conn, $_POST["tipo_usuario"]);
        
        // Validações
        if (empty($nome_completo) || empty($email) || empty($senha) || empty($confirmar_senha)) {
            $erro = "Por favor, preencha todos os campos.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = "Email inválido.";
        } elseif (strlen($senha) < 6) {
            $erro = "A senha deve ter pelo menos 6 caracteres.";
        } elseif ($senha !== $confirmar_senha) {
            $erro = "As senhas não coincidem.";
        } else {
            // Verificar se email já existe
            $verificar_email = mysqli_query($conn, "SELECT id_usuario FROM tb_usuarios WHERE email = '$email'");
            
            if (mysqli_num_rows($verificar_email) > 0) {
                $erro = "Este email já está cadastrado.";
            } else {
                // Criptografar senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                // Inserir usuário
                $sql = "INSERT INTO tb_usuarios (nome_completo, email, senha, tipo_usuario, status, data_criacao) 
                        VALUES ('$nome_completo', '$email', '$senha_hash', '$tipo_usuario', 'ativo', CURRENT_TIMESTAMP)";
                
                if (mysqli_query($conn, $sql)) {
                    $sucesso = "Conta criada com sucesso! Você pode fazer login agora.";
                    // Limpar campos
                    $_POST = array();
                } else {
                    $erro = "Erro ao criar conta. Tente novamente.";
                }
            }
        }
    }
    ?>

    <div class="container">
        <div class="form-container">
            <div class="header">
                <i class="fas fa-shield-alt" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h1>ShieldTech</h1>
                <p>Criar Nova Conta</p>
            </div>

            <?php if ($erro): ?>
                <div class="error-message" style="display: block;">
                    <i class="fas fa-exclamation-triangle"></i> <?= $erro ?>
                </div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="success-message" style="display: block; color: var(--success-color); background-color: rgba(46, 204, 113, 0.1); border: 1px solid var(--success-color); padding: 0.5rem; border-radius: 0.25rem; margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i> <?= $sucesso ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="action" value="cadastro">
                
                <div class="input-field">
                    <label for="nome_completo">Nome Completo</label>
                    <div class="input-container">
                        <i class="fas fa-user"></i>
                        <input type="text" id="nome_completo" name="nome_completo" placeholder="Digite seu nome completo" required value="<?= isset($_POST['nome_completo']) ? htmlspecialchars($_POST['nome_completo']) : '' ?>">
                    </div>
                </div>

                <div class="input-field">
                    <label for="email">Email</label>
                    <div class="input-container">
                        <i class="fas fa-envelope"></i>
                        <div class="email-validation">
                            <input type="email" id="email" name="email" placeholder="Digite seu email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            <span class="validation-icon" id="email-icon"></span>
                        </div>
                    </div>
                    <div class="email-error" id="email-error"></div>
                </div>

                <div class="input-field">
                    <label for="tipo_usuario">Tipo de Usuário</label>
                    <div class="input-container">
                        <i class="fas fa-user-tag"></i>
                        <select id="tipo_usuario" name="tipo_usuario" class="no-icon" required>
                            <option value="">Selecione o tipo</option>
                            <option value="morador" <?= (isset($_POST['tipo_usuario']) && $_POST['tipo_usuario'] == 'morador') ? 'selected' : '' ?>>Morador</option>
                            <option value="funcionario" <?= (isset($_POST['tipo_usuario']) && $_POST['tipo_usuario'] == 'funcionario') ? 'selected' : '' ?>>Funcionário</option>
                            <option value="admin" <?= (isset($_POST['tipo_usuario']) && $_POST['tipo_usuario'] == 'admin') ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                </div>

                <div class="input-field">
                    <label for="senha">Senha</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="senha" name="senha" placeholder="Digite sua senha (mín. 6 caracteres)" required minlength="6">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('senha')" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--light-text);"></i>
                    </div>
                    <small style="color: var(--light-text); font-size: 0.8rem;">
                        <i class="fas fa-info-circle"></i> A senha deve ter pelo menos 6 caracteres
                    </small>
                </div>

                <div class="input-field">
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required minlength="6">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirmar_senha')" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--light-text);"></i>
                    </div>
                    <div id="senha-match" style="font-size: 0.8rem; margin-top: 0.25rem;"></div>
                </div>

                <button type="submit" class="submit-button">
                    <i class="fas fa-user-plus"></i>
                    <span>Criar Conta</span>
                </button>
            </form>

            <div class="toggle-container">
                <a href="login.php" class="toggle-button">
                    Já tem uma conta? Faça login
                </a>
            </div>

            <div class="toggle-container" style="margin-top: 1rem;">
                <a href="../../landing.html" class="toggle-button">
                    <i class="fas fa-arrow-left"></i> Voltar ao site
                </a>
            </div>
        </div>
    </div>

    <script src="../../js/validation.js"></script>
    <script>
        // Configurar validação de email
        document.addEventListener('DOMContentLoaded', () => {
            EmailValidator.setupEmailValidation('email', 'email-error');
        });

        // Função para mostrar/ocultar senha
        function togglePassword(fieldId) {
            const senhaInput = document.getElementById(fieldId);
            const toggleIcon = senhaInput.parentElement.querySelector('.toggle-password');
            
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                senhaInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Verificar se senhas coincidem
        function verificarSenhas() {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            const senhaMatch = document.getElementById('senha-match');
            
            if (confirmarSenha.length > 0) {
                if (senha === confirmarSenha) {
                    senhaMatch.innerHTML = '<i class="fas fa-check" style="color: var(--success-color);"></i> Senhas coincidem';
                    senhaMatch.style.color = 'var(--success-color)';
                } else {
                    senhaMatch.innerHTML = '<i class="fas fa-times" style="color: var(--error-color);"></i> Senhas não coincidem';
                    senhaMatch.style.color = 'var(--error-color)';
                }
            } else {
                senhaMatch.innerHTML = '';
            }
        }

        document.getElementById('senha').addEventListener('input', verificarSenhas);
        document.getElementById('confirmar_senha').addEventListener('input', verificarSenhas);

        // Validação do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const nome = document.getElementById('nome_completo').value.trim();
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            const tipo = document.getElementById('tipo_usuario').value;
            
            if (!nome || !email || !senha || !confirmarSenha || !tipo) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos.');
                return false;
            }
            
            if (senha !== confirmarSenha) {
                e.preventDefault();
                alert('As senhas não coincidem.');
                return false;
            }
            
            if (senha.length < 6) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 6 caracteres.');
                return false;
            }
            
            // Validar formato do email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Por favor, digite um email válido.');
                return false;
            }
        });
    </script>
</body>
</html>