class Funcionario {
    constructor(nome, cpf, cargo, dataAdmissao, telefone, email) {
        this.id = Date.now();
        this.nome = nome;
        this.cpf = cpf;
        this.cargo = cargo;
        this.dataAdmissao = dataAdmissao;
        this.telefone = telefone;
        this.email = email;
    }
}

class Cargo {
    constructor(nome, descricao, salario, cargaHoraria) {
        this.id = Date.now();
        this.nome = nome;
        this.descricao = descricao;
        this.salario = salario;
        this.cargaHoraria = cargaHoraria;
    }
}

// Gerenciamento de funcionários
const gerenciadorFuncionarios = {
    funcionarios: [],

    init() {
        this.funcionarios = JSON.parse(localStorage.getItem('funcionarios')) || [];
        this.atualizarLista();
        this.configurarFormulario();
    },

    adicionar(funcionario) {
        // Validar dados do funcionário
        if (!this.validarDados(funcionario)) {
            alert('Por favor, preencha todos os campos corretamente.');
            return false;
        }

        // Verificar se CPF já existe
        if (this.funcionarios.some(f => f.cpf === funcionario.cpf && f.id !== funcionario.id)) {
            alert('CPF já cadastrado.');
            return false;
        }

        this.funcionarios.push(funcionario);
        this.salvar();
        this.atualizarLista();
        return true;
    },

    editar(id, dadosAtualizados) {
        // Validar dados atualizados
        if (!this.validarDados(dadosAtualizados)) {
            alert('Por favor, preencha todos os campos corretamente.');
            return false;
        }

        // Verificar se CPF já existe em outro funcionário
        if (this.funcionarios.some(f => f.cpf === dadosAtualizados.cpf && f.id !== id)) {
            alert('CPF já cadastrado em outro funcionário.');
            return false;
        }

        const index = this.funcionarios.findIndex(f => f.id === id);
        if (index !== -1) {
            this.funcionarios[index] = { ...this.funcionarios[index], ...dadosAtualizados };
            this.salvar();
            this.atualizarLista();
            return true;
        }
        return false;
    },

    validarDados(funcionario) {
        // Validar campos obrigatórios
        if (!funcionario.nome || !funcionario.cpf || !funcionario.cargo || 
            !funcionario.dataAdmissao || !funcionario.telefone || !funcionario.email) {
            return false;
        }

        // Validar formato do CPF (XXX.XXX.XXX-XX)
        const cpfRegex = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
        if (!cpfRegex.test(funcionario.cpf)) {
            return false;
        }

        // Validar formato do email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(funcionario.email)) {
            return false;
        }

        // Validar formato do telefone ((XX) XXXXX-XXXX)
        const telefoneRegex = /^\(\d{2}\)\s\d{5}-\d{4}$/;
        if (!telefoneRegex.test(funcionario.telefone)) {
            return false;
        }

        return true;
    },

    remover(id) {
        this.funcionarios = this.funcionarios.filter(f => f.id !== id);
        this.salvar();
        this.atualizarLista();
    },

    salvar() {
        localStorage.setItem('funcionarios', JSON.stringify(this.funcionarios));
    },

    atualizarLista() {
        const lista = document.getElementById('lista-funcionarios');
        lista.innerHTML = '';
        
        // Adicionar classe para o grid de cards
        lista.className = 'cards-funcionarios';
        
        if (this.funcionarios.length === 0) {
            const semRegistros = document.createElement('div');
            semRegistros.className = 'sem-registros';
            semRegistros.innerHTML = `
                <i class="fas fa-user-slash"></i>
                <p>Nenhum funcionário cadastrado</p>
            `;
            lista.appendChild(semRegistros);
            return;
        }

        this.funcionarios.forEach(funcionario => {
            const div = document.createElement('div');
            div.className = 'card-funcionario';
            div.innerHTML = `
                <div class="card-header">
                    <i class="fas fa-user-tie"></i>
                    <h4>${funcionario.nome}</h4>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-id-badge"></i> <strong>CPF:</strong> ${funcionario.cpf}</p>
                    <p><i class="fas fa-briefcase"></i> <strong>Cargo:</strong> ${funcionario.cargo}</p>
                    <p><i class="fas fa-calendar-alt"></i> <strong>Admissão:</strong> ${new Date(funcionario.dataAdmissao).toLocaleDateString()}</p>
                    <p><i class="fas fa-phone"></i> <strong>Telefone:</strong> ${funcionario.telefone}</p>
                    <p><i class="fas fa-envelope"></i> <strong>Email:</strong> ${funcionario.email}</p>
                </div>
                <div class="card-footer">
                    <button class="btn-editar" onclick="gerenciadorFuncionarios.carregarParaEdicao(${funcionario.id})"><i class="fas fa-edit"></i> Editar</button>
                    <button class="btn-remover" onclick="gerenciadorFuncionarios.remover(${funcionario.id})"><i class="fas fa-trash"></i> Remover</button>
                </div>
            `;
            lista.appendChild(div);
        });
    },

    configurarFormulario() {
        const form = document.getElementById('form-funcionario');
        const btnSubmit = form.querySelector('button[type="submit"]');
        let funcionarioEmEdicao = null;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const dados = {
                nome: form.nome.value,
                cpf: form.cpf.value,
                cargo: form.cargo.value,
                dataAdmissao: form['data-admissao'].value,
                telefone: form.telefone.value,
                email: form.email.value
            };

            if (funcionarioEmEdicao) {
                if (this.editar(funcionarioEmEdicao, dados)) {
                    funcionarioEmEdicao = null;
                    btnSubmit.textContent = 'Cadastrar Funcionário';
                    form.reset();
                }
            } else {
                const funcionario = new Funcionario(
                    dados.nome,
                    dados.cpf,
                    dados.cargo,
                    dados.dataAdmissao,
                    dados.telefone,
                    dados.email
                );
                if (this.adicionar(funcionario)) {
                    form.reset();
                }
            }
        });

        // Máscara para CPF
        const cpfInput = form.querySelector('#cpf');
        cpfInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
                e.target.value = value;
            }
        });

        // Máscara para telefone
        const telefoneInput = form.querySelector('#telefone');
        telefoneInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                e.target.value = value;
            }
        });

        // Configurar botão de limpar
        const btnLimpar = form.querySelector('button[type="reset"]');
        btnLimpar.addEventListener('click', () => {
            funcionarioEmEdicao = null;
            btnSubmit.textContent = 'Cadastrar Funcionário';
        });

        // Método para carregar dados para edição
        this.carregarParaEdicao = (id) => {
            const funcionario = this.funcionarios.find(f => f.id === id);
            if (funcionario) {
                funcionarioEmEdicao = funcionario.id;
                form.nome.value = funcionario.nome;
                form.cpf.value = funcionario.cpf;
                form.cargo.value = funcionario.cargo;
                form['data-admissao'].value = funcionario.dataAdmissao;
                form.telefone.value = funcionario.telefone;
                form.email.value = funcionario.email;
                btnSubmit.textContent = 'Salvar Alterações';
                form.scrollIntoView({ behavior: 'smooth' });
            }
        };
    }
};

