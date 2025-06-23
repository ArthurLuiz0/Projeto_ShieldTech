class Morador {
    constructor(nome, cpf, rg, telefone, email, residencia, animal = null) {
        this.id = Date.now();
        this.nome = nome;
        this.cpf = cpf;
        this.rg = rg;
        this.telefone = telefone;
        this.email = email;
        this.residencia = residencia;
        this.animal = animal;
    }
}

class Animal {
    constructor(nome, especie, raca, idade) {
        this.nome = nome;
        this.especie = especie;
        this.raca = raca;
        this.idade = idade;
    }
}

const gerenciadorMoradores = {
    moradores: [],

    init() {
        this.moradores = JSON.parse(localStorage.getItem('moradores')) || [];
        this.atualizarLista();
        this.configurarFormulario();
    },

    adicionar(morador) {
        if (!this.validarDados(morador)) {
            alert('Por favor, preencha todos os campos do morador corretamente.');
            return false;
        }

        if (this.moradores.some(m => m.cpf === morador.cpf)) {
            alert('CPF já cadastrado.');
            return false;
        }

        this.moradores.push(morador);
        this.salvar();
        this.atualizarLista();
        return true;
    },

    editar(id, dadosAtualizados) {
        if (!this.validarDados(dadosAtualizados)) {
            alert('Por favor, preencha todos os campos do morador corretamente.');
            return false;
        }

        if (this.moradores.some(m => m.cpf === dadosAtualizados.cpf && m.id !== id)) {
            alert('CPF já cadastrado em outro morador.');
            return false;
        }

        const index = this.moradores.findIndex(m => m.id === id);
        if (index !== -1) {
            this.moradores[index] = { ...this.moradores[index], ...dadosAtualizados };
            this.salvar();
            this.atualizarLista();
            return true;
        }
        return false;
    },
    }

    remover(id) {
        if (confirm('Tem certeza que deseja excluir este morador?')) {
            this.moradores = this.moradores.filter(m => m.id !== id);
            this.salvar();
            this.atualizarLista();
        }
    },

    salvar() {
        localStorage.setItem('moradores', JSON.stringify(this.moradores));
    },

    validarDados(morador) {
        // Validar campos obrigatórios
        if (!morador.nome || !morador.cpf || !morador.rg || 
            !morador.telefone || !morador.email || !morador.residencia) {
            return false;
        }

        // Validar formato do CPF (XXX.XXX.XXX-XX)
        const cpfRegex = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
        if (!cpfRegex.test(morador.cpf)) {
            return false;
        }

        // Validar formato do email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(morador.email)) {
            return false;
        }

        // Validar formato do telefone ((XX) XXXXX-XXXX)
        const telefoneRegex = /^\(\d{2}\)\s\d{5}-\d{4}$/;
        if (!telefoneRegex.test(morador.telefone)) {
            return false;
        }

        return true;
    },
    }

    atualizarLista() {
        const lista = document.getElementById('lista-moradores');
        if (!lista) return;

        lista.innerHTML = '';
        this.moradores.forEach(morador => {
            const div = document.createElement('div');
            div.classList.add('morador-item');
            div.innerHTML = `
                <div class="morador-info">
                    <h3>${morador.nome}</h3>
                    <p><strong>CPF:</strong> ${morador.cpf}</p>
                    <p><strong>RG:</strong> ${morador.rg}</p>
                    <p><strong>Telefone:</strong> ${morador.telefone}</p>
                    <p><strong>Email:</strong> ${morador.email}</p>
                    <p><strong>Residência:</strong> ${morador.residencia}</p>
                </div>
                ${morador.animal ? `
                <div class="animal-info">
                    <h4>Animal de Estimação</h4>
                    <p><strong>Nome:</strong> ${morador.animal.nome}</p>
                    <p><strong>Espécie:</strong> ${morador.animal.especie}</p>
                    <p><strong>Raça:</strong> ${morador.animal.raca}</p>
                    <p><strong>Idade:</strong> ${morador.animal.idade} anos</p>
                </div>
                ` : ''}
                <div class="botoes-acao">
                    <button onclick="gerenciadorMoradores.carregarParaEdicao('${morador.id}')" class="btn-editar">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button onclick="gerenciadorMoradores.remover('${morador.id}')" class="btn-excluir">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                </div>
            `;
            lista.appendChild(div);
        });
    },

    configurarFormulario() {
        const formMorador = document.getElementById('form-morador');
        const formAnimal = document.getElementById('form-animal');
        const btnSubmit = document.querySelector('button[type="submit"]');
        let moradorEmEdicao = null;

        // Configurar máscaras
        const cpfInput = formMorador.querySelector('#cpf');
        cpfInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
                e.target.value = value;
            }
        });

        const telefoneInput = formMorador.querySelector('#telefone');
        telefoneInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                e.target.value = value;
            }
        });

        // Configurar envio do formulário
        formMorador.addEventListener('submit', (e) => {
            e.preventDefault();

            const dadosMorador = {
                nome: formMorador.nome.value,
                cpf: formMorador.cpf.value,
                rg: formMorador.rg.value,
                telefone: formMorador.telefone.value,
                email: formMorador.email.value,
                residencia: formMorador.residencia.value
            };

            // Verificar se há dados do animal
            const nomeAnimal = formAnimal['nome-animal'].value;
            if (nomeAnimal) {
                dadosMorador.animal = new Animal(
                    nomeAnimal,
                    formAnimal.especie.value,
                    formAnimal.raca.value,
                    parseInt(formAnimal['idade-animal'].value) || 0
                );
            }

            if (moradorEmEdicao) {
                if (this.editar(moradorEmEdicao, dadosMorador)) {
                    moradorEmEdicao = null;
                    btnSubmit.textContent = 'Cadastrar Morador';
                    formMorador.reset();
                    formAnimal.reset();
                }
            } else {
                const morador = new Morador(
                    dadosMorador.nome,
                    dadosMorador.cpf,
                    dadosMorador.rg,
                    dadosMorador.telefone,
                    dadosMorador.email,
                    dadosMorador.residencia,
                    dadosMorador.animal
                );
                if (this.adicionar(morador)) {
                    formMorador.reset();
                    formAnimal.reset();
                }
            }
        });

        // Configurar botão de limpar
        const btnLimpar = formMorador.querySelector('button[type="reset"]');
        if (btnLimpar) {
            btnLimpar.addEventListener('click', () => {
                moradorEmEdicao = null;
                btnSubmit.textContent = 'Cadastrar Morador';
                formAnimal.reset();
            });
        }

        // Método para carregar dados para edição
        this.carregarParaEdicao = (id) => {
            const morador = this.moradores.find(m => m.id === id);
            if (morador) {
                moradorEmEdicao = morador.id;
                formMorador.nome.value = morador.nome;
                formMorador.cpf.value = morador.cpf;
                formMorador.rg.value = morador.rg;
                formMorador.telefone.value = morador.telefone;
                formMorador.email.value = morador.email;
                formMorador.residencia.value = morador.residencia;

                if (morador.animal) {
                    formAnimal['nome-animal'].value = morador.animal.nome;
                    formAnimal.especie.value = morador.animal.especie;
                    formAnimal.raca.value = morador.animal.raca;
                    formAnimal['idade-animal'].value = morador.animal.idade;
                } else {
                    formAnimal.reset();
                }

                btnSubmit.textContent = 'Salvar Alterações';
                formMorador.scrollIntoView({ behavior: 'smooth' });
            }
        };
    }
};

