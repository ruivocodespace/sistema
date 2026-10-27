<?php
session_start();
require_once "conexao.php";

$sucesso = "";
$erro = "";
$modulo_id = isset($_GET["modulo_id"]) ? intval($_GET["modulo_id"]) : 0;
$modulo = NULL;

if (isset($_GET["excluir"])) {
    $id = intval($_GET["excluir"]);
    $sql = "DELETE FROM aulas WHERE id = $id";
    if (mysqli_query($conexao, $sql)) {
        $sucesso = "Aula excluída com sucesso!";
    } else {
        $erro = "Erro ao excluir aula.";
    }
}
if (isset($_GET["salvo"])) {
    $sucesso = "Aula salva com sucesso!";
}

if ($modulo_id) {
    $res = mysqli_query($conexao, "SELECT id, titulo FROM modulos WHERE id = $modulo_id");
    $modulo = mysqli_fetch_assoc($res);
}

require_once "includes/menu.php";

$where = $modulo_id ? "WHERE a.modulo_id = $modulo_id" : "";
$sql = "SELECT a.*, m.titulo AS modulo
        FROM aulas a
        JOIN modulos m ON m.id = a.modulo_id
        $where
        ORDER BY m.ordem, a.ordem";
$aulas = mysqli_query($conexao, $sql);
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                    <a href="modulos.php" class="hover:text-senai-blue">Módulos</a> ›
                    <?php if ($modulo): ?>
                        <span class="text-gray-700 font-semibold"><?= htmlspecialchars($modulo["titulo"]) ?></span>
                    <?php else: ?>
                        <span class="text-gray-700 font-semibold">Todas as aulas</span>
                    <?php endif; ?>
                </div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Aulas</h1>
                <p class="text-sm text-gray-500">Cadastre os vídeos e conteúdos de cada módulo</p>
            </div>
            <a href="aula_form.php<?= $modulo_id ? "?modulo_id=$modulo_id" : "" ?>" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Nova Aula
            </a>
        </div>

        <div class="p-6 flex-1">

            <?php if (!empty($sucesso)): ?>
                <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                    <span class="font-bold text-base">✓</span>
                    <span><?= $sucesso ?></span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-700 text-lg leading-none">×</button>
                </div>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                    <span class="font-bold text-base">!</span>
                    <span><?= $erro ?></span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-700 text-lg leading-none">×</button>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-center">Ordem</th>
                            <th class="px-4 py-3 text-left">Título</th>
                            <th class="px-4 py-3 text-left">Módulo</th>
                            <th class="px-4 py-3 text-center">Duração</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <?php while ($a = mysqli_fetch_assoc($aulas)): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?= $a["ordem"] ?></td>
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($a["titulo"]) ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($a["modulo"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= htmlspecialchars($a["duracao"] ?? "-") ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <?php if (!empty($a["video_url"])): ?>
                                        <a href="<?= htmlspecialchars($a["video_url"]) ?>" target="_blank" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition" title="Ver Vídeo">▶ Vídeo</a>
                                    <?php endif; ?>
                                    <a href="aula_form.php?editar=<?= $a["id"] ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Editar">✏ Editar</a>
                                    <a onclick="return confirm('Tem certeza disso?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" href="?excluir=<?= $a["id"] ?><?= $modulo_id ? "&modulo_id=$modulo_id" : "" ?>">Excluir</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo <?= mysqli_num_rows($aulas) ?> aulas</p>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
