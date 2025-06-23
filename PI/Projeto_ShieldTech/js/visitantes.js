class Visitante {
    constructor(nome, documento, moradorVisitado, motivo) {
        this.id = Date.now();
        this.nome = nome;
        this.documento = documento;
        this.moradorVisitado = moradorVisitado;
        this.motivo = motivo;
        this.entrada = new Date();
        this.saida = null;
    }
}

const gerenciadorVisitantes = {
    visitantes: [],

    init() {
        this.visitantes = JSON.parse(localStorage.getItem('visitantes')) || [];
        this.atualizarLista();
        this.configurarFormulario();
        this.carregarMoradores();
        this.atualizarContadores();
    },

    adicionar(visitante) {
        this.visitantes.push(visitante);
        this.salvar();
        this.atualizarLista();
        this.atualizarContadores();
    },

    registrarSaida(id) {
        const visitante = this.visitantes.find(v => v.id === id);
        if (visitante) {
            visitante.saida = new Date();
            this.salvar();
            this.atualizarLista();
            this.atualizarContadores();
        }
    },

    salvar() {
        localStorage.setItem('visitantes', JSON.stringify(this.visitantes));
    },

    atualizarLista() {
        const lista = document.getElementById('lista-visitantes');
        lista.innerHTML = '';

        this.visitantes
            .filter(v => !v.saida)
            .forEach(visitante => {
                const div = document.createElement('div');
                div.className = 'lista-item';
                div.innerHTML = `
                    <h4>${visitante.nome}</h4>
                    <p>Visitando: ${visitante.moradorVisitado}</p>
                    <p>Entrada: ${new Date(visitante.entrada).toLocaleString()}</p>
                    <button onclick="gerenciadorVisitantes.registrarSaida(${visitante.id})">
                        Registrar Saída
                    </button>
                `;
                lista.appendChild(div);
            });
    },

    carregarMoradores() {
        const moradores = JSON.parse(localStorage.getItem('moradores')) || [];
        const select = document.getElementById('morador-visita');
        
        moradores.forEach(morador => {
            const option = document.createElement('option');
            option.value = morador.nome;
            option.textContent = `${morador.nome} - Residência ${morador.residencia}`;
            select.appendChild(option);
        });
    },

    configurarFormulario() {
        const form = document.getElementById('form-visitante');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const visitante = new Visitante(
                form['nome-visitante'].value,
                form.documento.value,
                form['morador-visita'].value,
                form.motivo.value
            );
            this.adicionar(visitante);
            form.reset();
        });
    },

    atualizarContadores() {
        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);

        // Conta visitantes de hoje
        const visitantesHoje = this.visitantes.filter(v => {
            const dataEntrada = new Date(v.entrada);
            dataEntrada.setHours(0, 0, 0, 0);
            return dataEntrada.getTime() === hoje.getTime();
        }).length;

        localStorage.setItem('visitantesHoje', visitantesHoje);
    }
};

document.addEventListener('DOMContentLoaded', () => gerenciadorVisitantes.init()); 