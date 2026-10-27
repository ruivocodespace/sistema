<?php
session_start();
require_once "conexao.php";

$erro = "";
$editando = NULL;

if (isset($_GET["editar"])) {
    $id = intval($_GET["editar"]);
    $sql = "SELECT * FROM modulos WHERE id = $id";
    $res = mysqli_query($conexao, $sql);
    $editando = mysqli_fetch_assoc($res);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id        = intval($_POST["id"]);
    $titulo    = mysqli_real_escape_string($conexao, trim($_POST["titulo"]));
    $descricao = mysqli_real_escape_string($conexao, trim($_POST["descricao"]));
    $ordem     = max(1, intval($_POST["ordem"]));

    if ($titulo === "") {
        $erro = "O título é obrigatório.";
    } else {
        if ($id) {
            $sql = "UPDATE modulos SET
                    titulo = '$titulo',
                    descricao = '$descricao',
                    ordem = $ordem
                    WHERE id = $id";
        } else {
            $sql = "INSERT INTO modulos (titulo, descricao, ordem) VALUES
                    ('$titulo', '$descricao', $ordem)";
        }

        if (mysqli_query($conexao, $sql)) {
            header("Location: modulos.php?salvo=1");
            exit;
        }
        $erro = "Erro ao salvar módulo.";
    }
}

require_once "includes/menu.php";
?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="modulos.php" class="hover:text-senai-blue">Módulos</a> ›
                <span class="text-gray-700 font-semibold"><?= $editando ? "Editar Módulo" : "Novo Módulo" ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800"><?= $editando ? "Editar Módulo" : "Novo Módulo" ?></h1>
        </div>
        <div class="p-6 flex-1 max-w-xl">

            <?php if (!empty($erro)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?= $erro ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="post">
                    <input type="hidden" value="<?= $editando['id'] ?? "" ?>" name="id"/>
                    <div class="mb-4">
                        <label class="form-label">Título *</label>
                        <input type="text" name="titulo" class="form-input" required maxlength="150" placeholder="Ex: Introdução" value="<?= htmlspecialchars($editando['titulo'] ?? "") ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-input" rows="3" placeholder="O que será visto neste módulo"><?= htmlspecialchars($editando['descricao'] ?? "") ?></textarea>
                    </div>
                    <div class="mb-6">
                        <label class="form-label">Ordem *</label>
                        <input type="number" name="ordem" class="form-input" min="1" required value="<?= $editando['ordem'] ?? 1 ?>">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button>
                        <a href="modulos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
