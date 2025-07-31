<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Veículo - ShieldTech</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php
    include("../../conectarbd.php");
    
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    // Processar formulário se foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = mysqli_real_escape_string($conn, $_POST["id"]);
        $placa = mysqli_real_escape_string($conn, $_POST["placa"]);
        $modelo = mysqli_real_escape_string($conn, $_POST["modelo"]);
        $cor = mysqli_real_escape_string($conn, $_POST["cor"]);
        $tipo = mysqli_real_escape_string($conn, $_POST["tipo"]);
        $id_morador = mysqli_real_escape_string($conn, $_POST["id_morador"]);
        
        // Verificar se a placa já existe em outro veículo
        $verificar_placa = mysqli_query($conn, "SELECT * FROM tb_veiculos WHERE placa = '$placa' AND id_veiculos != $id");
        if (mysqli_num_rows($verificar_placa) > 0) {
            echo "<script>alert('Esta placa já está cadastrada em outro veículo!');</script>";
        } else {
            $sql = "UPDATE tb_veiculos SET 
                    placa='$placa', modelo='$modelo', cor='$cor', 
                    tipo='$tipo', id_morador='$id_morador' 
                    WHERE id_veiculos=$id";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Veículo atualizado com sucesso!'); window.location = 'consultar_veiculos.php';</script>";
            } else {
                echo "<script>alert('Erro ao atualizar veículo: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
    
    // Buscar dados do veículo
    $selecionar = mysqli_query($conn, "SELECT * FROM tb_veiculos WHERE id_veiculos=$id");
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
        <h2>Editar Veículo</h2>

        <section class="form-section">
            <h3>Alterar Dados do Veículo</h3>
            <form method="post" action="">
                <input type="hidden" name="id" value="<?= $campo["id_veiculos"] ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="placa">Placa:</label>
                        <input type="text" id="placa" name="placa" value="<?= $campo["placa"] ?>" required maxlength="8">
                    </div>

                    <div class="form-group">
                        <label for="modelo">Modelo:</label>
                        <input type="text" id="modelo" name="modelo" value="<?= $campo["modelo"] ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cor">Cor:</label>
                        <select id="cor" name="cor" required>
                            <option value="">Selecione a cor</option>
                            <option value="Branco" <?= $campo["cor"] == "Branco" ? "selected" : "" ?>>Branco</option>
                            <option value="Preto" <?= $campo["cor"] == "Preto" ? "selected" : "" ?>>Preto</option>
                            <option value="Prata" <?= $campo["cor"] == "Prata" ? "selected" : "" ?>>Prata</option>
                            <option value="Cinza" <?= $campo["cor"] == "Cinza" ? "selected" : "" ?>>Cinza</option>
                            <option value="Azul" <?= $campo["cor"] == "Azul" ? "selected" : "" ?>>Azul</option>
                            <option value="Vermelho" <?= $campo["cor"] == "Vermelho" ? "selected" : "" ?>>Vermelho</option>
                            <option value="Verde" <?= $campo["cor"] == "Verde" ? "selected" : "" ?>>Verde</option>
                            <option value="Amarelo" <?= $campo["cor"] == "Amarelo" ? "selected" : "" ?>>Amarelo</option>
                            <option value="Marrom" <?= $campo["cor"] == "Marrom" ? "selected" : "" ?>>Marrom</option>
                            <option value="Outro" <?= $campo["cor"] == "Outro" ? "selected" : "" ?>>Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo:</label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Selecione o tipo</option>
                            <option value="Carro" <?= $campo["tipo"] == "Carro" ? "selected" : "" ?>>Carro</option>
                            <option value="Moto" <?= $campo["tipo"] == "Moto" ? "selected" : "" ?>>Moto</option>
                            <option value="Caminhonete" <?= $campo["tipo"] == "Caminhonete" ? "selected" : "" ?>>Caminhonete</option>
                            <option value="SUV" <?= $campo["tipo"] == "SUV" ? "selected" : "" ?>>SUV</option>
                            <option value="Van" <?= $campo["tipo"] == "Van" ? "selected" : "" ?>>Van</option>
                            <option value="Outro" <?= $campo["tipo"] == "Outro" ? "selected" : "" ?>>Outro</option>
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
                            $selected = ($campo["id_morador"] == $morador["id_moradores"]) ? "selected" : "";
                            echo "<option value='" . $morador["id_moradores"] . "' $selected>" . $morador["nome"] . " - Bloco " . $morador["bloco"] . "/" . $morador["torre"] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="consultar_veiculos.php" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </section>
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