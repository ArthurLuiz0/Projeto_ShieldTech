<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Moradores - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/validation.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
    include("../../conectarbd.php");
    
    // Processar formulário se foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
        $cpf = mysqli_real_escape_string($conn, $_POST["cpf"]);
        $rg = mysqli_real_escape_string($conn, $_POST["rg"]);
        $data_nascimento = mysqli_real_escape_string($conn, $_POST["data_nascimento"]);
        $sexo = mysqli_real_escape_string($conn, $_POST["sexo"]);
        $telefone = mysqli_real_escape_string($conn, $_POST["telefone"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $bloco = mysqli_real_escape_string($conn, $_POST["bloco"]);
        $torre = mysqli_real_escape_string($conn, $_POST["torre"]);
        $andar = mysqli_real_escape_string($conn, $_POST["andar"]);
        $veiculo = mysqli_real_escape_string($conn, $_POST["veiculo"]);
        $animais = mysqli_real_escape_string($conn, $_POST["animais"]);
        $foto = mysqli_real_escape_string($conn, $_POST["foto"]);
        $data_cadastro = date('Y-m-d H:i:s');
        
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Email inválido! Por favor, digite um email válido.');</script>";
        } else {
            // Verificar se email já existe
            $verificar_email = mysqli_query($conn, "SELECT * FROM tb_moradores WHERE email = '$email'");
            if (mysqli_num_rows($verificar_email) > 0) {
                echo "<script>alert('Este email já está cadastrado!');</script>";
            } else {
                $sql = "INSERT INTO tb_moradores (nome, cpf, rg, data_nascimento, sexo, telefone, email, bloco, torre, andar, veiculo, animais, foto, data_cadastro) 
                        VALUES ('$nome', '$cpf', '$rg', '$data_nascimento', '$sexo', '$telefone', '$email', '$bloco', '$torre', '$andar', '$veiculo', '$animais', '$foto', '$data_cadastro')";
                
                if (mysqli_query($conn, $sql)) {
                    echo "<script>alert('Morador cadastrado com sucesso!'); window.location = 'consultar_moradores.php';</script>";
                } else {
                    echo "<script>alert('Erro ao cadastrar morador: " . mysqli_error($conn) . "');</script>";
                }
            }
        }
    }
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
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-gear"></i> Cadastros</a>
                    <div class="dropdown-content">
                        <a href="cadastro_moradores.php">Moradores</a>
                        <a href="../funcionarios/cadastro_funcionarios.php">Funcionários</a>
                        <a href="../cargos/cadastro_cargos.php">Cargos</a>
                        <a href="../animais/cadastro_animais.php">Animais</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Gestão de Moradores</h2>

        <div class="form-grid">
            <section class="form-section">
                <h3>Cadastro de Morador</h3>
                <form method="post" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome Completo:</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>

                        <div class="form-group">
                            <label for="cpf">CPF:</label>
                            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="rg">RG:</label>
                            <input type="text" id="rg" name="rg" required>
                        </div>

                        <div class="form-group">
                            <label for="data_nascimento">Data de Nascimento:</label>
                            <input type="date" id="data_nascimento" name="data_nascimento" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sexo">Sexo:</label>
                            <select id="sexo" name="sexo" required>
                                <option value="">Selecione</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone:</label>
                            <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" placeholder="exemplo@email.com" required>
                        </div>

                        <div class="form-group">
                            <label for="bloco">Bloco:</label>
                            <input type="text" id="bloco" name="bloco" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="torre">Torre:</label>
                            <input type="text" id="torre" name="torre">
                        </div>

                        <div class="form-group">
                            <label for="andar">Andar:</label>
                            <input type="text" id="andar" name="andar">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="veiculo">Veículo:</label>
                            <input type="text" id="veiculo" name="veiculo" placeholder="Marca/Modelo - Placa">
                        </div>

                        <div class="form-group">
                            <label for="animais">Animais:</label>
                            <input type="text" id="animais" name="animais" placeholder="Tipo e nome dos animais">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="foto">Foto (URL):</label>
                        <input type="text" id="foto" name="foto" placeholder="URL da foto">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Cadastrar Morador
                        </button>
                        <a href="consultar_moradores.php" class="btn-secondary">
                            <i class="fas fa-list"></i> Ver Moradores
                        </a>
                    </div>
                </form>
            </section>

            <section class="info-section">
                <h3>Acesso Rápido</h3>
                <div class="quick-actions">
                    <a href="../animais/cadastro_animais.php" class="quick-action-card">
                        <i class="fas fa-paw"></i>
                        <h4>Cadastrar Animal</h4>
                        <p>Registre animais de estimação dos moradores</p>
                    </a>
                    
                    <a href="../funcionarios/cadastro_funcionarios.php" class="quick-action-card">
                        <i class="fas fa-user-tie"></i>
                        <h4>Cadastrar Funcionário</h4>
                        <p>Adicione novos funcionários ao sistema</p>
                    </a>
                    
                    <a href="../visitantes/visitantes.php" class="quick-action-card">
                        <i class="fas fa-user-friends"></i>
                        <h4>Registrar Visitante</h4>
                        <p>Controle de acesso de visitantes</p>
                    </a>
                </div>

                <div class="stats-section">
                    <h4>Estatísticas</h4>
                    <?php
                    $total_moradores = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_moradores"));
                    $total_blocos = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT bloco FROM tb_moradores WHERE bloco IS NOT NULL AND bloco != ''"));
                    $com_veiculos = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_moradores WHERE veiculo IS NOT NULL AND veiculo != ''"));
                    ?>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?= $total_moradores ?></span>
                        <span class="stat-label">Total de Moradores</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?= $total_blocos ?></span>
                        <span class="stat-label">Blocos Ocupados</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?= $com_veiculos ?></span>
                        <span class="stat-label">Com Veículos</span>
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
        // Máscara para CPF
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
                e.target.value = value;
            }
        });

        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                e.target.value = value;
            }
        });
    </script>
</body>
</html>