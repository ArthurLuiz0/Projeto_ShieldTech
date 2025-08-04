<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Encomenda - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        }
        
        .search-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }
        
        .clear-selection {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            float: right;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <?php
    include("../../conectarbd.php");
    include("GmailEncomendas.php");
    
    // Processar formulário se foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome_morador = mysqli_real_escape_string($conn, $_POST["nome_morador"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $descricao = mysqli_real_escape_string($conn, $_POST["descricao"]);
        $data_recebimento = mysqli_real_escape_string($conn, $_POST["data_recebimento"]);
        $status = mysqli_real_escape_string($conn, $_POST["status"]);
        $id_morador = mysqli_real_escape_string($conn, $_POST["id_morador"]);
        
        $sql = "INSERT INTO tb_encomendas (nome_morador, email, descricao, data_recebimento, status, id_morador) 
                VALUES ('$nome_morador', '$email', '$descricao', '$data_recebimento', '$status', '$id_morador')";
        
        if (mysqli_query($conn, $sql)) {
            enviar_email_encomenda($conn, $nome_morador, $descricao, $data_recebimento);
            echo "<script>alert('Encomenda cadastrada com sucesso!'); window.location = 'consultar_encomendas.php';</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar encomenda: " . mysqli_error($conn) . "');</script>";
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
        <h2>Gestão de Encomendas</h2>

        <div class="form-grid">
            <section class="form-section">
                <h3>Cadastro de Encomenda</h3>
                <form method="post" action="">
                    <input type="hidden" id="id_morador" name="id_morador" value="">
                    
                    <div class="form-group">
                        <label for="search_morador">Pesquisar Morador:</label>
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
                            <label for="nome_morador">Nome do Morador:</label>
                            <input type="text" id="nome_morador" name="nome_morador" readonly required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="descricao">Descrição:</label>
                            <input type="text" id="descricao" name="descricao" placeholder="Descrição da encomenda" required>
                        </div>

                        <div class="form-group">
                            <label for="data_recebimento">Data de Recebimento:</label>
                            <input type="date" id="data_recebimento" name="data_recebimento" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status" required>
                            <option value="">-- Selecione --</option>
                            <option value="Recebido" >Recebido</option>
                            <option value="Entregue" >Entregue</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Cadastrar Encomenda
                        </button>
                        <a href="consultar_encomendas.php" class="btn-secondary">
                            <i class="fas fa-list"></i> Ver Encomendas
                        </a>
                    </div>
                </form>
            </section>

            <section class="info-section">
                <h3>Informações sobre Encomendas</h3>
                <div class="info-cards">
                    <?php
                    $total_encomendas = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_encomendas"));
                    $pendentes = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_encomendas WHERE status = 'Pendente'"));
                    $entregues = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_encomendas WHERE status = 'Entregue'"));
                    $hoje = date('Y-m-d');
                    $hoje_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_encomendas WHERE data_recebimento = '$hoje'"));
                    ?>
                    
                    <div class="info-card">
                        <i class="fas fa-box"></i>
                        <h4>Total de Encomendas</h4>
                        <p><?= $total_encomendas ?></p>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-clock"></i>
                        <h4>Pendentes</h4>
                        <p><?= $pendentes ?></p>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-check"></i>
                        <h4>Entregues</h4>
                        <p><?= $entregues ?></p>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-calendar-day"></i>
                        <h4>Recebidas Hoje</h4>
                        <p><?= $hoje_count ?></p>
                    </div>
                </div>

                <div class="recent-animals">
                    <h4>Últimas Encomendas</h4>
                    <div class="recent-list">
                        <?php
                        $recentes = mysqli_query($conn, "SELECT * FROM tb_encomendas ORDER BY id_encomendas DESC LIMIT 5");
                        while ($encomenda = mysqli_fetch_array($recentes)) {
                            echo "<div class='recent-item'>";
                            echo "<strong>" . $encomenda['nome_morador'] . "</strong>";
                            echo "<br><small>" . $encomenda['descricao'] . " - " . date('d/m/Y', strtotime($encomenda['data_recebimento'])) . "</small>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 ShieldTech. Todos os direitos reservados.</p>
    </footer>

    <script>
        // Dados dos moradores para pesquisa
        const moradores = [
            <?php
            $moradores_query = mysqli_query($conn, "SELECT id_moradores, nome, email, bloco, torre FROM tb_moradores ORDER BY nome");
            $moradores_js = [];
            while ($morador = mysqli_fetch_array($moradores_query)) {
                $moradores_js[] = "{
                    id: " . $morador["id_moradores"] . ",
                    nome: '" . addslashes($morador["nome"]) . "',
                    email: '" . addslashes($morador["email"] ? $morador["email"] : "") . "',
                    bloco: '" . addslashes($morador["bloco"]) . "',
                    torre: '" . addslashes($morador["torre"]) . "'
                }";
            }
            echo implode(",\n            ", $moradores_js);
            ?>
        ];

        const searchInput = document.getElementById('search_morador');
        const searchResults = document.getElementById('search_results');
        const selectedMoradorDiv = document.getElementById('selected_morador');
        const selectedInfo = document.getElementById('selected_info');
        const nomeMoradorInput = document.getElementById('nome_morador');
        const emailInput = document.getElementById('email');
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
            nomeMoradorInput.value = morador.nome;
            emailInput.value = morador.email || '';
            idMoradorInput.value = morador.id;
            searchInput.value = morador.nome;
            
            // Mostrar informações do morador selecionado
            selectedInfo.innerHTML = `
                <div><strong>${morador.nome}</strong></div>
                <div>Bloco ${morador.bloco}/${morador.torre}</div>
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
            nomeMoradorInput.value = '';
            emailInput.value = '';
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

        // Validação do formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!idMoradorInput.value) {
                e.preventDefault();
                alert('Por favor, selecione um morador da lista de pesquisa.');
                searchInput.focus();
                return false;
            }
        });

        // Definir data padrão como hoje
        document.getElementById('data_recebimento').value = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>