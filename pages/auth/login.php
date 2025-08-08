<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
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
    
    // Processar login
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
        $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
        $senha = $_POST["senha"];
        
        if (empty($email) || empty($senha)) {
            $erro = "Por favor, preencha todos os campos.";
        } else {
            // Buscar usuário
            $sql = "SELECT * FROM tb_usuarios WHERE email = '$email' AND status = 'ativo'";
            $result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) == 1) {
                $usuario = mysqli_fetch_array($result);
                
                // Verificar se não está bloqueado por tentativas
                if ($usuario['tentativas_login'] >= 5) {
                    $erro = "Conta bloqueada por muitas tentativas. Entre em contato com o administrador.";
                } else {
                    // Verificar senha
                    if (password_verify($senha, $usuario['senha'])) {
                        // Login bem-sucedido
                        $_SESSION['usuario_id'] = $usuario['id_usuario'];
                        $_SESSION['usuario_nome'] = $usuario['nome_completo'];
                        $_SESSION['usuario_email'] = $usuario['email'];
                        $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];
                        
                        // Resetar tentativas e atualizar último login
                        $update_sql = "UPDATE tb_usuarios SET 
                                      tentativas_login = 0, 
                                      ultimo_login = CURRENT_TIMESTAMP 
                                      WHERE id_usuario = " . $usuario['id_usuario'];
                        mysqli_query($conn, $update_sql);
                        
                        // Redirecionar para dashboard
                        header("Location: ../../index.php");
                        exit();
                    } else {
                        // Senha incorreta - incrementar tentativas
                        $tentativas = $usuario['tentativas_login'] + 1;
                        $update_sql = "UPDATE tb_usuarios SET tentativas_login = $tentativas WHERE id_usuario = " . $usuario['id_usuario'];
                        mysqli_query($conn, $update_sql);
                        
                        $erro = "Email ou senha incorretos. Tentativas restantes: " . (5 - $tentativas);
                    }
                }
            } else {
                $erro = "Email ou senha incorretos.";
            }
        }
    }
    ?>

    <div class="container">
        <div class="form-container">
            <div class="header">
                <i class="fas fa-shield-alt" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h1>ShieldTech</h1>
                <p>Sistema de Controle de Acesso</p>
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
                <input type="hidden" name="action" value="login">
                
                <div class="input-field">
                    <label for="email">Email</label>
                    <div class="input-container">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Digite seu email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                </div>

                <div class="input-field">
                    <label for="senha">Senha</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                    </div>
                </div>

                <button type="submit" class="submit-button">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Entrar</span>
                </button>
            </form>

            <div class="toggle-container">
                <a href="cadastro.php" class="toggle-button">
                    Não tem uma conta? Cadastre-se
                </a>
            </div>

            <div class="toggle-container" style="margin-top: 1rem;">
                <a href="../../landing.html" class="toggle-button">
                    <i class="fas fa-arrow-left"></i> Voltar ao site
                </a>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar senha
        function togglePassword() {
            const senhaInput = document.getElementById('senha');
            const toggleIcon = document.querySelector('.toggle-password');
            
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

        // Validação do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value;
            
            if (!email || !senha) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos.');
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