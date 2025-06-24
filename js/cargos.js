class Cargo {
    constructor(nome, descricao, salario, cargaHoraria) {
        this.id = Date.now();
        this.nome = nome;
        this.descricao = descricao;
        this.salario = salario;
        this.cargaHoraria = cargaHoraria;
    }
}

const gerenciadorCargos = {
    cargos: [],

    init() {
        this.cargos = JSON.parse(localStorage.getItem('cargos')) || [];
        this.atualizarLista();
        this.configurarFormulario();
    },

    adicionar(cargo) {
        if (!this.validarDados(cargo)) {
            alert('Por favor, preencha todos os campos corretamente.');
            return false;
        }

        if (this.cargos.some(c => c.nome.toLowerCase() === cargo.nome.toLowerCase())) {
            alert('Já existe um cargo com este nome.');
            return false;
        }

        this.cargos.push(cargo);
        this.salvar();
        this.atualizarLista();
        return true;
    },

    editar(id, dadosAtualizados) {
        if (!this.validarDados(dadosAtualizados)) {
            alert('Por favor, preencha todos os campos corretamente.');
            return false;
        }

        if (this.cargos.some(c => c.nome.toLowerCase() === dadosAtualizados.nome.toLowerCase() && c.id !== id)) {
            alert('Já existe um cargo com este nome.');
            return false;
        }

        const index = this.cargos.findIndex(c => c.id === id);
        if (index !== -1) {
            this.cargos[index] = { ...this.cargos[index], ...dadosAtualizados };
            this.salvar();
            this.atualizarLista();
            return true;
        }
        return false;
    },

    remover(id) {
        if (confirm('Tem certeza que deseja excluir este cargo?')) {
            this.cargos = this.cargos.filter(c => c.id !== id);
            this.salvar();
            this.atualizarLista();
        }
    },

    salvar() {
        localStorage.setItem('cargos', JSON.stringify(this.cargos));
    },

    validarDados(cargo) {
        return cargo.nome && 
               cargo.descricao && 
               cargo.salario > 0 && 
               cargo.cargaHoraria > 0 && 
               cargo.cargaHoraria <= 44;
    },

    atualizarLista() {
        const tbody = document.querySelector('#tabela-cargos tbody');
        if (!tbody) return;

        tbody.innerHTML = '';
        this.cargos.forEach(cargo => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${cargo.nome}</td>
                <td>${cargo.descricao}</td>
                <td>R$ ${cargo.salario.toFixed(2)}</td>
                <td>${cargo.cargaHoraria}h</td>
                <td>
                    <button onclick="gerenciadorCargos.carregarParaEdicao('${cargo.id}')" class="btn-editar">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button onclick="gerenciadorCargos.remover('${cargo.id}')" class="btn-excluir">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Atualizar select de cargos no formulário de funcionários
        const selectCargo = document.getElementById('cargo');
        if (selectCargo) {
            const cargosAtuais = Array.from(selectCargo.options).map(opt => opt.value);
            this.cargos.forEach(cargo => {
                if (!cargosAtuais.includes(cargo.nome)) {
                    const option = document.createElement('option');
                    option.value = cargo.nome;
                    option.textContent = cargo.nome;
                    selectCargo.appendChild(option);
                }
            });
        }
    },

    configurarFormulario() {
        const form = document.getElementById('form-cargo');
        const btnSubmit = form.querySelector('button[type="submit"]');
        let cargoEmEdicao = null;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const dados = {
                nome: form['nome-cargo'].value,
                descricao: form.descricao.value,
                salario: parseFloat(form.salario.value),
                cargaHoraria: parseInt(form['carga-horaria'].value)
            };

            if (cargoEmEdicao) {
                if (this.editar(cargoEmEdicao, dados)) {
                    cargoEmEdicao = null;
                    btnSubmit.textContent = 'Cadastrar Cargo';
                    form.reset();
                }
            } else {
                const cargo = new Cargo(
                    dados.nome,
                    dados.descricao,
                    dados.salario,
                    dados.cargaHoraria
                );
                if (this.adicionar(cargo)) {
                    form.reset();
                }
            }
        });

        const btnLimpar = form.querySelector('button[type="reset"]');
        btnLimpar.addEventListener('click', () => {
            cargoEmEdicao = null;
            btnSubmit.textContent = 'Cadastrar Cargo';
        });

        this.carregarParaEdicao = (id) => {
            const cargo = this.cargos.find(c => c.id === id);
            if (cargo) {
                cargoEmEdicao = cargo.id;
                form['nome-cargo'].value = cargo.nome;
                form.descricao.value = cargo.descricao;
                form.salario.value = cargo.salario;
                form['carga-horaria'].value = cargo.cargaHoraria;
                btnSubmit.textContent = 'Salvar Alterações';
                form.scrollIntoView({ behavior: 'smooth' });
            }
        };
    }
};

// Inicializar o gerenciador quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    gerenciadorCargos.init();
});