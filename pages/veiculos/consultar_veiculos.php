<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Veículos - ShieldTech</title>
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
        <h2>Veículos Cadastrados</h2>
        
        <div class="actions-bar">
            <a href="cadastro_veiculos.php" class="btn-primary">
                <i class="fas fa-plus"></i> Novo Veículo
            </a>
        </div>

        <section class="lista-section">
            <div class="cards-animais">
                <?php
                include("../../conectarbd.php");
                $selecionar = mysqli_query($conn, "SELECT v.*, m.nome as nome_morador, m.bloco, m.torre 
                                                  FROM tb_veiculos v 
                                                  LEFT JOIN tb_moradores m ON v.id_morador = m.id_moradores 
                                                  ORDER BY v.placa");
                
                if (mysqli_num_rows($selecionar) > 0) {
                    while ($campo = mysqli_fetch_array($selecionar)) {
                        echo "<div class='card-animal'>";
                        echo "<div class='card-header'>";
                        echo "<i class='fas fa-car'></i>";
                        echo "<h4>" . $campo["placa"] . "</h4>";
                        echo "</div>";
                        echo "<div class='card-body'>";
                        echo "<p><i class='fas fa-car'></i> <strong>Modelo:</strong> " . $campo["modelo"] . "</p>";
                        echo "<p><i class='fas fa-palette'></i> <strong>Cor:</strong> " . $campo["cor"] . "</p>";
                        echo "<p><i class='fas fa-tag'></i> <strong>Tipo:</strong> " . $campo["tipo"] . "</p>";
                        echo "<p><i class='fas fa-user'></i> <strong>Proprietário:</strong> " . $campo["nome_morador"] . "</p>";
                        echo "<p><i class='fas fa-home'></i> <strong>Localização:</strong> Bloco " . $campo["bloco"] . "/" . $campo["torre"] . "</p>";
                        echo "</div>";
                        echo "<div class='card-footer'>";
                        echo "<a href='editar_veiculo.php?id=" . $campo["id_veiculos"] . "' class='btn-editar'>";
                        echo "<i class='fas fa-edit'></i> Editar</a>";
                        echo "<a href='excluir_veiculo.php?id=" . $campo["id_veiculos"] . "' class='btn-remover' onclick='return confirm(\"Tem certeza que deseja excluir este veículo?\")'>";
                        echo "<i class='fas fa-trash'></i> Remover</a>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='sem-registros'>";
                    echo "<i class='fas fa-car'></i>";
                    echo "<p>Nenhum veículo cadastrado</p>";
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