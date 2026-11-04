<?php
require_once "conexao.php";

// Variável para armazenar mensagem de erro
$erro = "";

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Receber e limpar os dados do formulário
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        // BUSCAR O USUÁRIO NO BANCO PELO EMAIL
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = mysqli_prepare($conexao, $sql);

        if ($stmt) {
            // Vincular o parâmetro e executar a consulta
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            // Obter o resultado
            $resultado = mysqli_stmt_get_result($stmt);
            $usuario = mysqli_fetch_assoc($resultado);

            // VERIFICA SE ENCONTROU O USUÁRIO
            if ($usuario) {
                // COMPARA A SENHA COM O HASH ARMAZENADO
                if (password_verify($senha, $usuario["senha"])) {
                    // Iniciar a sessão e armazenar os dados do usuário
                    session_start();
                    $_SESSION["usuario_id"]    = $usuario["id"];
                    $_SESSION["usuario_nome"]  = $usuario["nome"];
                    $_SESSION["usuario_email"] = $usuario["email"];
                    $_SESSION["usuario_tipo"]  = $usuario["tipo"];

                    // Redirecionar para a página inicial ou painel
                    header("Location: cursos.php");
                    exit;
                } else {
                    $erro = "Senha incorreta.";
                }
            } else {
                $erro = "Usuário não encontrado.";
            }
        } else {
            $erro = "Erro ao preparar a consulta: " . mysqli_error($conexao);
        }
    }
}
?>