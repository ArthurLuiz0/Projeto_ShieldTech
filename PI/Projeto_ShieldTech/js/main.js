// Funções para a página inicial
function registrarEntrada() {
    window.location.href = 'pages/visitantes.html';
}

function registrarSaida() {
    window.location.href = 'pages/visitantes.html';
}

function novoMorador() {
    window.location.href = 'pages/moradores.html';
}

// Atualizar contadores do dashboard
function atualizarDashboard() {
    const visitantesHoje = parseInt(localStorage.getItem('visitantesHoje')) || 0;
    const moradoresPresentes = parseInt(localStorage.getItem('moradoresPresentes')) || 0;
    const totalResidencias = parseInt(localStorage.getItem('totalResidencias')) || 0;

    document.getElementById('visitantes-hoje').textContent = formatarNumero(visitantesHoje);
    document.getElementById('moradores-presentes').textContent = formatarNumero(moradoresPresentes);
    document.getElementById('total-residencias').textContent = formatarNumero(totalResidencias);

    // Atualiza a cada 30 segundos
    setTimeout(atualizarDashboard, 30000);
}

// Formata números para exibição
function formatarNumero(numero) {
    return numero.toLocaleString('pt-BR');
}

// Inicializar dashboard e configurar atualização automática
document.addEventListener('DOMContentLoaded', () => {
    atualizarDashboard();
}); 