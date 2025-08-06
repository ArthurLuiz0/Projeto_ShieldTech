<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Cargos - ShieldTech</title>
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
        <h2>Cargos Cadastrados</h2>
        
        <div class="actions-bar">
            <a href="cadastro_cargos.php" class="btn-primary">
                <i class="fas fa-plus"></i> Novo Cargo
            </a>
        </div>

        <section class="form-section">
            <h3>Pesquisar Cargos</h3>
            <form method="GET" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="pesquisa">Pesquisar por nome:</label>
                        <input type="text" id="pesquisa" name="pesquisa" placeholder="Digite o nome do cargo..." 
                               value="<?= $_GET['pesquisa'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="filtro_salario">Filtrar por faixa salarial:</label>
                        <select id="filtro_salario" name="filtro_salario">
                            <option value="">Todas as faixas</option>
                            <option value="0-1500" <?= (isset($_GET['filtro_salario']) && $_GET['filtro_salario'] == '0-1500') ? 'selected' : '' ?>>Até R$ 1.500</option>
                            <option value="1500-3000" <?= (isset($_GET['filtro_salario']) && $_GET['filtro_salario'] == '1500-3000') ? 'selected' : '' ?>>R$ 1.500 - R$ 3.000</option>
                            <option value="3000-5000" <?= (isset($_GET['filtro_salario']) && $_GET['filtro_salario'] == '3000-5000') ? 'selected' : '' ?>>R$ 3.000 - R$ 5.000</option>
                            <option value="5000+" <?= (isset($_GET['filtro_salario']) && $_GET['filtro_salario'] == '5000+') ? 'selected' : '' ?>>Acima de R$ 5.000</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search"></i> Pesquisar
                    </button>
                    <a href="consultar_cargos.php" class="btn-secondary">
                        <i class="fas fa-refresh"></i> Limpar Filtros
                    </a>
                </div>
            </form>
        </section>
        <section class="lista-section">
            <div class="tabela-container">
                <table class="tabela-relatorio">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome do Cargo</th>
                            <th>Descrição</th>
                            <th>Salário Base</th>
                            <th>Carga Horária</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include("../../conectarbd.php");
                        
                        // Construir query com filtros
                        $sql = "SELECT * FROM tb_cargo WHERE 1=1";
                        
                        if (isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {
                            $pesquisa = mysqli_real_escape_string($conn, $_GET['pesquisa']);
                            $sql .= " AND nome_cargo LIKE '%$pesquisa%'";
                        }
                        
                        if (isset($_GET['filtro_salario']) && !empty($_GET['filtro_salario'])) {
                            $faixa = $_GET['filtro_salario'];
                            switch($faixa) {
                                case '0-1500':
                                    $sql .= " AND CAST(salario_base AS DECIMAL(10,2)) <= 1500";
                                    break;
                                case '1500-3000':
                                    $sql .= " AND CAST(salario_base AS DECIMAL(10,2)) > 1500 AND CAST(salario_base AS DECIMAL(10,2)) <= 3000";
                                    break;
                                case '3000-5000':
                                    $sql .= " AND CAST(salario_base AS DECIMAL(10,2)) > 3000 AND CAST(salario_base AS DECIMAL(10,2)) <= 5000";
                                    break;
                                case '5000+':
                                    $sql .= " AND CAST(salario_base AS DECIMAL(10,2)) > 5000";
                                    break;
                            }
                        }
                        
                        $sql .= " ORDER BY nome_cargo";
                        
                        $selecionar = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($selecionar) > 0) {
                            $total_encontrados = mysqli_num_rows($selecionar);
                            if (isset($_GET['pesquisa']) || isset($_GET['filtro_salario'])) {
                                echo "<div style='margin-bottom: 1rem; padding: 0.5rem; background: #e8f4fd; border-radius: 0.5rem; border-left: 4px solid #3498db;'>";
                                echo "<i class='fas fa-info-circle'></i> Encontrados $total_encontrados cargo(s)";
                                echo "</div>";
                            }
                            
                            while ($campo = mysqli_fetch_array($selecionar)) {
                                echo "<tr>";
                                echo "<td>" . $campo["id_cargos"] . "</td>";
                                echo "<td>" . $campo["nome_cargo"] . "</td>";
                                echo "<td>" . $campo["descricao"] . "</td>";
                                echo "<td>R$ " . number_format($campo["salario_base"], 2, ',', '.') . "</td>";
                                echo "<td>" . $campo["carga_horaria"] . "</td>";
                                echo "<td class='acoes'>";
                                echo "<a href='editar_cargo.php?id=" . $campo["id_cargos"] . "' class='btn-editar'>";
                                echo "<i class='fas fa-edit'></i> Editar</a>";
                                echo "<a href='excluir_cargo.php?id=" . $campo["id_cargos"] . "' class='btn-excluir' onclick='return confirm(\"Tem certeza que deseja excluir este cargo?\")'>";
                                echo "<i class='fas fa-trash'></i> Excluir</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center;'>Nenhum cargo cadastrado</td></tr>";
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
</body>
</html>