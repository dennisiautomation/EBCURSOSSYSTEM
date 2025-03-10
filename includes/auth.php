<?php
// Funções relacionadas à autenticação

// Iniciar sessão
function start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Verificar se o aluno está logado
function is_student_logged_in() {
    start_session();
    return isset($_SESSION['aluno_id']);
}

// Verificar se admin está logado
function is_admin_logged_in() {
    start_session();
    return isset($_SESSION['admin_id']);
}

// Exigir autenticação de aluno
function require_student_auth() {
    if (!is_student_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}

// Exigir autenticação de admin
function require_admin_auth() {
    if (!is_admin_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}
?>
