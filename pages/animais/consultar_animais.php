<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Animais - ShieldTech</title>
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
        <h2>Animais Cadastrados</h2>
        
        <div class="actions-bar">
            <a href="cadastro_animais.php" class="btn-primary">
                <i class="fas fa-plus"></i> Novo Animal
            </a>
        </div>

        <section class="form-section">
            <h3>Pesquisar Animais</h3>
            <form method="GET" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="pesquisa">Pesquisar por nome:</label>
                        <input type="text" id="pesquisa" name="pesquisa" placeholder="Digite o nome do animal..." 
                               value="<?= $_GET['pesquisa'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="filtro_tipo">Filtrar por tipo:</label>
                        <select id="filtro_tipo" name="filtro_tipo">
                            <option value="">Todos os tipos</option>
                            <?php
                            $tipos = mysqli_query($conn, "SELECT DISTINCT tipo FROM tb_animais WHERE tipo IS NOT NULL ORDER BY tipo");
                            while ($tipo = mysqli_fetch_array($tipos)) {
                                $selected = (isset($_GET['filtro_tipo']) && $_GET['filtro_tipo'] == $tipo['tipo']) ? 'selected' : '';
                                echo "<option value='" . $tipo["tipo"] . "' $selected>" . $tipo["tipo"] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search"></i> Pesquisar
                    </button>
                    <a href="consultar_animais.php" class="btn-secondary">
                        <i class="fas fa-refresh"></i> Limpar Filtros
                    </a>
                </div>
            </form>
        </section>
        <section class="lista-section">
            <div class="cards-animais">
                <?php
                include("../../conectarbd.php");
                
                // Construir query com filtros
                $sql = "SELECT a.*, m.nome as nome_morador, m.bloco, m.torre 
                        FROM tb_animais a 
                        LEFT JOIN tb_moradores m ON a.id_morador = m.id_moradores 
                        WHERE 1=1";
                
                if (isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {
                    $pesquisa = mysqli_real_escape_string($conn, $_GET['pesquisa']);
                    $sql .= " AND a.nome LIKE '%$pesquisa%'";
                }
                
                if (isset($_GET['filtro_tipo']) && !empty($_GET['filtro_tipo'])) {
                    $tipo = mysqli_real_escape_string($conn, $_GET['filtro_tipo']);
                    $sql .= " AND a.tipo = '$tipo'";
                }
                
                $sql .= " ORDER BY a.nome";
                
                $selecionar = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($selecionar) > 0) {
                    $total_encontrados = mysqli_num_rows($selecionar);
                    if (isset($_GET['pesquisa']) || isset($_GET['filtro_tipo'])) {
                        echo "<div style='margin-bottom: 1rem; padding: 0.5rem; background: #e8f4fd; border-radius: 0.5rem; border-left: 4px solid #3498db;'>";
                        echo "<i class='fas fa-info-circle'></i> Encontrados $total_encontrados animal(is)";
                        echo "</div>";
                    }
                    
                    while ($campo = mysqli_fetch_array($selecionar)) {
                        echo "<div class='card-animal'>";
                        echo "<div class='card-header'>";
                        echo "<i class='fas fa-paw'></i>";
                        echo "<h4>" . $campo["nome"] . "</h4>";
                        echo "</div>";
                        echo "<div class='card-body'>";
                        echo "<p><i class='fas fa-tag'></i> <strong>Tipo:</strong> " . $campo["tipo"] . "</p>";
                        echo "<p><i class='fas fa-ruler'></i> <strong>Porte:</strong> " . $campo["porte"] . "</p>";
                        echo "<p><i class='fas fa-user'></i> <strong>Responsável:</strong> " . $campo["nome_morador"] . "</p>";
                        echo "<p><i class='fas fa-home'></i> <strong>Localização:</strong> Bloco " . $campo["bloco"] . "/" . $campo["torre"] . "</p>";
                        if ($campo["observacoes"]) {
                            echo "<p><i class='fas fa-sticky-note'></i> <strong>Observações:</strong> " . $campo["observacoes"] . "</p>";
                        }
                        echo "</div>";
                        echo "<div class='card-footer'>";
                        echo "<a href='editar_animal.php?id=" . $campo["id_animais"] . "' class='btn-editar'>";
                        echo "<i class='fas fa-edit'></i> Editar</a>";
                        echo "<a href='excluir_animal.php?id=" . $campo["id_animais"] . "' class='btn-remover' onclick='return confirm(\"Tem certeza que deseja excluir este animal?\")'>";
                        echo "<i class='fas fa-trash'></i> Remover</a>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='sem-registros'>";
                    echo "<i class='fas fa-paw'></i>";
                    echo "<p>Nenhum animal cadastrado</p>";
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