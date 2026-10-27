<?php
session_start();
require_once "conexao.php";

$erro = "";
$editando = NULL;

if (isset($_GET["editar"])) {
    $id = intval($_GET["editar"]);
    $sql = "SELECT * FROM aulas WHERE id = $id";
    $res = mysqli_query($conexao, $sql);
    $editando = mysqli_fetch_assoc($res);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id        = intval($_POST["id"]);
    $modulo_id = intval($_POST["modulo_id"]);
    $titulo    = mysqli_real_escape_string($conexao, trim($_POST["titulo"]));
    $video_url = mysqli_real_escape_string($conexao, trim($_POST["video_url"]));
    $duracao   = mysqli_real_escape_string($conexao, trim($_POST["duracao"]));
    $descricao = mysqli_real_escape_string($conexao, trim($_POST["descricao"]));
    $ordem     = max(1, intval($_POST["ordem"]));

    if ($titulo === "" || !$modulo_id) {
        $erro = "Módulo e título são obrigatórios.";
    } else {
        if ($id) {
            $sql = "UPDATE aulas SET
                    modulo_id = $modulo_id,
                    titulo = '$titulo',
                    video_url = '$video_url',
                    duracao = '$duracao',
                    descricao = '$descricao',
                    ordem = $ordem
                    WHERE id = $id";
        } else {
            $sql = "INSERT INTO aulas (modulo_id, titulo, video_url, duracao, descricao, ordem) VALUES
                    ($modulo_id, '$titulo', '$video_url', '$duracao', '$descricao', $ordem)";
        }

        if (mysqli_query($conexao, $sql)) {
            header("Location: aulas.php?modulo_id=$modulo_id&salvo=1");
            exit;
        }
        $erro = "Erro ao salvar aula.";
    }
}

$modulo_selecionado = $editando["modulo_id"] ?? intval($_GET["modulo_id"] ?? 0);
$modulos = mysqli_query($conexao, "SELECT id, titulo FROM modulos ORDER BY ordem, titulo");

require_once "includes/menu.php";
?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="modulos.php" class="hover:text-senai-blue">Módulos</a> ›
                <a href="aulas.php<?= $modulo_selecionado ? "?modulo_id=$modulo_selecionado" : "" ?>" class="hover:text-senai-blue">Aulas</a> ›
                <span class="text-gray-700 font-semibold"><?= $editando ? "Editar Aula" : "Nova Aula" ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800"><?= $editando ? "Editar Aula" : "Nova Aula" ?></h1>
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
                        <label class="form-label">Módulo *</label>
                        <select name="modulo_id" class="form-input" required>
                            <option value="">Selecione o módulo</option>
                            <?php while ($m = mysqli_fetch_assoc($modulos)): ?>
                                <option value="<?= $m["id"] ?>" <?= $m["id"] == $modulo_selecionado ? "selected" : "" ?>><?= htmlspecialchars($m["titulo"]) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Título *</label>
                        <input type="text" name="titulo" class="form-input" required maxlength="150" placeholder="Ex: Variáveis e tipos" value="<?= htmlspecialchars($editando['titulo'] ?? "") ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">URL do vídeo</label>
                        <input type="url" name="video_url" class="form-input" maxlength="500" placeholder="https://youtube.com/..." value="<?= htmlspecialchars($editando['video_url'] ?? "") ?>">
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label">Duração</label>
                            <input type="text" name="duracao" class="form-input" maxlength="10" placeholder="12:30" value="<?= htmlspecialchars($editando['duracao'] ?? "") ?>">
                        </div>
                        <div>
                            <label class="form-label">Ordem *</label>
                            <input type="number" name="ordem" class="form-input" min="1" required value="<?= $editando['ordem'] ?? 1 ?>">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-input" rows="3" placeholder="Resumo do conteúdo da aula"><?= htmlspecialchars($editando['descricao'] ?? "") ?></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button>
                        <a href="aulas.php<?= $modulo_selecionado ? "?modulo_id=$modulo_selecionado" : "" ?>" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
