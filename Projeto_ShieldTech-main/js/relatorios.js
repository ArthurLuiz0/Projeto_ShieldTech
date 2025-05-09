const gerenciadorRelatorios = {
    dadosFiltrados: [], // Armazena os dados do relatório atual

    init() {
        this.configurarFormulario();
        this.configurarFiltrosAvancados();
        this.configurarPeriodo();
    },

    configurarFiltrosAvancados() {
        const btn = document.getElementById('btn-filtros-avancados');
        const filtros = document.querySelector('.filtros-avancados');
        
        btn.addEventListener('click', () => {
            filtros.classList.toggle('ativo');
            btn.textContent = filtros.classList.contains('ativo') ? 
                'Ocultar Filtros Avançados' : 'Filtros Avançados';
        });
    },

    configurarPeriodo() {
        const periodo = document.getElementById('periodo');
        const datasPersonalizadas = document.getElementById('datas-personalizadas');
        
        periodo.addEventListener('change', (e) => {
            if (e.target.value === 'personalizado') {
                datasPersonalizadas.style.display = 'block';
            } else {
                datasPersonalizadas.style.display = 'none';
                this.definirDatasPeriodo(e.target.value);
            }
        });
    },

    definirDatasPeriodo(periodo) {
        const hoje = new Date();
        const dataInicio = document.getElementById('data-inicio');
        const dataFim = document.getElementById('data-fim');

        switch(periodo) {
            case 'hoje':
                dataInicio.value = this.formatarData(hoje);
                dataFim.value = this.formatarData(hoje);
                break;
            case 'ontem':
                const ontem = new Date(hoje);
                ontem.setDate(ontem.getDate() - 1);
                dataInicio.value = this.formatarData(ontem);
                dataFim.value = this.formatarData(ontem);
                break;
            case 'semana':
                const semanaPassada = new Date(hoje);
                semanaPassada.setDate(semanaPassada.getDate() - 7);
                dataInicio.value = this.formatarData(semanaPassada);
                dataFim.value = this.formatarData(hoje);
                break;
            case 'mes':
                const mesPassado = new Date(hoje);
                mesPassado.setMonth(mesPassado.getMonth() - 1);
                dataInicio.value = this.formatarData(mesPassado);
                dataFim.value = this.formatarData(hoje);
                break;
        }
    },

    formatarData(data) {
        return data.toISOString().split('T')[0];
    },

    gerarRelatorio(tipo, dataInicio, dataFim, filtros = {}) {
        const dados = this.obterDados(tipo);
        this.dadosFiltrados = this.filtrarDados(dados, dataInicio, dataFim, filtros);
        this.exibirInformacoes(this.dadosFiltrados, tipo);
        this.exibirRelatorio(this.dadosFiltrados, tipo);
    },

    filtrarDados(dados, inicio, fim, filtros) {
        let resultado = this.filtrarPorData(dados, inicio, fim);

        if (filtros.busca) {
            const busca = filtros.busca.toLowerCase();
            resultado = resultado.filter(item => 
                item.nome?.toLowerCase().includes(busca) ||
                item.moradorVisitado?.toLowerCase().includes(busca) ||
                item.residencia?.toString().includes(busca)
            );
        }

        if (filtros.ordenacao) {
            resultado = this.ordenarDados(resultado, filtros.ordenacao);
        }

        return resultado;
    },

    ordenarDados(dados, ordenacao) {
        return [...dados].sort((a, b) => {
            switch(ordenacao) {
                case 'data-desc':
                    return new Date(b.entrada || b.data) - new Date(a.entrada || a.data);
                case 'data-asc':
                    return new Date(a.entrada || a.data) - new Date(b.entrada || b.data);
                case 'nome':
                    return (a.nome || '').localeCompare(b.nome || '');
                case 'residencia':
                    return (a.residencia || '').localeCompare(b.residencia || '');
                default:
                    return 0;
            }
        });
    },

    exibirInformacoes(dados, tipo) {
        const info = document.getElementById('relatorio-info');
        const total = dados.length;
        let texto = `Total de registros: ${total}`;

        switch(tipo) {
            case 'visitantes':
                const visitasAtivas = dados.filter(v => !v.saida).length;
                texto += ` | Visitas em andamento: ${visitasAtivas}`;
                break;
            case 'moradores':
                const residencias = new Set(dados.map(m => m.residencia)).size;
                texto += ` | Total de residências: ${residencias}`;
                break;
            case 'resumo':
                texto = this.gerarResumoGeral(dados);
                break;
        }

        info.textContent = texto;
    },

    gerarResumoGeral(dados) {
        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);

        const visitasHoje = dados.filter(v => 
            new Date(v.entrada) >= hoje
        ).length;

        const mediaVisitas = Math.round(dados.length / 30); // média mensal
        
        return `Visitas hoje: ${visitasHoje} | Média mensal: ${mediaVisitas} visitas`;
    },

    exportarCSV() {
        if (!this.dadosFiltrados.length) return;

        const headers = Object.keys(this.dadosFiltrados[0]);
        const csv = [
            headers.join(','),
            ...this.dadosFiltrados.map(row => 
                headers.map(field => JSON.stringify(row[field] || '')).join(',')
            )
        ].join('\n');

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'relatorio.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    },

    imprimir() {
        window.print();
    },

    obterDados(tipo) {
        switch(tipo) {
            case 'visitantes':
                return JSON.parse(localStorage.getItem('visitantes')) || [];
            case 'moradores':
                return JSON.parse(localStorage.getItem('moradores')) || [];
            case 'acessos':
                return JSON.parse(localStorage.getItem('acessos')) || [];
            default:
                return [];
        }
    },

    filtrarPorData(dados, inicio, fim) {
        if (!inicio || !fim) return dados;

        const dataInicio = new Date(inicio);
        const dataFim = new Date(fim);

        return dados.filter(item => {
            const data = new Date(item.entrada || item.data);
            return data >= dataInicio && data <= dataFim;
        });
    },

    exibirRelatorio(dados, tipo) {
        const container = document.getElementById('resultado-relatorio');
        container.innerHTML = '';

        const tabela = document.createElement('table');
        tabela.className = 'tabela-relatorio';

        // Cabeçalho da tabela
        const thead = document.createElement('thead');
        thead.innerHTML = this.obterCabecalhoTabela(tipo);
        tabela.appendChild(thead);

        // Corpo da tabela
        const tbody = document.createElement('tbody');
        dados.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = this.obterLinhaTabela(item, tipo);
            tbody.appendChild(tr);
        });
        tabela.appendChild(tbody);

        container.appendChild(tabela);
    },

    obterCabecalhoTabela(tipo) {
        switch(tipo) {
            case 'visitantes':
                return `
                    <tr>
                        <th>Nome</th>
                        <th>Morador Visitado</th>
                        <th>Entrada</th>
                        <th>Saída</th>
                    </tr>
                `;
            case 'moradores':
                return `
                    <tr>
                        <th>Nome</th>
                        <th>Residência</th>
                        <th>Telefone</th>
                    </tr>
                `;
            case 'acessos':
                return `
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Pessoa</th>
                    </tr>
                `;
            default:
                return '';
        }
    },

    obterLinhaTabela(item, tipo) {
        switch(tipo) {
            case 'visitantes':
                return `
                    <td>${item.nome}</td>
                    <td>${item.moradorVisitado}</td>
                    <td>${new Date(item.entrada).toLocaleString()}</td>
                    <td>${item.saida ? new Date(item.saida).toLocaleString() : '-'}</td>
                `;
            case 'moradores':
                return `
                    <td>${item.nome}</td>
                    <td>${item.residencia}</td>
                    <td>${item.telefone}</td>
                `;
            case 'acessos':
                return `
                    <td>${new Date(item.data).toLocaleString()}</td>
                    <td>${item.tipo}</td>
                    <td>${item.pessoa}</td>
                `;
            default:
                return '';
        }
    },

    configurarFormulario() {
        const form = document.getElementById('form-filtros');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.gerarRelatorio(
                form['tipo-relatorio'].value,
                form['data-inicio'].value,
                form['data-fim'].value,
                {
                    ordenacao: form['ordenacao'].value,
                    busca: form['busca'].value
                }
            );
        });
    }
};

document.addEventListener('DOMContentLoaded', () => gerenciadorRelatorios.init()); 