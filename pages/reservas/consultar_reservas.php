<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Reservas - ShieldTech</title>
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
                <li class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-gear"></i> Cadastros</a>
                    <div class="dropdown-content">
                        <a href="../moradores/cadastro_moradores.php">Moradores</a>
                        <a href="../funcionarios/cadastro_funcionarios.php">Funcionários</a>
                        <a href="../cargos/cadastro_cargos.php">Cargos</a>
                        <a href="../animais/cadastro_animais.php">Animais</a>
                    </div>
                </li>
                <li><a href="reservas.php"><i class="fas fa-calendar-alt"></i> Reservas</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Reservas Cadastradas</h2>
        
        <div class="actions-bar">
            <a href="reservas.php" class="btn-primary">
                <i class="fas fa-plus"></i> Nova Reserva
            </a>
        </div>

        <section class="lista-section">
            <div class="tabela-container">
                <table class="tabela-relatorio">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Local</th>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Duração</th>
                            <th>Morador</th>
                            <th>Observações</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include("../../conectarbd.php");
                        
                        $sql = "SELECT r.*, m.nome as nome_morador, m.bloco, m.torre 
                                FROM tb_reservas r 
                                LEFT JOIN tb_moradores m ON r.id_moradores = m.id_moradores 
                                ORDER BY r.data DESC, r.horario DESC";
                        
                        $selecionar = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($selecionar) > 0) {
                            while ($campo = mysqli_fetch_array($selecionar)) {
                                $hoje = date('Y-m-d');
                                $data_reserva = $campo["data"];
                                $status = ($data_reserva >= $hoje) ? 'Confirmada' : 'Realizada';
                                $status_class = ($data_reserva >= $hoje) ? 'status-ativo' : 'status-presente';
                                
                                echo "<tr>";
                                echo "<td>" . $campo["id_reservas"] . "</td>";
                                echo "<td>" . $campo["local"] . "</td>";
                                echo "<td>" . date('d/m/Y', strtotime($campo["data"])) . "</td>";
                                echo "<td>" . $campo["horario"] . "</td>";
                                echo "<td>" . $campo["tempo_duracao"] . "</td>";
                                echo "<td>" . $campo["nome_morador"] . " - Bloco " . $campo["bloco"] . "/" . $campo["torre"] . "</td>";
                                echo "<td>" . ($campo["descricao"] ? substr($campo["descricao"], 0, 50) . "..." : "Sem observações") . "</td>";
                                echo "<td><span class='$status_class'>$status</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align: center;'>Nenhuma reserva encontrada</td></tr>";
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