<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/validation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
    include("verificar_sessao.php");
    include("../../conectarbd.php");
    
    verificarLogin();
    $usuario = obterUsuarioLogado();
    
    $erro = '';
    $sucesso = '';
    
    // Processar atualização do perfil
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'atualizar_perfil') {
        $nome_completo = mysqli_real_escape_string($conn, trim($_POST["nome_completo"]));
        $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
        $senha_atual = $_POST["senha_atual"];
        $nova_senha = $_POST["nova_senha"];
        $confirmar_nova_senha = $_POST["confirmar_nova_senha"];
        
        // Validações básicas
        if (empty($nome_completo) || empty($email)) {
            $erro = "Nome e email são obrigatórios.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = "Email inválido.";
        } else {
            // Verificar se email já existe em outro usuário
            $verificar_email = mysqli_query($conn, "SELECT id_usuario FROM tb_usuarios WHERE email = '$email' AND id_usuario != " . $usuario['id']);
            
            if (mysqli_num_rows($verificar_email) > 0) {
                $erro = "Este email já está sendo usado por outro usuário.";
            } else {
                // Se está alterando senha
                if (!empty($nova_senha)) {
                    if (empty($senha_atual)) {
                        $erro = "Digite sua senha atual para alterar a senha.";
                    } elseif (strlen($nova_senha) < 6) {
                        $erro = "A nova senha deve ter pelo menos 6 caracteres.";
                    } elseif ($nova_senha !== $confirmar_nova_senha) {
                        $erro = "As novas senhas não coincidem.";
                    } else {
                        // Verificar senha atual
                        $verificar_senha = mysqli_query($conn, "SELECT senha FROM tb_usuarios WHERE id_usuario = " . $usuario['id']);
                        $dados_usuario = mysqli_fetch_array($verificar_senha);
                        
                        if (!password_verify($senha_atual, $dados_usuario['senha'])) {
                            $erro = "Senha atual incorreta.";
                        } else {
                            // Atualizar com nova senha
                            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                            $sql = "UPDATE tb_usuarios SET 
                                   nome_completo='$nome_completo', 
                                   email='$email', 
                                   senha='$nova_senha_hash' 
                                   WHERE id_usuario=" . $usuario['id'];
                        }
                    }
                } else {
                    // Atualizar apenas nome e email
                    $sql = "UPDATE tb_usuarios SET 
                           nome_completo='$nome_completo', 
                           email='$email' 
                           WHERE id_usuario=" . $usuario['id'];
                }
                
                if (!$erro && isset($sql)) {
                    if (mysqli_query($conn, $sql)) {
                        // Atualizar sessão
                        $_SESSION['usuario_nome'] = $nome_completo;
                        $_SESSION['usuario_email'] = $email;
                        $sucesso = "Perfil atualizado com sucesso!";
                        $usuario['nome'] = $nome_completo;
                        $usuario['email'] = $email;
                    } else {
                        $erro = "Erro ao atualizar perfil.";
                    }
                }
            }
        }
    }
    
    // Buscar dados atuais do usuário
    $dados_usuario = mysqli_query($conn, "SELECT * FROM tb_usuarios WHERE id_usuario = " . $usuario['id']);
    $dados = mysqli_fetch_array($dados_usuario);
    ?>

    <header>
        <nav>
            <div class="logo">
                <h1><i class="fas fa-shield"></i> ShieldTech</h1>
            </div>
            <ul class="menu">
                <li><a href="../../index.php"><i class="fas fa-home"></i> Início</a></li>
                <li><a href="../visitantes/visitantes.php"><i class="fas fa-user-friends"></i> Visitantes</a></li>
                <li><a href="../relatorios/relatorios.php"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
                <li><a href="../reservas/reservas.php"><i class="fas fa-calendar"></i> Reservas</a></li>
                <li><a href="../encomendas/cadastro_encomendas.php"><i class="fas fa-box"></i> Encomendas</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-gear"></i> Cadastros</a>
                    <div class="dropdown-content">
                        <a href="../moradores/cadastro_moradores.php">Moradores</a>
                        <a href="../funcionarios/cadastro_funcionarios.php">Funcionários</a>
                        <a href="../cargos/cadastro_cargos.php">Cargos</a>
                        <a href="../animais/cadastro_animais.php">Animais</a>
                        <a href="../veiculos/cadastro_veiculos.php">Veículos</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-user"></i> <?= $usuario['nome'] ?></a>
                    <div class="dropdown-content">
                        <a href="perfil.php">Meu Perfil</a>
                        <a href="logout.php">Sair</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Meu Perfil</h2>

        <div class="form-grid">
            <section class="form-section">
                <h3>Dados da Conta</h3>
                
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
                    <input type="hidden" name="action" value="atualizar_perfil">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_completo">Nome Completo:</label>
                            <input type="text" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($dados['nome_completo']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <div class="email-validation">
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($dados['email']) ?>" required>
                                <span class="validation-icon" id="email-icon"></span>
                            </div>
                            <div class="email-error" id="email-error"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_usuario">Tipo de Usuário:</label>
                            <input type="text" value="<?= ucfirst($dados['tipo_usuario']) ?>" readonly style="background-color: #f8f9fa; color: #6c757d;">
                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <input type="text" value="<?= ucfirst($dados['status']) ?>" readonly style="background-color: #f8f9fa; color: #6c757d;">
                        </div>
                    </div>

                    <hr style="margin: 2rem 0; border: none; border-top: 1px solid #dee2e6;">

                    <h4 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <i class="fas fa-key"></i> Alterar Senha (Opcional)
                    </h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="senha_atual">Senha Atual:</label>
                            <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nova_senha">Nova Senha:</label>
                            <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite a nova senha (mín. 6 caracteres)" minlength="6">
                        </div>

                        <div class="form-group">
                            <label for="confirmar_nova_senha">Confirmar Nova Senha:</label>
                            <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" placeholder="Confirme a nova senha" minlength="6">
                        </div>
                    </div>

                    <div id="senha-match" style="font-size: 0.8rem; margin-top: 0.25rem;"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                        <a href="../../index.php" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                        </a>
                    </div>
                </form>
            </section>

            <section class="info-section">
                <h3>Informações da Conta</h3>
                
                <div class="info-cards">
                    <div class="info-card">
                        <i class="fas fa-calendar-plus"></i>
                        <h4>Membro desde</h4>
                        <p><?= date('d/m/Y', strtotime($dados['data_criacao'])) ?></p>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-clock"></i>
                        <h4>Último Login</h4>
                        <p><?= $dados['ultimo_login'] ? date('d/m/Y H:i', strtotime($dados['ultimo_login'])) : 'Primeiro acesso' ?></p>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-shield-alt"></i>
                        <h4>Nível de Acesso</h4>
                        <p><?= ucfirst($dados['tipo_usuario']) ?></p>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-check-circle"></i>
                        <h4>Status da Conta</h4>
                        <p><?= ucfirst($dados['status']) ?></p>
                    </div>
                </div>

                <div class="quick-actions">
                    <h4>Dicas de Segurança</h4>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 0.5rem; margin-top: 1rem;">
                        <ul style="margin: 0; padding-left: 1.5rem; color: #666;">
                            <li>Use uma senha forte com pelo menos 6 caracteres</li>
                            <li>Não compartilhe suas credenciais de acesso</li>
                            <li>Faça logout ao sair de computadores públicos</li>
                            <li>Mantenha seu email atualizado</li>
                            <li>Entre em contato se suspeitar de acesso não autorizado</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 ShieldTech. Todos os direitos reservados.</p>
    </footer>

    <script src="../../js/validation.js"></script>
    <script>
        // Configurar validação de email
        document.addEventListener('DOMContentLoaded', () => {
            EmailValidator.setupEmailValidation('email', 'email-error');
        });

        // Verificar se senhas coincidem
        function verificarSenhas() {
            const novaSenha = document.getElementById('nova_senha').value;
            const confirmarNovaSenha = document.getElementById('confirmar_nova_senha').value;
            const senhaMatch = document.getElementById('senha-match');
            
            if (confirmarNovaSenha.length > 0) {
                if (novaSenha === confirmarNovaSenha) {
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

        document.getElementById('nova_senha').addEventListener('input', verificarSenhas);
        document.getElementById('confirmar_nova_senha').addEventListener('input', verificarSenhas);

        // Validação do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const novaSenha = document.getElementById('nova_senha').value;
            const confirmarNovaSenha = document.getElementById('confirmar_nova_senha').value;
            const senhaAtual = document.getElementById('senha_atual').value;
            
            // Se está tentando alterar senha
            if (novaSenha || confirmarNovaSenha) {
                if (!senhaAtual) {
                    e.preventDefault();
                    alert('Digite sua senha atual para alterar a senha.');
                    return false;
                }
                
                if (novaSenha !== confirmarNovaSenha) {
                    e.preventDefault();
                    alert('As novas senhas não coincidem.');
                    return false;
                }
                
                if (novaSenha.length < 6) {
                    e.preventDefault();
                    alert('A nova senha deve ter pelo menos 6 caracteres.');
                    return false;
                }
            }
        });
    </script>
</body>
</html>