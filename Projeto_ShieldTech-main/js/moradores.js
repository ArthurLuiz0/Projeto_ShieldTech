// Gerenciador de Moradores
class MoradoresManager {
    constructor() {
        this.moradores = DataManager.getData('moradores') || [];
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Form de cadastro
        const form = document.getElementById('moradorForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        // Botões de ação
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="editar"]')) {
                this.handleEdit(e.target.dataset.id);
            }
            if (e.target.matches('[data-action="excluir"]')) {
                this.handleDelete(e.target.dataset.id);
            }
        });
    }

    async handleSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const morador = Object.fromEntries(formData.entries());

        try {
            if (morador.id) {
                await this.atualizarMorador(morador);
            } else {
                await this.adicionarMorador(morador);
            }
            form.reset();
            this.atualizarLista();
            showNotification('Morador salvo com sucesso!', 'success');
        } catch (error) {
            showNotification('Erro ao salvar morador.', 'error');
        }
    }

    async adicionarMorador(morador) {
        morador.id = Date.now().toString();
        morador.dataCadastro = new Date().toISOString();
        this.moradores.push(morador);
        await this.salvarDados();
    }

    async atualizarMorador(moradorAtualizado) {
        const index = this.moradores.findIndex(m => m.id === moradorAtualizado.id);
        if (index !== -1) {
            this.moradores[index] = { ...this.moradores[index], ...moradorAtualizado };
            await this.salvarDados();
        }
    }

    async handleDelete(id) {
        if (confirm('Tem certeza que deseja excluir este morador?')) {
            try {
                await this.excluirMorador(id);
                this.atualizarLista();
                showNotification('Morador excluído com sucesso!', 'success');
            } catch (error) {
                showNotification('Erro ao excluir morador.', 'error');
            }
        }
    }

    async excluirMorador(id) {
        this.moradores = this.moradores.filter(m => m.id !== id);
        await this.salvarDados();
    }

    handleEdit(id) {
        const morador = this.moradores.find(m => m.id === id);
        if (morador) {
            const form = document.getElementById('moradorForm');
            Object.keys(morador).forEach(key => {
                const input = form.elements[key];
                if (input) input.value = morador[key];
            });
        }
    }

    async salvarDados() {
        return DataManager.saveData('moradores', this.moradores);
    }

    atualizarLista() {
        const tbody = document.querySelector('#listaMoradores tbody');
        if (!tbody) return;

        tbody.innerHTML = this.moradores.map(morador => `
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
