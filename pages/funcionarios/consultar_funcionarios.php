<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Funcionários - ShieldTech</title>
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
        <h2>Funcionários Cadastrados</h2>
        
        <div class="actions-bar">
            <a href="cadastro_funcionarios.php" class="btn-primary">
                <i class="fas fa-plus"></i> Novo Funcionário
            </a>
        </div>

        <section class="form-section">
            <h3>Pesquisar Funcionários</h3>
            <form method="GET" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="pesquisa">Pesquisar por nome:</label>
                        <input type="text" id="pesquisa" name="pesquisa" placeholder="Digite o nome do funcionário..." 
                               value="<?= $_GET['pesquisa'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="filtro_cargo">Filtrar por cargo:</label>
                        <select id="filtro_cargo" name="filtro_cargo">
                            <option value="">Todos os cargos</option>
                            <?php
                            $cargos = mysqli_query($conn, "SELECT DISTINCT funcao_cargo FROM tb_funcionarios WHERE funcao_cargo IS NOT NULL ORDER BY funcao_cargo");
                            while ($cargo = mysqli_fetch_array($cargos)) {
                                $selected = (isset($_GET['filtro_cargo']) && $_GET['filtro_cargo'] == $cargo['funcao_cargo']) ? 'selected' : '';
                                echo "<option value='" . $cargo["funcao_cargo"] . "' $selected>" . $cargo["funcao_cargo"] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search"></i> Pesquisar
                    </button>
                    <a href="consultar_funcionarios.php" class="btn-secondary">
                        <i class="fas fa-refresh"></i> Limpar Filtros
                    </a>
                </div>
            </form>
        </section>

        <section class="lista-section">
            <div class="cards-funcionarios">
                <?php
                include("../../conectarbd.php");
                
                // Construir query com filtros
                $sql = "SELECT * FROM tb_funcionarios WHERE 1=1";
                
                if (isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {
                    $pesquisa = mysqli_real_escape_string($conn, $_GET['pesquisa']);
                    $sql .= " AND nome LIKE '%$pesquisa%'";
                }
                
                if (isset($_GET['filtro_cargo']) && !empty($_GET['filtro_cargo'])) {
                    $cargo = mysqli_real_escape_string($conn, $_GET['filtro_cargo']);
                    $sql .= " AND funcao_cargo = '$cargo'";
                }
                
                $sql .= " ORDER BY nome";
                
                $selecionar = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($selecionar) > 0) {
                    $total_encontrados = mysqli_num_rows($selecionar);
                    if (isset($_GET['pesquisa']) || isset($_GET['filtro_cargo'])) {
                        echo "<div style='margin-bottom: 1rem; padding: 0.5rem; background: #e8f4fd; border-radius: 0.5rem; border-left: 4px solid #3498db;'>";
                        echo "<i class='fas fa-info-circle'></i> Encontrados $total_encontrados funcionário(s)";
                        echo "</div>";
                    }
                    
                    while ($campo = mysqli_fetch_array($selecionar)) {
                        echo "<div class='card-funcionario'>";
                        echo "<div class='card-header'>";
                        echo "<i class='fas fa-user-tie'></i>";
                        echo "<h4>" . $campo["nome"] . "</h4>";
                        echo "</div>";
                        echo "<div class='card-body'>";
                        echo "<p><i class='fas fa-id-badge'></i> <strong>CPF:</strong> " . $campo["cpf"] . "</p>";
                        echo "<p><i class='fas fa-briefcase'></i> <strong>Cargo:</strong> " . $campo["funcao_cargo"] . "</p>";
                        echo "<p><i class='fas fa-calendar-alt'></i> <strong>Admissão:</strong> " . date('d/m/Y', strtotime($campo["data_admissao"])) . "</p>";
                        echo "<p><i class='fas fa-phone'></i> <strong>Telefone:</strong> " . $campo["telefone"] . "</p>";
                        echo "<p><i class='fas fa-envelope'></i> <strong>Email:</strong> " . $campo["email"] . "</p>";
                        echo "<p><i class='fas fa-dollar-sign'></i> <strong>Salário:</strong> R$ " . number_format($campo["salario"], 2, ',', '.') . "</p>";
                        echo "</div>";
                        echo "<div class='card-footer'>";
                        echo "<a href='editar_funcionario.php?id=" . $campo["id_funcionarios"] . "' class='btn-editar'>";
                        echo "<i class='fas fa-edit'></i> Editar</a>";
                        echo "<a href='excluir_funcionario.php?id=" . $campo["id_funcionarios"] . "' class='btn-remover' onclick='return confirm(\"Tem certeza que deseja excluir este funcionário?\")'>";
                        echo "<i class='fas fa-trash'></i> Remover</a>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='sem-registros'>";
                    echo "<i class='fas fa-user-slash'></i>";
                    echo "<p>Nenhum funcionário cadastrado</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 ShieldTech. Todos os direitos reservados.</p>
    </footer>
</body>
</html>