// Gerenciamento de cargos
const gerenciadorCargos = {
    cargos: [],

    init() {
        this.cargos = JSON.parse(localStorage.getItem('cargos')) || [];
        this.atualizarTabela();
        this.configurarFormulario();
        this.atualizarSelectCargos();
    },

    adicionar(cargo) {
        this.cargos.push(cargo);
        this.salvar();
        this.atualizarTabela();
        this.atualizarSelectCargos();
    },

    remover(id) {
        this.cargos = this.cargos.filter(c => c.id !== id);
        this.salvar();
        this.atualizarTabela();
        this.atualizarSelectCargos();
    },

    salvar() {
        localStorage.setItem('cargos', JSON.stringify(this.cargos));
    },

    atualizarTabela() {
        const tbody = document.querySelector('#tabela-cargos tbody');
        tbody.innerHTML = '';

        this.cargos.forEach(cargo => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${cargo.nome}</td>
                <td>${cargo.descricao}</td>
                <td>R$ ${parseFloat(cargo.salario).toFixed(2)}</td>
                <td>${cargo.cargaHoraria}h</td>
                <td>
                    <button class="btn-remove" data-id="${cargo.id}">Remover</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Adicionar event listeners para os botões de remover
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseInt(btn.getAttribute('data-id'));
                this.remover(id);
            });
        });
    },

    atualizarSelectCargos() {
        const select = document.getElementById('cargo');
        // Manter apenas a primeira opção (placeholder)
        select.innerHTML = '<option value="">Selecione um cargo</option>';
        
        this.cargos.forEach(cargo => {
            const option = document.createElement('option');
            option.value = cargo.nome;
            option.textContent = cargo.nome;
            select.appendChild(option);
        });
    },

    configurarFormulario() {
        const form = document.getElementById('form-cargo');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const cargo = new Cargo(
                form['nome-cargo'].value,
                form.descricao.value,
                parseFloat(form.salario.value),
                parseInt(form['carga-horaria'].value)
            );
            this.adicionar(cargo);
            form.reset();
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    gerenciadorFuncionarios.init();
    gerenciadorCargos.init();
});







class Morador {
    constructor(nome, cpf, residencia, telefone, email) {
        this.id = Date.now();
        this.nome = nome;
        this.cpf = cpf;
        this.residencia = residencia;
        this.telefone = telefone;
        this.email = email;
    }
}

// Gerenciamento de moradores
const gerenciadorMoradores = {
    moradores: [],

    init() {
        this.moradores = JSON.parse(localStorage.getItem('moradores')) || [];
        this.atualizarLista();
        this.configurarFormulario();
        this.atualizarContadores();
    },

    adicionar(morador) {
        this.moradores.push(morador);
        this.salvar();
        this.atualizarLista();
        this.atualizarContadores();
    },

    remover(id) {
        this.moradores = this.moradores.filter(m => m.id !== id);
        this.salvar();
        this.atualizarLista();
        this.atualizarContadores();
    },

    salvar() {
        localStorage.setItem('moradores', JSON.stringify(this.moradores));
    },

    atualizarLista() {
        const lista = document.getElementById('lista-moradores');
        lista.innerHTML = '';

        this.moradores.forEach(morador => {
            const div = document.createElement('div');
            div.className = 'lista-item';
            div.innerHTML = `
                <h4>${morador.nome}</h4>
                <p>Residência: ${morador.residencia}</p>
                <p>Telefone: ${morador.telefone}</p>
                <button onclick="gerenciadorMoradores.remover(${morador.id})">Remover</button>
            `;
            lista.appendChild(div);
        });
    },

    configurarFormulario() {
        const form = document.getElementById('form-morador');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const morador = new Morador(
                form.nome.value,
                form.cpf.value,
                form.residencia.value,
                form.telefone.value,
                form.email.value
            );
            this.adicionar(morador);
            form.reset();
        });
    },

    atualizarContadores() {
        // Atualiza o total de residências
        const totalResidencias = new Set(this.moradores.map(m => m.residencia)).size;
        localStorage.setItem('totalResidencias', totalResidencias);

        // Atualiza o contador de moradores presentes (assumindo que todos estão presentes por padrão)
        localStorage.setItem('moradoresPresentes', this.moradores.length);
    }
};

document.addEventListener('DOMContentLoaded', () => gerenciadorMoradores.init());