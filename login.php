<?php
require_once "conexao.php";

$pdo = $conexao ?? $pdo ?? $conn ?? null;
if (!$pdo) {
    die("Erro: Conexão com o banco de dados não encontrada.");
}

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
        try {
            // BUSCAR O USUÁRIO NO BANCO PELO EMAIL
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $conexao->prepare($sql);
            $stmt->execute([':email' => $email]);

            $usuario = $stmt->fetch();

            // VERIFICA SE ENCONTROU O USUÁRIO
            if ($usuario) {
                // COMPARA A SENHA COM O HASH ARMAZENADO
                if (password_verify($senha, $usuario["senha"])) {

                    $_SESSION["usuario_id"]    = $usuario["id"];
                    $_SESSION["usuario_nome"]  = $usuario["nome"];
                    $_SESSION["usuario_email"] = $usuario["email"];
                    $_SESSION["usuario_tipo"]  = $usuario["tipo"];

                    // Formato de array
                    $_SESSION["usuario"] = [
                        "id"    => $usuario["id"],
                        "nome"  => $usuario["nome"],
                        "email" => $usuario["email"],
                        "tipo"  => $usuario["tipo"]
                    ];

                    // Lógica opcional para o "Lembrar de mim"
                    if (isset($_POST['lembrar'])) {
                        // Define um cookie seguro válido por 30 dias se o usuário marcar a caixinha
                        setcookie("usuario_email", $email, time() + (86400 * 30), "/");
                    }

                    // REDIRECIONAMENTO COM SWITCH CASE PARA DIFERENTES TIPOS DE USUÁRIO
                    switch ($_SESSION["usuario_tipo"]) {
                        case 'admin':
                            header("Location: cursos.php");
                            exit();

                        case 'aluno':
                        case 'ambos': // Perfil duplo: Entra por padrão no painel de aluno
                            header("Location: inscricoes.php");
                            exit();

                        default:
                            header("Location: login.php");
                            exit();
                    }
                } else {
                    $erro = "E-mail ou senha incorretos.";
                }
            } else {
                $erro = "E-mail ou senha incorretos.";
            }
        } catch (PDOException $e) {
            $erro = "Erro interno no sistema. Tente novamente mais tarde.";
            error_log("Erro no Login: " . $e->getMessage());
        }
    }
}

// O Cabeçalho (HTML)
include "includes/menu.php";
?>

<div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 py-12 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">

    <div class="max-w-md w-full mb-4">
        <?php if (!empty($erro)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg text-sm shadow-sm">
                ⚠️ <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION["sucesso_cadastro"])): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg text-sm shadow-sm">
                ✅ <?php echo htmlspecialchars($_SESSION["sucesso_cadastro"]); ?>
            </div>
            <?php unset($_SESSION["sucesso_cadastro"]); ?>
        <?php endif; ?>
    </div>

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">

        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8 text-center">
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Acesse sua conta</h1>
            <p class="text-indigo-100 text-sm">Aprenda e Ensine com Quem Entende Você</p>
        </div>

        <div class="p-6 md:p-8">

            <form method="POST" action="" class="space-y-5" id="form-login">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" id="email" name="email" required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ($_COOKIE['usuario_email'] ?? '')); ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm text-gray-700"
                        placeholder="seu@email.com">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                        <a href="recuperar-senha.php" class="text-xs text-indigo-600 hover:underline">Esqueceu a senha?</a>
                    </div>
                    <div class="relative">
                        <input type="password" id="senha" name="senha" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm pr-10 text-gray-700"
                            placeholder="Insira sua senha">

                        <button type="button" onclick="toggleSenha('senha')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" aria-label="Mostrar senha">
                            <svg id="icon-olho" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="lembrar" name="lembrar" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" <?php echo isset($_COOKIE['usuario_email']) ? 'checked' : ''; ?>>
                    <label for="lembrar" class="ml-2 text-sm text-gray-600 select-none">Lembrar de mim</label>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl transition transform hover:scale-[1.02] shadow-lg text-base">
                    Entrar na plataforma
                </button>

            </form>

            <p class="text-center text-sm text-gray-600 mt-6">
                Novo no StudyLink? <a href="cadastro.php" class="text-indigo-600 font-semibold hover:underline">Crie sua conta</a>
            </p>
        </div>
    </div>
</div>

<script>
    // Toggle visibilidade da senha
    function toggleSenha(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>

<?php include 'includes/footer.php'; ?>