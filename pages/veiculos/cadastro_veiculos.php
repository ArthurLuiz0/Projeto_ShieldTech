<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Veículos - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
    include("../../conectarbd.php");
    
    // Processar formulário se foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $placa = mysqli_real_escape_string($conn, $_POST["placa"]);
        $modelo = mysqli_real_escape_string($conn, $_POST["modelo"]);
        $cor = mysqli_real_escape_string($conn, $_POST["cor"]);
        $tipo = mysqli_real_escape_string($conn, $_POST["tipo"]);
        $id_morador = mysqli_real_escape_string($conn, $_POST["id_morador"]);
        
        // Verificar se a placa já existe
        $verificar_placa = mysqli_query($conn, "SELECT * FROM tb_veiculos WHERE placa = '$placa'");
        if (mysqli_num_rows($verificar_placa) > 0) {
            echo "<script>alert('Esta placa já está cadastrada!');</script>";
        } else {
            $sql = "INSERT INTO tb_veiculos (placa, modelo, cor, tipo, id_morador) 
                    VALUES ('$placa', '$modelo', '$cor', '$tipo', '$id_morador')";
            
            if (mysqli_query($conn, $sql)) {
                // Atualizar status do morador para "Possui" veículo
                mysqli_query($conn, "UPDATE tb_moradores SET veiculo = 'Possui' WHERE id_moradores = $id_morador");
                echo "<script>alert('Veículo cadastrado com sucesso!'); window.location = 'consultar_veiculos.php';</script>";
            } else {
                echo "<script>alert('Erro ao cadastrar veículo: " . mysqli_error($conn) . "');</script>";
            }
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
        <h2>Gestão de Veículos</h2>

        <div class="form-grid">
            <section class="form-section">
                <h3>Cadastro de Veículo</h3>
                <form method="post" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="placa">Placa:</label>
                            <input type="text" id="placa" name="placa" placeholder="ABC-1234" required maxlength="8">
                        </div>

                        <div class="form-group">
                            <label for="modelo">Modelo:</label>
                            <input type="text" id="modelo" name="modelo" placeholder="Ex: Honda Civic" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cor">Cor:</label>
                            <select id="cor" name="cor" required>
                                <option value="">Selecione a cor</option>
                                <option value="Branco">Branco</option>
                                <option value="Preto">Preto</option>
                                <option value="Prata">Prata</option>
                                <option value="Cinza">Cinza</option>
                                <option value="Azul">Azul</option>
                                <option value="Vermelho">Vermelho</option>
                                <option value="Verde">Verde</option>
                                <option value="Amarelo">Amarelo</option>
                                <option value="Marrom">Marrom</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tipo">Tipo:</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Selecione o tipo</option>
                                <option value="Carro">Carro</option>
                                <option value="Moto">Moto</option>
                                <option value="Caminhonete">Caminhonete</option>
                                <option value="SUV">SUV</option>
                                <option value="Van">Van</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_morador">Morador Proprietário:</label>
                        <select id="id_morador" name="id_morador" required>
                            <option value="">Selecione um morador</option>
                            <?php
                            $moradores = mysqli_query($conn, "SELECT * FROM tb_moradores ORDER BY nome");
                            while ($morador = mysqli_fetch_array($moradores)) {
                                echo "<option value='" . $morador["id_moradores"] . "'>" . $morador["nome"] . " - Bloco " . $morador["bloco"] . "/" . $morador["torre"] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Cadastrar Veículo
                        </button>
                        <a href="consultar_veiculos.php" class="btn-secondary">
                            <i class="fas fa-list"></i> Ver Veículos
                        </a>
                    </div>
                </form>
            </section>

            <section class="info-section">
                <h3>Informações sobre Veículos</h3>
                <div class="info-cards">
                    <?php
                    $total_veiculos = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_veiculos"));
                    $carros = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_veiculos WHERE tipo = 'Carro'"));
                    $motos = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_veiculos WHERE tipo = 'Moto'"));
                    $outros = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_veiculos WHERE tipo NOT IN ('Carro', 'Moto')"));
                    ?>
                    
                    <div class="info-card">
                        <i class="fas fa-car"></i>
                        <h4>Total de Veículos</h4>
                        <p><?= $total_veiculos ?></p>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-car"></i>
                        <h4>Carros</h4>
                        <p><?= $carros ?></p>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-motorcycle"></i>
                        <h4>Motos</h4>
                        <p><?= $motos ?></p>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-truck"></i>
                        <h4>Outros</h4>
                        <p><?= $outros ?></p>
                    </div>
                </div>

                <div class="recent-animals">
                    <h4>Últimos Veículos Cadastrados</h4>
                    <div class="recent-list">
                        <?php
                        $recentes = mysqli_query($conn, "SELECT v.*, m.nome as nome_morador FROM tb_veiculos v
                                                        LEFT JOIN tb_moradores m ON v.id_morador = m.id_moradores 
                                                        ORDER BY v.id_veiculos DESC LIMIT 5");
                        while ($veiculo = mysqli_fetch_array($recentes)) {
                            echo "<div class='recent-item'>";
                            echo "<strong>" . $veiculo['placa'] . "</strong> - " . $veiculo['modelo'];
                            echo "<br><small>Proprietário: " . $veiculo['nome_morador'] . "</small>";
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
        // Máscara para placa
        document.getElementById('placa').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
            if (value.length <= 7) {
                // Formato brasileiro: ABC-1234
                if (value.length > 3) {
                    value = value.replace(/^([A-Z]{3})(\d{1,4}).*/, '$1-$2');
                }
                e.target.value = value;
            }
        });
    </script>
</body>
</html>