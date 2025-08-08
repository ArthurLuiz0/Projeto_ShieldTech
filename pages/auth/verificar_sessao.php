<?php
/**
 * Arquivo para verificar se o usuário está logado
 * Incluir este arquivo no topo de páginas que precisam de autenticação
 */

session_start();

function verificarLogin() {
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_email'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}

function obterUsuarioLogado() {
    if (isset($_SESSION['usuario_id'])) {
        return [
            'id' => $_SESSION['usuario_id'],
            'nome' => $_SESSION['usuario_nome'],
            'email' => $_SESSION['usuario_email'],
            'tipo' => $_SESSION['usuario_tipo']
        ];
    }
    return null;
}

function isAdmin() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
}

function isFuncionario() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'funcionario';
}

function isMorador() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'morador';
}

// Verificar se a sessão ainda é válida (opcional - verificar no banco)
function verificarSessaoValida($conn) {
    if (isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
        $result = mysqli_query($conn, "SELECT status FROM tb_usuarios WHERE id_usuario = $usuario_id");
        
        if (mysqli_num_rows($result) == 0) {
            // Usuário não existe mais
            session_destroy();
            header("Location: ../auth/login.php");
            exit();
        }
        
        $usuario = mysqli_fetch_array($result);
        if ($usuario['status'] !== 'ativo') {
            // Usuário inativo ou bloqueado
            session_destroy();
            header("Location: ../auth/login.php?erro=conta_inativa");
            exit();
        }
    }
}
?>