// Inicializar o gerenciador quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    gerenciadorMoradores.init();
});
                <td>${morador.telefone}</td>
                <td>${morador.email}</td>
                <td>
                    <button class="btn-action" data-action="editar" data-id="${morador.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action" data-action="excluir" data-id="${morador.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Métodos de busca e filtro
    buscarMorador(termo) {
        return this.moradores.filter(morador => 
            Object.values(morador).some(valor => 
                String(valor).toLowerCase().includes(termo.toLowerCase())
            )
        );
    }

    filtrarPorApartamento(apartamento) {
        return this.moradores.filter(morador => 
            morador.apartamento === apartamento
        );
    }

    // Métodos de validação
    validarMorador(morador) {
        const erros = [];

        if (!morador.nome) erros.push('Nome é obrigatório');
        if (!morador.apartamento) erros.push('Apartamento é obrigatório');
        if (!morador.telefone) erros.push('Telefone é obrigatório');
        if (morador.email && !this.validarEmail(morador.email)) {
            erros.push('Email inválido');
        }

        return erros;
    }

    validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    const moradoresManager = new MoradoresManager();
    moradoresManager.atualizarLista();

    // Adicionar listener para busca
    const searchInput = document.getElementById('searchMorador');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const resultados = moradoresManager.buscarMorador(e.target.value);
            // Atualizar a lista com os resultados da busca
            const tbody = document.querySelector('#listaMoradores tbody');
            if (tbody) {
                tbody.innerHTML = resultados.map(morador => `
                    <tr>
                        <td>${morador.nome}</td>
                        <td>${morador.apartamento}</td>
                        <td>${morador.telefone}</td>
                        <td>${morador.email}</td>
                        <td>
                            <button class="btn-action" data-action="editar" data-id="${morador.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action" data-action="excluir" data-id="${morador.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
        });
    }
});
