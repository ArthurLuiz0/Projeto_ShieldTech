<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Moradores - ShieldTech</title>
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
        <h2>Moradores Cadastrados</h2>
        
        <div class="actions-bar">
            <a href="cadastro_moradores.php" class="btn-primary">
                <i class="fas fa-plus"></i> Novo Morador
            </a>
        </div>

        <section class="form-section">
            <h3>Pesquisar Moradores</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="pesquisa">Pesquisar por nome:</label>
                    <input type="text" id="pesquisa" name="pesquisa" placeholder="Digite o nome do morador..." onkeyup="filtrarMoradores()">
                </div>
                <div class="form-group">
                    <label for="filtro_bloco">Filtrar por bloco:</label>
                    <select id="filtro_bloco" name="filtro_bloco" onchange="filtrarMoradores()">
                        <option value="">Todos os blocos</option>
                        <?php
                        include("../../conectarbd.php");
                        $blocos = mysqli_query($conn, "SELECT DISTINCT bloco FROM tb_moradores WHERE bloco IS NOT NULL AND bloco != '' ORDER BY bloco");
                        while ($bloco = mysqli_fetch_array($blocos)) {
                            echo "<option value='" . $bloco["bloco"] . "'>" . $bloco["bloco"] . "</option>";
                        }
                        ?>
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
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Bloco/Torre</th>
                            <th>Andar</th>
                            <th>Veículo</th>
                            <th>Animais</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-moradores">
                        <?php
                        $selecionar = mysqli_query($conn, "SELECT m.*, a.nome as nome_animal, a.tipo as tipo_animal 
                                                          FROM tb_moradores m 
                                                          LEFT JOIN tb_animais a ON m.id_moradores = a.id_morador 
                                                          ORDER BY m.nome");
                        
                        if (mysqli_num_rows($selecionar) > 0) {
                            while ($campo = mysqli_fetch_array($selecionar)) {
                                // Buscar veículos do morador
                                $veiculos_query = mysqli_query($conn, "SELECT placa, modelo FROM tb_veiculos WHERE id_morador = " . $campo["id_moradores"]);
                                $veiculo_info = "";
                                $veiculo_class = "";
                                
                                if (mysqli_num_rows($veiculos_query) > 0) {
                                    $veiculos = [];
                                    while ($veiculo = mysqli_fetch_array($veiculos_query)) {
                                        $veiculos[] = $veiculo["modelo"] . " (" . $veiculo["placa"] . ")";
                                    }
                                    $veiculo_info = implode(", ", $veiculos);
                                    $veiculo_class = "status-ativo";
                                } else {
                                    // Verificar se o morador marcou que possui veículo
                                    if ($campo["veiculo"] == "Possui") {
                                        $veiculo_info = "Possui - Não cadastrado";
                                        $veiculo_class = "status-warning";
                                    } else {
                                        $veiculo_info = "Não possui";
                                        $veiculo_class = "status-inactive";
                                    }
                                }
                                
                                // Buscar informações do animal
                                $animal_info = "";
                                $animal_class = "";
                                
                                if ($campo["nome_animal"]) {
                                    $animal_info = $campo["nome_animal"] . " (" . $campo["tipo_animal"] . ")";
                                    $animal_class = "status-ativo";
                                } else {
                                    // Verificar se o morador marcou que possui animal
                                    if ($campo["animais"] == "Possui" || $campo["animais"] == "sim") {
                                        $animal_info = "Possui - Não cadastrado";
                                        $animal_class = "status-warning";
                                    } else {
                                        $animal_info = "Não possui";
                                        $animal_class = "status-inactive";
                                    }
                                }
                                
                                echo "<tr>";
                                echo "<td>" . $campo["id_moradores"] . "</td>";
                                echo "<td>";
                                echo "</td>";
                                echo "<td>" . $campo["nome"] . "</td>";
                                echo "<td>" . $campo["cpf"] . "</td>";
                                echo "<td>" . $campo["telefone"] . "</td>";
                                echo "<td>" . ($campo["email"] ? $campo["email"] : "Não informado") . "</td>";
                                echo "<td>" . $campo["bloco"] . "/" . $campo["torre"] . "</td>";
                                echo "<td>" . $campo["andar"] . "</td>";
                                echo "<td><span class='$veiculo_class'>" . $veiculo_info . "</span></td>";
                                echo "<td><span class='$animal_class'>" . $animal_info . "</span></td>";
                                echo "<td><span class='status-ativo'>" . ($campo["status"] ? $campo["status"] : "Ativo") . "</span></td>";
                                echo "<td class='acoes'>";
                                echo "<a href='editar_morador.php?id=" . $campo["id_moradores"] . "' class='btn-editar'>";
                                echo "<i class='fas fa-edit'></i> Editar</a>";
                                echo "<a href='excluir_morador.php?id=" . $campo["id_moradores"] . "' class='btn-excluir' onclick='return confirm(\"Tem certeza que deseja excluir este morador?\")'>";
                                echo "<i class='fas fa-trash'></i> Excluir</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12' style='text-align: center;'>Nenhum morador cadastrado</td></tr>";
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
        // Dados dos moradores para filtro em JavaScript
        const moradores = [
            <?php
            $selecionar_js = mysqli_query($conn, "SELECT m.*, a.nome as nome_animal, a.tipo as tipo_animal 
                                                 FROM tb_moradores m 
                                                 LEFT JOIN tb_animais a ON m.id_moradores = a.id_morador 
                                                 ORDER BY m.nome");
            $moradores_js = [];
            while ($campo = mysqli_fetch_array($selecionar_js)) {
                // Buscar veículos para JavaScript
                $veiculos_query = mysqli_query($conn, "SELECT placa, modelo FROM tb_veiculos WHERE id_morador = " . $campo["id_moradores"]);
                $veiculo_info = "Não possui";
                if (mysqli_num_rows($veiculos_query) > 0) {
                    $veiculos = [];
                    while ($veiculo = mysqli_fetch_array($veiculos_query)) {
                        $veiculos[] = $veiculo["modelo"] . " (" . $veiculo["placa"] . ")";
                    }
                    $veiculo_info = implode(", ", $veiculos);
                }
                
                $animal_info = $campo["nome_animal"] ? $campo["nome_animal"] . " (" . $campo["tipo_animal"] . ")" : "Não possui";
                $moradores_js[] = "{
                    id: " . $campo["id_moradores"] . ",
                    nome: '" . addslashes($campo["nome"]) . "',
                    cpf: '" . addslashes($campo["cpf"]) . "',
                    telefone: '" . addslashes($campo["telefone"]) . "',
                    email: '" . addslashes($campo["email"] ? $campo["email"] : "Não informado") . "',
                    bloco: '" . addslashes($campo["bloco"]) . "',
                    torre: '" . addslashes($campo["torre"]) . "',
                    andar: '" . addslashes($campo["andar"]) . "',
                    veiculo: '" . addslashes($veiculo_info) . "',
                    veiculo_class: '" . addslashes($veiculo_class) . "',
                    animais: '" . addslashes($animal_info) . "',
                    animal_class: '" . addslashes($animal_class) . "',
                    status: '" . addslashes($campo["status"] ? $campo["status"] : "Ativo") . "'
                }";
            }
            echo implode(",\n            ", $moradores_js);
            ?>
        ];

        function filtrarMoradores() {
            const pesquisa = document.getElementById('pesquisa').value.toLowerCase();
            const filtroBloco = document.getElementById('filtro_bloco').value.toLowerCase();
            const tbody = document.getElementById('tabela-moradores');
            const resultadoPesquisa = document.getElementById('resultado-pesquisa');
            
            let moradoresFiltrados = moradores.filter(morador => {
                const nomeMatch = morador.nome.toLowerCase().includes(pesquisa);
                const blocoMatch = filtroBloco === '' || morador.bloco.toLowerCase() === filtroBloco;
                return nomeMatch && blocoMatch;
            });
            
            // Atualizar resultado da pesquisa
            if (pesquisa || filtroBloco) {
                resultadoPesquisa.style.color = '#3498db';
                resultadoPesquisa.textContent = `Encontrados ${moradoresFiltrados.length} morador(es)`;
            } else {
                resultadoPesquisa.textContent = '';
            }
            
            // Limpar tabela
            tbody.innerHTML = '';
            
            if (moradoresFiltrados.length === 0) {
                tbody.innerHTML = '<tr><td colspan="11" style="text-align: center;">Nenhum morador encontrado</td></tr>';
                return;
            }
            
            // Preencher tabela com resultados filtrados
            moradoresFiltrados.forEach(morador => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${morador.id}</td>
                    <td>${destacarTexto(morador.nome, pesquisa)}</td>
                    <td>${morador.cpf}</td>
                    <td>${morador.telefone}</td>
                    <td>${morador.email}</td>
                    <td>${morador.bloco}/${morador.torre}</td>
                    <td>${morador.andar}</td>
                    <td><span class='${morador.veiculo_class}'>${morador.veiculo}</span></td>
                    <td><span class='${morador.animal_class}'>${morador.animais}</span></td>
                    <td><span class='status-ativo'>${morador.status}</span></td>
                    <td class='acoes'>
                        <a href='editar_morador.php?id=${morador.id}' class='btn-editar'>
                            <i class='fas fa-edit'></i> Editar
                        </a>
                        <a href='excluir_morador.php?id=${morador.id}' class='btn-excluir' onclick='return confirm("Tem certeza que deseja excluir este morador?")'>
                            <i class='fas fa-trash'></i> Excluir
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
        
        function limparFiltros() {
            document.getElementById('pesquisa').value = '';
            document.getElementById('filtro_bloco').value = '';
            filtrarMoradores();
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