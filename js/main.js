// Funções para a página inicial
function registrarEntrada() {
    window.location.href = 'pages/visitantes.html';
}

function registrarSaida() {
    window.location.href = 'pages/visitantes.html';
}

function novoMorador() {
    window.location.href = 'pages/cadastro_moradores.html';
}

// Atualizar contadores do dashboard
function atualizarDashboard() {
    const visitantesHoje = parseInt(localStorage.getItem('visitantesHoje')) || 0;
    const moradoresPresentes = parseInt(localStorage.getItem('moradoresPresentes')) || 0;
    const totalResidencias = parseInt(localStorage.getItem('totalResidencias')) || 0;

    const elementoVisitantes = document.getElementById('visitantes-hoje');
    const elementoMoradores = document.getElementById('moradores-presentes');
    const elementoResidencias = document.getElementById('total-residencias');

    if (elementoVisitantes) elementoVisitantes.textContent = formatarNumero(visitantesHoje);
    if (elementoMoradores) elementoMoradores.textContent = formatarNumero(moradoresPresentes);
    if (elementoResidencias) elementoResidencias.textContent = formatarNumero(totalResidencias);

    // Atualiza a cada 30 segundos
    setTimeout(atualizarDashboard, 30000);
}

// Formata números para exibição
function formatarNumero(numero) {
    return numero.toLocaleString('pt-BR');
}

// Função para mostrar notificações
function showNotification(message, type = 'success') {
    // Remove notificações existentes
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Remove notification after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Inicializar dashboard e configurar atualização automática
document.addEventListener('DOMContentLoaded', () => {
    atualizarDashboard();
    
    // Configurar menu mobile
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu');
    
    if (menuToggle && menu) {
        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    }
});