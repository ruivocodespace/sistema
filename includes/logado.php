<?php
// ============================================================
// StudyLink — Protetor de Sessão 
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a variável essencial do usuário NÃO existe na sessão
if (!isset($_SESSION["usuario_id"])) {

    $_SESSION["url_tentada"] = $_SERVER["REQUEST_URI"];

    // Destrói qualquer sessão inválida por segurança
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();

    // Redireciona imediatamente para a tela de login
    header("Location: login.php");
    exit();
}
