<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Visitante - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/cpf-validation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
    include("../../conectarbd.php");
    require_once("../../php/photo-upload.php");
    
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    // Processar formulário se foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = mysqli_real_escape_string($conn, $_POST["id"]);
        $nome_visitante = mysqli_real_escape_string($conn, $_POST["nome_visitante"]);
        $num_documento = mysqli_real_escape_string($conn, $_POST["num_documento"]);
        $telefone = mysqli_real_escape_string($conn, $_POST["telefone"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $data_nascimento = mysqli_real_escape_string($conn, $_POST["data_nascimento"]);
        $id_morador = mysqli_real_escape_string($conn, $_POST["id_morador"]);
        
        // Buscar dados atuais do visitante
        $current_data = mysqli_query($conn, "SELECT foto FROM tb_visitantes WHERE id_visitantes = $id");
        $current = mysqli_fetch_array($current_data);
        
        // Processar upload de foto
        $foto_url = $current["foto"]; // Manter foto atual por padrão
        if (isset($_FILES['foto_file']) && $_FILES['foto_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $photoUpload = new PhotoUpload('visitantes');
            
            // Remover foto antiga se existir
            if (!empty($current["foto"]) && strpos($current["foto"], '../imagens/') === 0) {
                $oldFilename = basename($current["foto"]);
                $photoUpload->deletePhoto($oldFilename);
            }
            
            $uploadResult = $photoUpload->uploadPhoto($_FILES['foto_file'], 'visitante');
            
            if ($uploadResult['success']) {
                $foto_url = $uploadResult['url'];
            } else {
                echo "<script>alert('Erro no upload da foto: " . $uploadResult['message'] . "');</script>";
            }
        } elseif (!empty($_POST["foto"]) && $_POST["foto"] !== $current["foto"]) {
            // Se URL foi alterada, usar nova URL
            $foto_url = mysqli_real_escape_string($conn, $_POST["foto"]);
        }
        
        $sql = "UPDATE tb_visitantes SET 
                nome_visitante='$nome_visitante', num_documento='$num_documento', telefone='$telefone', 
                email='$email', data_nascimento='$data_nascimento', foto='$foto_url', id_morador='$id_morador' 
                WHERE id_visitantes=$id";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Visitante atualizado com sucesso!'); window.location = 'consultar_visitantes.php';</script>";
        } else {
            echo "<script>alert('Erro ao atualizar visitante: " . mysqli_error($conn) . "');</script>";
        }
    }
    
    // Buscar dados do visitante
    $selecionar = mysqli_query($conn, "SELECT * FROM tb_visitantes WHERE id_visitantes=$id");
    $campo = mysqli_fetch_array($selecionar);
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
        <h2>Editar Visitante</h2>

        <section class="form-section">
            <h3>Alterar Dados do Visitante</h3>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $campo["id_visitantes"] ?>">
                <input type="hidden" id="id_morador" name="id_morador" value="<?= $campo["id_morador"] ?>">
                
                <div class="form-group">
                    <label for="search_morador">Morador Visitado:</label>
                    <div class="search-container">
                        <input type="text" id="search_morador" name="search_morador" 
                               placeholder="Digite o nome do morador..." 
                               autocomplete="off" required>
                        <div class="search-results" id="search_results"></div>
                    </div>
                    <div class="selected-morador" id="selected_morador" style="<?= $campo["id_morador"] ? 'display: block;' : 'display: none;' ?>">
                        <button type="button" class="clear-selection" onclick="clearSelection()">×</button>
                        <strong>Morador selecionado:</strong>
                        <div id="selected_info">
                            <?php
                            if ($campo["id_morador"]) {
                                $morador_atual = mysqli_query($conn, "SELECT nome, bloco, torre, telefone, email FROM tb_moradores WHERE id_moradores = " . $campo["id_morador"]);
                                $morador_data = mysqli_fetch_array($morador_atual);
                                if ($morador_data) {
                                    echo "<div><strong>" . $morador_data["nome"] . "</strong></div>";
                                    echo "<div>Bloco " . $morador_data["bloco"] . "/" . $morador_data["torre"] . "</div>";
                                    echo "<div>Tel: " . $morador_data["telefone"] . "</div>";
                                    echo "<div>Email: " . ($morador_data["email"] ?: 'Não cadastrado') . "</div>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome_visitante">Nome do Visitante:</label>
                        <input type="text" id="nome_visitante" name="nome_visitante" value="<?= $campo["nome_visitante"] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="num_documento">Número do Documento:</label>
                       <div class="cpf-validation">
                           <input type="text" id="num_documento" name="num_documento" value="<?= $campo["num_documento"] ?>" required>
                           <span class="validation-icon" id="cpf-icon"></span>
                       </div>
                       <div class="cpf-error" id="cpf-error"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" value="<?= $campo["telefone"] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= $campo["email"] ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento:</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="<?= $campo["data_nascimento"] ?>" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="foto">Foto (URL):</label>
                    <input type="text" id="foto" name="foto" value="<?= $campo["foto"] ?>" placeholder="https://exemplo.com/foto.jpg">
                    <div style="margin: 0.5rem 0; text-align: center; color: #666;">
                        <span>OU</span>
                    </div>
                    <label for="foto_file">Nova Foto (Arquivo Local):</label>
                    <input type="file" id="foto_file" name="foto_file" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewLocalImage(this)">
                    <small style="color: #666; font-size: 0.8em;">
                        <i class="fas fa-info-circle"></i> 
                        Cole o link da foto OU selecione um arquivo do seu dispositivo (máx. 5MB)
                    </small>
                    <div id="foto-preview" style="margin-top: 0.5rem; <?= $campo["foto"] ? 'display: block;' : 'display: none;' ?>">
                        <img id="preview-img" src="<?= $campo["foto"] ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #3498db;">
                    </div>
                    <div id="foto-preview-local" style="margin-top: 0.5rem; display: none;">
                        <img id="preview-img-local" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #28a745;">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="consultar_visitantes.php" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 ShieldTech. Todos os direitos reservados.</p>
    </footer>
    
    <script src="../../js/cpf-validator.js"></script>
    <script src="../../js/photo-preview.js"></script>
    <script>
        // Dados dos moradores para pesquisa
        const moradores = [
            <?php
            $moradores_query = mysqli_query($conn, "SELECT id_moradores, nome, bloco, torre, telefone, email FROM tb_moradores ORDER BY nome");
            $moradores_js = [];
            while ($morador = mysqli_fetch_array($moradores_query)) {
                $moradores_js[] = "{
                    id: " . $morador["id_moradores"] . ",
                    nome: '" . addslashes($morador["nome"]) . "',
                    bloco: '" . addslashes($morador["bloco"]) . "',
        // Configuração da pesquisa de moradores
        const searchInput = document.getElementById('search_morador');
        const searchResults = document.getElementById('search_results');
        const selectedMoradorDiv = document.getElementById('selected_morador');
        const selectedInfo = document.getElementById('selected_info');
        const idMoradorInput = document.getElementById('id_morador');
                    torre: '" . addslashes($morador["torre"]) . "',
        // Preencher campo de pesquisa com morador atual
        if (idMoradorInput.value) {
            const moradorAtual = moradores.find(m => m.id == idMoradorInput.value);
            if (moradorAtual) {
                searchInput.value = moradorAtual.nome;
            }
        }
                    telefone: '" . addslashes($morador["telefone"]) . "',
        // Função de pesquisa
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
                    email: '" . addslashes($morador["email"] ? $morador["email"] : "") . "'
            const filteredMoradores = moradores.filter(morador => 
                morador.nome.toLowerCase().includes(query)
            );
                }";
            if (filteredMoradores.length > 0) {
                searchResults.innerHTML = '';
                filteredMoradores.forEach(morador => {
                    const div = document.createElement('div');
                    div.className = 'search-item';
                    div.innerHTML = `
                        <div class="morador-info">
                            <div class="morador-name">${morador.nome}</div>
                            <div class="morador-details">Bloco ${morador.bloco}/${morador.torre}</div>
                        </div>
                        <div>
                            ${morador.email ? 
                                `<span class="email-badge">✉️ ${morador.email}</span>` : 
                                `<span class="no-email-badge">⚠️ Sem email</span>`
                            }
                        </div>
                    `;
                    
                    div.addEventListener('click', function() {
                        selectMorador(morador);
                    });
                    
                    searchResults.appendChild(div);
                });
                searchResults.style.display = 'block';
            } else {
                searchResults.innerHTML = '<div class="search-item">Nenhum morador encontrado</div>';
                searchResults.style.display = 'block';
            }
        });
            }
        // Função para selecionar morador
        function selectMorador(morador) {
            idMoradorInput.value = morador.id;
            searchInput.value = morador.nome;
            
            selectedInfo.innerHTML = `
                <div><strong>${morador.nome}</strong></div>
                <div>Bloco ${morador.bloco}/${morador.torre}</div>
                <div>Tel: ${morador.telefone}</div>
                <div>Email: ${morador.email || 'Não cadastrado'}</div>
            `;
            
            selectedMoradorDiv.style.display = 'block';
            searchResults.style.display = 'none';
            
            searchInput.style.borderColor = '#28a745';
            searchInput.style.backgroundColor = '#f8fff8';
        }
            echo implode(",\n            ", $moradores_js);
        // Função para limpar seleção
        function clearSelection() {
            idMoradorInput.value = '';
            searchInput.value = '';
            selectedMoradorDiv.style.display = 'none';
            searchInput.style.borderColor = '';
            searchInput.style.backgroundColor = '';
            searchInput.focus();
        }
            ?>
        // Fechar resultados ao clicar fora
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                searchResults.style.display = 'none';
            }
        });
        ];
        // Configurar validação de CPF para edição de visitante
        document.addEventListener('DOMContentLoaded', () => {
            const visitanteId = <?= $campo["id_visitantes"] ?>;
            CPFValidator.setupCompleteValidation('num_documento', 'cpf-error', 'visitantes', visitanteId);
        });
        
        // Preview de imagem local
        function previewLocalImage(input) {
            const preview = document.getElementById('foto-preview-local');
            const img = document.getElementById('preview-img-local');
            const urlPreview = document.getElementById('foto-preview');
            
            if (input.files && input.files[0]) {
                // Verificar tamanho do arquivo (5MB)
                if (input.files[0].size > 5 * 1024 * 1024) {
                    alert('Arquivo muito grande! Máximo permitido: 5MB');
                    input.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.style.display = 'block';
                    // Ocultar preview da URL quando arquivo local é selecionado
                    if (urlPreview) urlPreview.style.display = 'none';
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
        
        // Validação do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!idMoradorInput.value) {
                e.preventDefault();
                alert('Por favor, selecione um morador da lista de pesquisa.');
                searchInput.focus();
                return false;
            }
        });
    </script>
    
    <style>
        .search-container {
            position: relative;
        }
        
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .search-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s;
        }
        
        .search-item:hover {
            background-color: #f5f5f5;
        }
        
        .search-item:last-child {
            border-bottom: none;
        }
        
        .morador-info {
            flex: 1;
        }
        
        .morador-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .morador-details {
            font-size: 0.9em;
            color: #666;
        }
        
        .email-badge {
            background: #e8f4fd;
            color: #2c3e50;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            border: 1px solid #3498db;
        }
        
        .no-email-badge {
            background: #fff3cd;
            color: #856404;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            border: 1px solid #ffc107;
        }
        
        .selected-morador {
            background: #e8f5e8;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #28a745;
            margin-top: 10px;
            position: relative;
        }
        
        .clear-selection {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            float: right;
            font-size: 1.2em;
            position: absolute;
            top: 5px;
            right: 10px;
        }
        
        .clear-selection:hover {
            color: #a71e2a;
        }
    </style>
</body>
</html>