<?php
// sessao.php
const TEMPO_LIMITE = 15 * 60; // 15 minutos em segundos

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_SESSION['ultimo_acesso']) && (time() - $_SESSION['ultimo_acesso'] > TEMPO_LIMITE)) {
    session_unset();
    session_destroy();
    header('Location: ../login.php?expirou=1');
    exit;
}

$_SESSION['ultimo_acesso'] = time(); // renova o tempo a cada acesso