<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Morador - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/validation.css">
    <link rel="stylesheet" href="../../css/cpf-validation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
    include("../../conectarbd.php");
    
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    // Processar formulário se foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = mysqli_real_escape_string($conn, $_POST["id"]);
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
        
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Email inválido! Por favor, digite um email válido.');</script>";
        } else {
            // Verificar se email já existe em outro morador
            $verificar_email = mysqli_query($conn, "SELECT * FROM tb_moradores WHERE email = '$email' AND id_moradores != $id");
            if (mysqli_num_rows($verificar_email) > 0) {
                echo "<script>alert('Este email já está cadastrado em outro morador!');</script>";
            } else {
                $sql = "UPDATE tb_moradores SET 
                        nome='$nome', cpf='$cpf', rg='$rg', data_nascimento='$data_nascimento', 
                        sexo='$sexo', telefone='$telefone', email='$email', bloco='$bloco', torre='$torre', 
                        andar='$andar', veiculo='$veiculo', animais='$animais', foto='$foto' 
                        WHERE id_moradores=$id";
                
                if (mysqli_query($conn, $sql)) {
                    // Gerenciar dados do animal
                    if (isset($_POST['tem_animal']) && $_POST['tem_animal'] == '1' && !empty($_POST['nome_animal'])) {
                        $nome_animal = mysqli_real_escape_string($conn, $_POST["nome_animal"]);
                        $tipo_animal = mysqli_real_escape_string($conn, $_POST["tipo_animal"]);
                        $porte_animal = mysqli_real_escape_string($conn, $_POST["porte_animal"]);
                        $observacoes_animal = mysqli_real_escape_string($conn, $_POST["observacoes_animal"]);
                        
                        // Verificar se já existe animal para este morador
                        $verificar_animal = mysqli_query($conn, "SELECT * FROM tb_animais WHERE id_morador = $id");
                        
                        if (mysqli_num_rows($verificar_animal) > 0) {
                            // Atualizar animal existente
                            $sql_animal = "UPDATE tb_animais SET 
                                          nome='$nome_animal', tipo='$tipo_animal', porte='$porte_animal', observacoes='$observacoes_animal' 
                                          WHERE id_morador=$id";
                        } else {
                            // Inserir novo animal
                            $sql_animal = "INSERT INTO tb_animais (nome, tipo, porte, observacoes, id_morador) 
                                          VALUES ('$nome_animal', '$tipo_animal', '$porte_animal', '$observacoes_animal', '$id')";
                        }
                        
                        mysqli_query($conn, $sql_animal);
                    } else {
                        // Se não tem animal marcado, remover animal existente
                        mysqli_query($conn, "DELETE FROM tb_animais WHERE id_morador = $id");
                        // Atualizar status do morador para "Não possui"
                        mysqli_query($conn, "UPDATE tb_moradores SET animais = 'Não possui' WHERE id_moradores = $id");
                    }
                    
                    echo "<script>alert('Morador atualizado com sucesso!'); window.location = 'consultar_moradores.php';</script>";
                } else {
                    echo "<script>alert('Erro ao atualizar morador: " . mysqli_error($conn) . "');</script>";
                }
            }
        }
    }
    
    // Buscar dados do morador
    $selecionar = mysqli_query($conn, "SELECT * FROM tb_moradores WHERE id_moradores=$id");
    $campo = mysqli_fetch_array($selecionar);
    
    // Buscar dados do animal se existir
    $animal_query = mysqli_query($conn, "SELECT * FROM tb_animais WHERE id_morador = $id");
    $animal = mysqli_fetch_array($animal_query);
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
            </ul>
        </nav>
    </header>

    <main>
        <h2>Editar Morador</h2>

        <section class="form-section">
            <h3>Alterar Dados do Morador</h3>
            <form method="post" action="">
                <input type="hidden" name="id" value="<?= $campo["id_moradores"] ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" value="<?= $campo["nome"] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <div class="cpf-validation">
                            <input type="text" id="cpf" name="cpf" value="<?= $campo["cpf"] ?>" required>
                            <span class="validation-icon" id="cpf-icon"></span>
                        </div>
                        <div class="cpf-error" id="cpf-error"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="rg">RG:</label>
                        <input type="text" id="rg" name="rg" value="<?= $campo["rg"] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento:</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="<?= $campo["data_nascimento"] ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sexo">Sexo:</label>
                        <select id="sexo" name="sexo" required>
                            <option value="">Selecione</option>
                            <option value="Masculino" <?= $campo["sexo"] == "Masculino" ? "selected" : "" ?>>Masculino</option>
                            <option value="Feminino" <?= $campo["sexo"] == "Feminino" ? "selected" : "" ?>>Feminino</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="tel" id="telefone" name="telefone" value="<?= $campo["telefone"] ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <div class="email-validation">
                            <input type="email" id="email" name="email" value="<?= $campo["email"] ?>" required>
                            <span class="validation-icon" id="email-icon"></span>
                        </div>
                        <div class="email-error" id="email-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="bloco">Bloco:</label>
                        <input type="text" id="bloco" name="bloco" value="<?= $campo["bloco"] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="torre">Torre:</label>
                        <input type="text" id="torre" name="torre" value="<?= $campo["torre"] ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="andar">Andar:</label>
                        <input type="text" id="andar" name="andar" value="<?= $campo["andar"] ?>">
                    </div>

                    <div class="form-group">
                        <label for="veiculo">Veículo:</label>
                        <input type="hidden" id="veiculo" name="veiculo" value="<?= $campo["veiculo"] ?>">
                        <div style="background: #f8f9fa; padding: 0.8rem; border-radius: 0.5rem; border: 1px solid #dee2e6;">
                            <span style="color: #495057;">
                                <i class="fas fa-car"></i> 
                                Status atual: <strong><?= $campo["veiculo"] == "Possui" ? "Possui veículo" : "Não possui veículo" ?></strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="animais">Animais:</label>
                        <input type="hidden" id="animais" name="animais" value="<?= $campo["animais"] ?>">
                        <div style="background: #f8f9fa; padding: 0.8rem; border-radius: 0.5rem; border: 1px solid #dee2e6;">
                            <span style="color: #495057;">
                                <i class="fas fa-paw"></i> 
                                Status atual: <strong><?= ($campo["animais"] == "Possui" || $campo["animais"] == "sim") ? "Possui animal" : "Não possui animal" ?></strong>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tem_animal">
                            <input type="checkbox" id="tem_animal" name="tem_animal" value="1" onchange="toggleAnimalForm()" <?= $animal ? 'checked' : '' ?>> 
                            Alterar status do animal
                        </label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tem_veiculo_edit">
                            <input type="checkbox" id="tem_veiculo_edit" name="tem_veiculo_edit" value="1" onchange="toggleVeiculoStatus()" <?= $campo["veiculo"] == "Possui" ? 'checked' : '' ?>> 
                            Alterar status do veículo
                        </label>
                    </div>
                </div>

                <div style="background: #e8f4fd; padding: 1rem; border-radius: 0.5rem; margin: 1rem 0; border-left: 4px solid #3498db;">
                    <p style="margin: 0; color: #3498db;">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Gerenciar Veículos:</strong> Para cadastrar, editar ou remover veículos deste morador, 
                        acesse o menu <strong>Cadastros > Veículos</strong>.
                    </p>
                </div>

                <!-- Formulário de Animal -->
                <div id="animal-form-section" style="display: <?= $animal ? 'block' : 'none' ?>;">
                    <h4 style="color: var(--primary-color); margin: 1.5rem 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--accent-color);">
                        <i class="fas fa-paw"></i> Dados do Animal
                    </h4>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_animal">Nome do Animal:</label>
                            <input type="text" id="nome_animal" name="nome_animal" value="<?= $animal ? $animal['nome'] : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="tipo_animal">Tipo:</label>
                            <select id="tipo_animal" name="tipo_animal">
                                <option value="">Selecione o tipo</option>
                                <option value="Cão" <?= ($animal && $animal['tipo'] == 'Cão') ? 'selected' : '' ?>>Cão</option>
                                <option value="Gato" <?= ($animal && $animal['tipo'] == 'Gato') ? 'selected' : '' ?>>Gato</option>
                                <option value="Pássaro" <?= ($animal && $animal['tipo'] == 'Pássaro') ? 'selected' : '' ?>>Pássaro</option>
                                <option value="Peixe" <?= ($animal && $animal['tipo'] == 'Peixe') ? 'selected' : '' ?>>Peixe</option>
                                <option value="Outro" <?= ($animal && $animal['tipo'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="porte_animal">Porte:</label>
                            <select id="porte_animal" name="porte_animal">
                                <option value="">Selecione o porte</option>
                                <option value="Pequeno" <?= ($animal && $animal['porte'] == 'Pequeno') ? 'selected' : '' ?>>Pequeno</option>
                                <option value="Médio" <?= ($animal && $animal['porte'] == 'Médio') ? 'selected' : '' ?>>Médio</option>
                                <option value="Grande" <?= ($animal && $animal['porte'] == 'Grande') ? 'selected' : '' ?>>Grande</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="observacoes_animal">Observações sobre o Animal:</label>
                        <textarea id="observacoes_animal" name="observacoes_animal" rows="3" placeholder="Informações adicionais sobre o animal (vacinas, comportamento, etc.)"><?= $animal ? $animal['observacoes'] : '' ?></textarea>
                    </div>
                </div>

                
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="consultar_moradores.php" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 ShieldTech. Todos os direitos reservados.</p>
    </footer>

    <script src="../../js/validation.js"></script>
    <script src="../../js/cpf-validator.js"></script>
    <script src="../../js/photo-preview.js"></script>
    <script>
        // Configurar validação de email para edição
        document.addEventListener('DOMContentLoaded', () => {
            const moradorId = <?= $campo["id_moradores"] ?>;
            
            EmailValidator.setupEmailValidation('email', 'email-error');
            CPFValidator.setupCompleteValidation('cpf', 'cpf-error', 'moradores', moradorId);
            
            // Verificação adicional para email duplicado
            const emailInput = document.getElementById('email');
            const emailIcon = document.getElementById('email-icon');
            
            emailInput.addEventListener('input', async () => {
                const email = emailInput.value.trim();
                if (email) {
                    const exists = await checkEmailExists(email, moradorId);
                    if (exists) {
                        emailInput.classList.add('invalid');
                        emailInput.classList.remove('valid');
                        document.getElementById('email-error').textContent = 'Este email já está cadastrado!';
                        document.getElementById('email-error').style.color = '#e74c3c';
                        document.getElementById('email-error').style.display = 'block';
                        emailIcon.innerHTML = '<i class="fas fa-times-circle invalid"></i>';
                    }
                }
            });
        });
        
        // Função para mostrar/ocultar formulário de animal
        function toggleAnimalForm() {
            const checkbox = document.getElementById('tem_animal');
            const animalForm = document.getElementById('animal-form-section');
            const animaisInput = document.getElementById('animais');
            
            if (checkbox.checked) {
                animalForm.style.display = 'block';
                animalForm.style.animation = 'fadeIn 0.3s ease-in';
                animaisInput.value = 'Possui';
                
                // Tornar campos obrigatórios quando visíveis
                document.getElementById('nome_animal').required = true;
                document.getElementById('tipo_animal').required = true;
                document.getElementById('porte_animal').required = true;
            } else {
                animalForm.style.display = 'none';
                animaisInput.value = 'Não possui';
                
                // Remover obrigatoriedade e limpar campos
                document.getElementById('nome_animal').required = false;
                document.getElementById('tipo_animal').required = false;
                document.getElementById('porte_animal').required = false;
                
                // Limpar todos os campos do animal
                document.getElementById('nome_animal').value = '';
                document.getElementById('tipo_animal').value = '';
                document.getElementById('porte_animal').value = '';
                document.getElementById('observacoes_animal').value = '';
            }
        }
        
        // Função para alterar status do veículo
        function toggleVeiculoStatus() {
            const checkbox = document.getElementById('tem_veiculo_edit');
            const veiculoInput = document.getElementById('veiculo');
            
            if (checkbox.checked) {
                veiculoInput.value = 'Possui';
            } else {
                veiculoInput.value = 'Não possui';
            }
        }
    </script>
</body>
</html>