<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Visitantes - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
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
        <h2>Histórico de Visitantes</h2>
        
        <div class="actions-bar">
            <a href="visitantes.php" class="btn-primary">
                <i class="fas fa-plus"></i> Novo Visitante
            </a>
        </div>

        <section class="form-section">
            <h3>Pesquisar Visitantes</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="pesquisa">Pesquisar por nome:</label>
                    <input type="text" id="pesquisa" name="pesquisa" placeholder="Digite o nome do visitante..." onkeyup="filtrarVisitantes()">
                </div>
                <div class="form-group">
                    <label for="filtro_status">Filtrar por status:</label>
                    <select id="filtro_status" name="filtro_status" onchange="filtrarVisitantes()">
                        <option value="">Todos os status</option>
                        <option value="Presente">Presente</option>
                        <option value="Saiu">Saiu</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" onclick="limparFiltros()" class="btn-secondary">
                    <i class="fas fa-refresh"></i> Limpar Filtros
                </button>
            </div>
        </section>
        
        <section class="lista-section">
            <div id="resultado-pesquisa" style="margin-bottom: 1rem; font-weight: 500; color: var(--primary-color);"></div>
            <div class="tabela-container">
                <table class="tabela-relatorio">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Documento</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Data Nascimento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-visitantes">
                        <?php
                        include("../../conectarbd.php");
                        $selecionar = mysqli_query($conn, "SELECT * FROM tb_visitantes ORDER BY nome_visitante");
                        
                        if (mysqli_num_rows($selecionar) > 0) {
                            while ($campo = mysqli_fetch_array($selecionar)) {
                                echo "<tr>";
                                echo "<td>" . $campo["id_visitantes"] . "</td>";
                                echo "<td>";
                                echo "</td>";
                                echo "<td>" . $campo["nome_visitante"] . "</td>";
                                echo "<td>" . $campo["num_documento"] . "</td>";
                                echo "<td>" . $campo["telefone"] . "</td>";
                                echo "<td>" . ($campo["email"] ? $campo["email"] : "Não informado") . "</td>";
                                echo "<td>" . date('d/m/Y', strtotime($campo["data_nascimento"])) . "</td>";
                                echo "<td><span class='status-" . strtolower($campo["status"]) . "'>" . $campo["status"] . "</span></td>";
                                echo "<td class='acoes'>";
                                if ($campo["status"] == "Presente") {
                                    echo "<a href='registrar_saida.php?id=" . $campo["id_visitantes"] . "' class='btn-danger'>";
                                    echo "<i class='fas fa-sign-out-alt'></i> Registrar Saída</a>";
                                }
                                echo "<a href='editar_visitante.php?id=" . $campo["id_visitantes"] . "' class='btn-editar'>";
                                echo "<i class='fas fa-edit'></i> Editar</a>";
                                echo "<a href='excluir_visitante.php?id=" . $campo["id_visitantes"] . "' class='btn-excluir' onclick='return confirm(\"Tem certeza que deseja excluir este visitante?\")'>";
                                echo "<i class='fas fa-trash'></i> Excluir</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' style='text-align: center;'>Nenhum visitante cadastrado</td></tr>";
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

    <script>
        // Dados dos visitantes para filtro em JavaScript
        const visitantes = [
            <?php
            $selecionar_js = mysqli_query($conn, "SELECT * FROM tb_visitantes ORDER BY nome_visitante");
            $visitantes_js = [];
            while ($campo = mysqli_fetch_array($selecionar_js)) {
                $visitantes_js[] = "{
                    id: " . $campo["id_visitantes"] . ",
                    nome: '" . addslashes($campo["nome_visitante"]) . "',
                    documento: '" . addslashes($campo["num_documento"]) . "',
                    telefone: '" . addslashes($campo["telefone"]) . "',
                    email: '" . addslashes($campo["email"] ? $campo["email"] : "Não informado") . "',
                    data_nascimento: '" . addslashes(date('d/m/Y', strtotime($campo["data_nascimento"]))) . "',
                    status: '" . addslashes($campo["status"]) . "'
                }";
            }
            echo implode(",\n            ", $visitantes_js);
            ?>
        ];

        function filtrarVisitantes() {
            const pesquisa = document.getElementById('pesquisa').value.toLowerCase();
            const filtroStatus = document.getElementById('filtro_status').value.toLowerCase();
            const tbody = document.getElementById('tabela-visitantes');
            const resultadoPesquisa = document.getElementById('resultado-pesquisa');
            
            let visitantesFiltrados = visitantes.filter(visitante => {
                const nomeMatch = visitante.nome.toLowerCase().includes(pesquisa);
                const statusMatch = filtroStatus === '' || visitante.status.toLowerCase() === filtroStatus;
                return nomeMatch && statusMatch;
            });
            
            // Atualizar resultado da pesquisa
            if (pesquisa || filtroStatus) {
                resultadoPesquisa.style.color = '#3498db';
                resultadoPesquisa.textContent = `Encontrados ${visitantesFiltrados.length} visitante(s)`;
            } else {
                resultadoPesquisa.textContent = '';
            }
            
            // Limpar tabela
            tbody.innerHTML = '';
            
            if (visitantesFiltrados.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center;">Nenhum visitante encontrado</td></tr>';
                return;
            }
            
            // Preencher tabela com resultados filtrados
            visitantesFiltrados.forEach(visitante => {
                const tr = document.createElement('tr');
                const statusClass = visitante.status.toLowerCase() === 'presente' ? 'status-presente' : 'status-ativo';
                
                tr.innerHTML = `
                    <td>${visitante.id}</td>
                    <td>${destacarTexto(visitante.nome, pesquisa)}</td>
                    <td>${visitante.documento}</td>
                    <td>${visitante.telefone}</td>
                    <td>${visitante.email}</td>
                    <td>${visitante.data_nascimento}</td>
                    <td><span class='${statusClass}'>${visitante.status}</span></td>
                    <td class='acoes'>
                        ${visitante.status === 'Presente' ? 
                            `<a href='registrar_saida.php?id=${visitante.id}' class='btn-danger'>
                                <i class='fas fa-sign-out-alt'></i> Registrar Saída
                            </a>` : ''
                        }
                        <a href='editar_visitante.php?id=${visitante.id}' class='btn-editar'>
                            <i class='fas fa-edit'></i> Editar
                        </a>
                        <a href='excluir_visitante.php?id=${visitante.id}' class='btn-excluir' onclick='return confirm("Tem certeza que deseja excluir este visitante?")'>
                            <i class='fas fa-trash'></i> Excluir
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
        
        function limparFiltros() {
            document.getElementById('pesquisa').value = '';
            document.getElementById('filtro_status').value = '';
            filtrarVisitantes();
        }
        
        // Destacar texto pesquisado
        function destacarTexto(texto, pesquisa) {
            if (!pesquisa) return texto;
            const regex = new RegExp(`(${pesquisa})`, 'gi');
            return texto.replace(regex, '<mark>$1</mark>');
        }
    </script>
</body>
</html>