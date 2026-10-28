<?php
session_start();
require_once "conexao.php";

$erro = "";
$editando = NULL;

if (isset($_GET["editar"])) {
    $id = intval($_GET["editar"]);
    $sql = "SELECT * FROM cursos WHERE id = $id";
    $res = mysqli_query($conexao, $sql);
    $editando = mysqli_fetch_assoc($res);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id        = intval($_POST["id"]);
    $modulo_id = intval($_POST["modulo_id"]);
    $titulo    = mysqli_real_escape_string($conexao, trim($_POST["titulo"]));
    $descricao = mysqli_real_escape_string($conexao, trim($_POST["descricao"]));
    $ativo     = isset($_POST["ativo"]) ? 1 : 0;
    $capa_antiga = $editando["capa"] ?? "";
    $capa      = $capa_antiga;

    if ($titulo === "" || $descricao === "" || !$modulo_id) {
        $erro = "Título, descrição e módulo são obrigatórios.";
    } elseif (!empty($_FILES["capa"]["name"])) {
        $arquivo  = $_FILES["capa"];
        $extensao = strtolower(pathinfo($arquivo["name"], PATHINFO_EXTENSION));

        if ($arquivo["error"] !== UPLOAD_ERR_OK || $arquivo["size"] > 2 * 1024 * 1024) {
            $erro = "Erro no envio da capa. O tamanho máximo é 2 MB.";
        } elseif (!in_array($extensao, ["jpg", "jpeg", "png", "gif", "webp"]) || !getimagesize($arquivo["tmp_name"])) {
            $erro = "A capa deve ser uma imagem JPG, PNG, GIF ou WEBP.";
        } else {
            $capa = "uploads/" . uniqid() . "." . $extensao;
            if (!move_uploaded_file($arquivo["tmp_name"], __DIR__ . "/" . $capa)) {
                $erro = "Não foi possível salvar a capa.";
            }
        }
    }

    if ($erro === "") {
        if ($id) {
            $sql = "UPDATE cursos SET
                    modulo_id = $modulo_id,
                    titulo = '$titulo',
                    descricao = '$descricao',
                    professor = '$professor',
                    capa = '$capa',
                    ativo = $ativo
                    WHERE id = $id";
        } else {
            $sql = "INSERT INTO cursos (modulo_id, titulo, descricao, professor, capa, ativo) VALUES
                    ($modulo_id, '$titulo', '$descricao', '$professor', '$capa', $ativo)";
        }

        if (mysqli_query($conexao, $sql)) {
            if ($capa !== $capa_antiga && strpos($capa_antiga, "uploads/") === 0 && is_file(__DIR__ . "/" . $capa_antiga)) {
                unlink(__DIR__ . "/" . $capa_antiga);
            }
            header("Location: cursos.php?salvo=1");
            exit;
        }
        $erro = "Erro ao salvar curso.";
    }
}

$modulos = mysqli_query($conexao, "SELECT id, titulo FROM modulos ORDER BY ordem, titulo");

require_once "includes/menu.php";
?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="cursos.php" class="hover:text-senai-blue">Cursos</a> ›
                <span class="text-gray-700 font-semibold"><?= $editando ? "Editar Curso" : "Novo Curso" ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800"><?= $editando ? "Editar Curso" : "Novo Curso" ?></h1>
        </div>
        <div class="p-6 flex-1 max-w-xl">

            <?php if (!empty($erro)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?= $erro ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" value="<?= $editando['id'] ?? "" ?>" name="id"/>
                    <div class="mb-4">
                        <label class="form-label">Título *</label>
                        <input type="text" name="titulo" class="form-input" required maxlength="150" placeholder="Ex: PHP do Zero" value="<?= htmlspecialchars($editando['titulo'] ?? "") ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Descrição *</label>
                        <textarea name="descricao" class="form-input" rows="4" required placeholder="Sobre o que é o curso"><?= htmlspecialchars($editando['descricao'] ?? "") ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Módulo *</label>
                        <select name="modulo_id" class="form-input" required>
                            <option value="">Selecione o módulo</option>
                            <?php while ($m = mysqli_fetch_assoc($modulos)): ?>
                                <option value="<?= $m["id"] ?>" <?= $m["id"] == ($editando["modulo_id"] ?? 0) ? "selected" : "" ?>><?= htmlspecialchars($m["titulo"]) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Professor *</label>
                        <input type="text" name="professor" class="form-input" required maxlength="150" placeholder="Ex: João Silva" value="<?= htmlspecialchars($editando['professor'] ?? "") ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Capa</label>
                        <?php if (!empty($editando['capa'])): ?>
                            <img src="<?= htmlspecialchars($editando['capa']) ?>" alt="Capa atual" class="w-40 h-24 object-cover rounded-lg mb-2 border border-gray-200">
                        <?php endif; ?>
                        <input type="file" name="capa" class="form-input" accept="image/jpeg,image/png,image/gif,image/webp">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF ou WEBP até 2 MB<?= !empty($editando['capa']) ? ". Deixe em branco para manter a capa atual" : "" ?>.</p>
                    </div>
                    <div class="mb-6">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="ativo" value="1" <?= ($editando['ativo'] ?? 1) ? "checked" : "" ?>>
                            Curso ativo
                        </label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button>
                        <a href="cursos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
