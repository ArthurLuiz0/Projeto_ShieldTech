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
        this.funcionarios.push(funcionario);
        this.salvar();
        this.atualizarLista();
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
                    <button class="btn-editar"><i class="fas fa-edit"></i> Editar</button>
                    <button class="btn-remover" onclick="gerenciadorFuncionarios.remover(${funcionario.id})"><i class="fas fa-trash"></i> Remover</button>
                </div>
            `;
            lista.appendChild(div);
        });
    },

    configurarFormulario() {
        const form = document.getElementById('form-funcionario');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const funcionario = new Funcionario(
                form.nome.value,
                form.cpf.value,
                form.cargo.value,
                form['data-admissao'].value,
                form.telefone.value,
                form.email.value
            );
            this.adicionar(funcionario);
            form.reset();
        });
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




