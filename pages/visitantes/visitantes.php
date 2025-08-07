<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Visitantes - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/cpf-validation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
    include("../../conectarbd.php");
    require_once("../../php/photo-upload.php");
    
    // Processar formulário se foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome_visitante = mysqli_real_escape_string($conn, $_POST["nome_visitante"]);
        $num_documento = mysqli_real_escape_string($conn, $_POST["num_documento"]);
        $telefone = mysqli_real_escape_string($conn, $_POST["telefone"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $data_nascimento = mysqli_real_escape_string($conn, $_POST["data_nascimento"]);
        $status = mysqli_real_escape_string($conn, $_POST["status"]);
        $id_morador = mysqli_real_escape_string($conn, $_POST["id_morador"]);
        
        // Processar upload de foto
        $foto_url = '';
        if (isset($_FILES['foto_file']) && $_FILES['foto_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $photoUpload = new PhotoUpload('visitantes');
            $uploadResult = $photoUpload->uploadPhoto($_FILES['foto_file'], 'visitante');
            
            if ($uploadResult['success']) {
                $foto_url = $uploadResult['url'];
            } else {
                echo "<script>alert('Erro no upload da foto: " . $uploadResult['message'] . "');</script>";
            }
        } elseif (!empty($_POST["foto"])) {
            // Se não há arquivo, usar URL fornecida
            $foto_url = mysqli_real_escape_string($conn, $_POST["foto"]);
        }
        
        $sql = "INSERT INTO tb_visitantes (nome_visitante, num_documento, telefone, email, data_nascimento, foto, status, id_morador) 
                VALUES ('$nome_visitante', '$num_documento', '$telefone', '$email', '$data_nascimento', '$foto_url', '$status', '$id_morador')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Visitante registrado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao registrar visitante: " . mysqli_error($conn) . "');</script>";
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
                        <a href="../veiculos/cadastro_veiculos.php">Veículos</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Controle de Visitantes</h2>
        
        <section class="form-section">
            <h3>Registro de Visitante</h3>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" id="id_morador" name="id_morador" value="">
                
                <div class="form-group">
                    <label for="search_morador">Pesquisar Morador Visitado:</label>
                    <div class="search-container">
                        <input type="text" id="search_morador" name="search_morador" 
                               placeholder="Digite o nome do morador..." 
                               autocomplete="off" required>
                        <div class="search-results" id="search_results"></div>
                    </div>
                    <div class="selected-morador" id="selected_morador">
                        <button type="button" class="clear-selection" onclick="clearSelection()">×</button>
                        <strong>Morador selecionado:</strong>
                        <div id="selected_info"></div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome_visitante">Nome do Visitante:</label>
                        <input type="text" id="nome_visitante" name="nome_visitante" required>
                    </div>

                    <div class="form-group">
                        <label for="num_documento">Número do Documento:</label>
                       <div class="cpf-validation">
                           <input type="text" id="num_documento" name="num_documento" placeholder="CPF" required>
                           <span class="validation-icon" id="cpf-icon"></span>
                       </div>
                       <div class="cpf-error" id="cpf-error"></div>
                       <small style="color: #666;">Digite apenas o CPF (sem RG)</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento:</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="Presente">Presente</option>                   
                        </select>
                    </div>
                </div>


                <div class="form-group full-width">
                    <label for="foto">Foto (URL):</label>
                    <input type="text" id="foto" name="foto" placeholder="https://exemplo.com/foto.jpg">
                    <div style="margin: 0.5rem 0; text-align: center; color: #666;">
                        <span>OU</span>
                    </div>
                    <label for="foto_file">Foto (Arquivo Local):</label>
                    <input type="file" id="foto_file" name="foto_file" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewLocalImage(this)">
                    <small style="color: #666; font-size: 0.8em;">
                        <i class="fas fa-info-circle"></i> 
                        Cole o link da foto OU selecione um arquivo do seu dispositivo (máx. 5MB)
                    </small>
                    <div id="foto-preview" style="margin-top: 0.5rem; display: none;">
                        <img id="preview-img" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #3498db;">
                    </div>
                    <div id="foto-preview-local" style="margin-top: 0.5rem; display: none;">
                        <img id="preview-img-local" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #28a745;">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Registrar Entrada
                    </button>
                    <a href="consultar_visitantes.php" class="btn-secondary">
                        <i class="fas fa-list"></i> Ver Visitantes
                    </a>
                </div>
            </form>
        </section>

        <section class="lista-section">
            <h3>Visitantes Presentes</h3>
            <div class="tabela-container">
                <table class="tabela-relatorio">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Foto</th>
                            <th>Documento</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Morador Visitado</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $selecionar = mysqli_query($conn, "SELECT v.*, m.nome as nome_morador, m.bloco, m.torre 
                                                          FROM tb_visitantes v 
                                                          LEFT JOIN tb_moradores m ON v.id_morador = m.id_moradores 
                                                          WHERE v.status = 'Presente' 
                                                          ORDER BY v.nome_visitante");
                        
                        if (mysqli_num_rows($selecionar) > 0) {
                            while ($campo = mysqli_fetch_array($selecionar)) {
                                echo "<tr>";
                                echo "<td>" . $campo["nome_visitante"] . "</td>";
                                echo "<td>";
                                if ($campo["foto"]) {
                                    echo "<img src='" . $campo["foto"] . "' alt='Foto de " . $campo["nome_visitante"] . "' style='width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #28a745;'>";
                                } else {
                                    echo "<div style='width: 40px; height: 40px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 2px solid #ddd;'>";
                                    echo "<i class='fas fa-user' style='color: #999; font-size: 0.8rem;'></i>";
                                    echo "</div>";
                                }
                                echo "</td>";
                                echo "<td>" . $campo["num_documento"] . "</td>";
                                echo "<td>" . $campo["telefone"] . "</td>";
                                echo "<td>" . ($campo["email"] ? $campo["email"] : "Não informado") . "</td>";
                                echo "<td>" . ($campo["nome_morador"] ? $campo["nome_morador"] . " - Bloco " . $campo["bloco"] . "/" . $campo["torre"] : "Não informado") . "</td>";
                                echo "<td><span class='status-ativo'>" . $campo["status"] . "</span></td>";
                                echo "<td>";
                                echo "<a href='registrar_saida.php?id=" . $campo["id_visitantes"] . "' class='btn-saida'>";
                                echo "<i class='fas fa-sign-out-alt'></i> Registrar Saída</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align: center;'>Nenhum visitante presente</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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
                    torre: '" . addslashes($morador["torre"]) . "',
                    telefone: '" . addslashes($morador["telefone"]) . "',
                    email: '" . addslashes($morador["email"] ? $morador["email"] : "") . "'
                }";
            }
            echo implode(",\n            ", $moradores_js);
            ?>
        ];

        // Configuração da pesquisa de moradores
        const searchInput = document.getElementById('search_morador');
        const searchResults = document.getElementById('search_results');
        const selectedMoradorDiv = document.getElementById('selected_morador');
        const selectedInfo = document.getElementById('selected_info');
        const idMoradorInput = document.getElementById('id_morador');

        // Função de pesquisa
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            const filteredMoradores = moradores.filter(morador => 
                morador.nome.toLowerCase().includes(query)
            );

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

        // Função para selecionar morador
        function selectMorador(morador) {
            // Preencher campos
            idMoradorInput.value = morador.id;
            searchInput.value = morador.nome;
            
            // Mostrar informações do morador selecionado
            selectedInfo.innerHTML = `
                <div><strong>${morador.nome}</strong></div>
                <div>Bloco ${morador.bloco}/${morador.torre}</div>
                <div>Tel: ${morador.telefone}</div>
                <div>Email: ${morador.email || 'Não cadastrado'}</div>
            `;
            
            selectedMoradorDiv.style.display = 'block';
            searchResults.style.display = 'none';
            
            // Adicionar feedback visual
            searchInput.style.borderColor = '#28a745';
            searchInput.style.backgroundColor = '#f8fff8';
        }

        // Função para limpar seleção
        function clearSelection() {
            idMoradorInput.value = '';
            searchInput.value = '';
            selectedMoradorDiv.style.display = 'none';
            searchInput.style.borderColor = '';
            searchInput.style.backgroundColor = '';
            searchInput.focus();
        }

        // Fechar resultados ao clicar fora
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                searchResults.style.display = 'none';
            }
        });

        // Configurar validação de CPF para visitantes
        document.addEventListener('DOMContentLoaded', () => {
            CPFValidator.setupCompleteValidation('num_documento', 'cpf-error', 'visitantes');
        });
        
        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                e.target.value = value;
            }
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
            display: none;
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