<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Moradores - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/validation.css">
    <link rel="stylesheet" href="../../css/cpf-validation.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .animal-form-section {
            background: #f8f9fa;
            border: 2px solid #e3f2fd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animal-form-title {
            color: var(--primary-color);
            margin: 0 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            background: #e8f5e8;
            border-radius: 6px;
            border-left: 4px solid #4caf50;
        }

        .checkbox-container input[type="checkbox"] {
            transform: scale(1.2);
            accent-color: #4caf50;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid #28a745;
            margin: 1rem 0;
            display: none;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid #dc3545;
            margin: 1rem 0;
            display: none;
        }
    </style>
</head>
<body>
    <?php
    include("../../conectarbd.php");
    
    $success_message = "";
    $error_message = "";
    
    // Processar formulário se foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Dados do morador
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
        $animais = isset($_POST["animais"]) ? "Possui" : "Não possui";
        $foto = mysqli_real_escape_string($conn, $_POST["foto"]);
        $data_cadastro = date('Y-m-d H:i:s');
        
        // Verificar se CPF já existe
        $verifica_cpf = mysqli_query($conn, "SELECT id_moradores FROM tb_moradores WHERE cpf = '$cpf'");
        if (mysqli_num_rows($verifica_cpf) > 0) {
            $error_message = "CPF já cadastrado no sistema!";
        } else {
            // Iniciar transação
            mysqli_autocommit($conn, FALSE);
            
            try {
                // Inserir morador
                $sql_morador = "INSERT INTO tb_moradores (nome, cpf, rg, data_nascimento, sexo, telefone, email, bloco, torre, andar, veiculo, animais, foto, data_cadastro) 
                VALUES ('$nome', '$cpf', '$rg', '$data_nascimento', '$sexo', '$telefone','$email', '$bloco', '$torre', '$andar', '$veiculo', '$animais', '$foto', '$data_cadastro')";

                if (mysqli_query($conn, $sql_morador)) {
                    $id_morador = mysqli_insert_id($conn);
                    
                    // Se possui animal, inserir dados do animal
                    if (isset($_POST["animais"]) && !empty($_POST["nome_animal"])) {
                        $nome_animal = mysqli_real_escape_string($conn, $_POST["nome_animal"]);
                        $tipo_animal = mysqli_real_escape_string($conn, $_POST["tipo_animal"]);
                        $porte_animal = mysqli_real_escape_string($conn, $_POST["porte_animal"]);
                        $idade_animal = !empty($_POST["idade_animal"]) ? (int)$_POST["idade_animal"] : NULL;
                        $cor_animal = mysqli_real_escape_string($conn, $_POST["cor_animal"]);
                        $peso_animal = !empty($_POST["peso_animal"]) ? (float)$_POST["peso_animal"] : NULL;
                        $observacoes_animal = mysqli_real_escape_string($conn, $_POST["observacoes_animal"]);
                        
                        $sql_animal = "INSERT INTO tb_animais (nome, tipo, porte, observacoes, id_morador, idade, cor, peso, data_cadastro) 
                        VALUES ('$nome_animal', '$tipo_animal', '$porte_animal', '$observacoes_animal', $id_morador, " . 
                        ($idade_animal !== NULL ? $idade_animal : "NULL") . ", '$cor_animal', " . 
                        ($peso_animal !== NULL ? $peso_animal : "NULL") . ", '$data_cadastro')";
                        
                        if (!mysqli_query($conn, $sql_animal)) {
                            throw new Exception("Erro ao cadastrar animal: " . mysqli_error($conn));
                        }
                    }
                    
                    // Confirmar transação
                    mysqli_commit($conn);
                    $success_message = "Morador " . ($animais == "Possui" ? "e animal " : "") . "cadastrado com sucesso!";
                    
                    // Limpar formulário após sucesso
                    echo "<script>
                        setTimeout(function() {
                            if(confirm('Cadastro realizado com sucesso! Deseja cadastrar outro morador?')) {
                                window.location.reload();
                            } else {
                                window.location = 'consultar_moradores.php';
                            }
                        }, 1500);
                    </script>";
                    
                } else {
                    throw new Exception("Erro ao cadastrar morador: " . mysqli_error($conn));
                }
                
            } catch (Exception $e) {
                // Desfazer transação em caso de erro
                mysqli_rollback($conn);
                $error_message = $e->getMessage();
            }
            
            // Restaurar autocommit
            mysqli_autocommit($conn, TRUE);
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
                <li><a href="../reservas/reservas.php"><i class="fas fa-calendar"></i> Reservas</a></li>
                <li><a href="../encomendas/cadastro_encomendas.php"><i class="fas fa-box"></i> Encomendas</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-gear"></i> Cadastros</a>
                    <div class="dropdown-content">
                        <a href="../moradores/cadastro_moradores.php">Moradores</a>
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

        <?php if ($success_message): ?>
            <div class="success-message" style="display: block;">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="error-message" style="display: block;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="form-grid">
            <section class="form-section">
                <h3>Cadastro de Morador</h3>
                <form method="post" action="" id="form-morador">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome Completo:</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>

                        <div class="form-group">
                            <label for="cpf">CPF:</label>
                            <div class="cpf-validation">
                                <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                                <span class="validation-icon" id="cpf-icon"></span>
                            </div>
                            <div class="cpf-error" id="cpf-error"></div>
                            <div class="cpf-tooltip">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltiptext">
                                    Digite um CPF válido. O sistema verificará:<br>
                                    • Formato correto (11 dígitos)<br>
                                    • Dígitos verificadores válidos<br>
                                    • Se já não está cadastrado<br>
                                    Exemplo: 123.456.789-00
                                </span>
                            </div>
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

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <div class="email-validation">
                            <input type="email" id="email" name="email" placeholder="exemplo@email.com" required>
                            <span class="validation-icon" id="email-icon"></span>
                        </div>
                        <div class="email-error" id="email-error"></div>
                        <div class="email-tooltip">
                            <i class="fas fa-info-circle"></i>
                            <span class="tooltiptext">
                                Digite um email válido. Exemplos:<br>
                                • usuario@gmail.com<br>
                                • nome@empresa.com.br<br>
                                • contato@dominio.org
                            </span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="bloco">Bloco:</label>
                            <input type="text" id="bloco" name="bloco" required>
                        </div>

                        <div class="form-group">
                            <label for="torre">Torre:</label>
                            <input type="text" id="torre" name="torre">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="andar">Andar:</label>
                            <input type="text" id="andar" name="andar">
                        </div>

                        <div class="form-group">
                            <label for="veiculo">Veículo:</label>
                            <input type="text" id="veiculo" name="veiculo" placeholder="Marca/Modelo - Placa">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="foto">Foto (URL):</label>
                        <input type="text" id="foto" name="foto" placeholder="URL da foto">
                    </div>

                    <!-- Seção de Animal de Estimação -->
                    <div class="form-row">
                        <div class="form-group full-width">
                            <div class="checkbox-container">
                                <input type="checkbox" id="animais" name="animais" value="Possui" onchange="toggleAnimalForm()">
                                <label for="animais">
                                    <i class="fas fa-paw"></i> Possui animal de estimação?
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="animal-form-section" class="animal-form-section" style="display: none;">
                        <h4 class="animal-form-title">
                            <i class="fas fa-paw"></i> Dados do Animal de Estimação
                        </h4>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nome_animal">Nome do Animal: <span style="color: red;">*</span></label>
                                <input type="text" id="nome_animal" name="nome_animal">
                            </div>

                            <div class="form-group">
                                <label for="tipo_animal">Tipo: <span style="color: red;">*</span></label>
                                <select id="tipo_animal" name="tipo_animal">
                                    <option value="">Selecione o tipo</option>
                                    <option value="Cão">Cão</option>
                                    <option value="Gato">Gato</option>
                                    <option value="Pássaro">Pássaro</option>
                                    <option value="Peixe">Peixe</option>
                                    <option value="Hamster">Hamster</option>
                                    <option value="Coelho">Coelho</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="porte_animal">Porte: <span style="color: red;">*</span></label>
                                <select id="porte_animal" name="porte_animal">
                                    <option value="">Selecione o porte</option>
                                    <option value="Pequeno">Pequeno (até 10kg)</option>
                                    <option value="Médio">Médio (10kg a 25kg)</option>
                                    <option value="Grande">Grande (acima de 25kg)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="idade_animal">Idade (anos):</label>
                                <input type="number" id="idade_animal" name="idade_animal" min="0" max="30" step="1">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="cor_animal">Cor/Pelagem:</label>
                                <input type="text" id="cor_animal" name="cor_animal" placeholder="Ex: Preto, Branco, Caramelo...">
                            </div>

                            <div class="form-group">
                                <label for="peso_animal">Peso (kg):</label>
                                <input type="number" id="peso_animal" name="peso_animal" step="0.1" min="0" max="100">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="observacoes_animal">Observações sobre o Animal:</label>
                            <textarea id="observacoes_animal" name="observacoes_animal" rows="3" placeholder="Informações adicionais: vacinas, comportamento, cuidados especiais, etc."></textarea>
                        </div>

                        <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 0.75rem; margin-top: 1rem;">
                            <small><i class="fas fa-info-circle" style="color: #856404;"></i> 
                            <strong>Importante:</strong> Os campos marcados com <span style="color: red;">*</span> são obrigatórios quando o animal estiver sendo cadastrado.</small>
                        </div>
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
                    $com_animais = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_moradores WHERE animais = 'Possui'"));
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

                    <div class="stat-item">
                        <span class="stat-number"><?= $com_animais ?></span>
                        <span class="stat-label">Com Animais</span>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 ShieldTech. Todos os direitos reservados.</p>
    </footer>

    <script src="../../js/validation.js"></script>
    <script src="../../js/cpf-validator.js"></script>
    <script>
        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                e.target.value = value;
            }
        });

        // Configurar validação de email em tempo real
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof EmailValidator !== 'undefined') {
                EmailValidator.setupEmailValidation('email', 'email-error');
            }
            if (typeof CPFValidator !== 'undefined') {
                CPFValidator.setupCompleteValidation('cpf', 'cpf-error', 'moradores');
            }
        });

        // Função para mostrar/ocultar formulário de animal
        function toggleAnimalForm() {
            const checkbox = document.getElementById('animais');
            const animalForm = document.getElementById('animal-form-section');
            
            if (checkbox.checked) {
                animalForm.style.display = 'block';
                
                // Tornar campos obrigatórios quando visíveis
                document.getElementById('nome_animal').required = true;
                document.getElementById('tipo_animal').required = true;
                document.getElementById('porte_animal').required = true;
                
                // Scroll suave até o formulário do animal
                setTimeout(() => {
                    animalForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
                
            } else {
                animalForm.style.display = 'none';
                
                // Remover obrigatoriedade e limpar campos
                const camposAnimal = ['nome_animal', 'tipo_animal', 'porte_animal', 'idade_animal', 'cor_animal', 'peso_animal', 'observacoes_animal'];
                camposAnimal.forEach(campo => {
                    const elemento = document.getElementById(campo);
                    elemento.required = false;
                    elemento.value = '';
                });
            }
        }

        // Validação antes do envio do formulário
        document.getElementById('form-morador').addEventListener('submit', function(e) {
            const temAnimal = document.getElementById('animais').checked;
            
            if (temAnimal) {
                const nomeAnimal = document.getElementById('nome_animal').value.trim();
                const tipoAnimal = document.getElementById('tipo_animal').value;
                const porteAnimal = document.getElementById('porte_animal').value;
                
                if (!nomeAnimal || !tipoAnimal || !porteAnimal) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios do animal (Nome, Tipo e Porte).');
                    return false;
                }
            }
            
            return true;
        });

        // Validação em tempo real dos campos do animal
        document.getElementById('nome_animal')?.addEventListener('blur', function() {
            if (document.getElementById('animais').checked && !this.value.trim()) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '';
            }
        });

        document.getElementById('tipo_animal')?.addEventListener('change', function() {
            if (document.getElementById('animais').checked && !this.value) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '';
            }
        });

        document.getElementById('porte_animal')?.addEventListener('change', function() {
            if (document.getElementById('animais').checked && !this.value) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '';
            }
        });
    </script>
</body>
</html>