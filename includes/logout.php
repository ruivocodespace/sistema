<?php
// Inicia sessão se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Armazena mensagem de despedida (opcional)
$_SESSION['mensagem_logout'] = "Você saiu com sucesso. Volte sempre!";

// Destroi todas as variáveis de sessão
$_SESSION = array();

// Remove o cookie de sessão se existir
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroi a sessão
session_destroy();

// Redireciona para a página inicial
header("Location: ../index.php");
exit();
