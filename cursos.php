<?php
session_start();
require_once "conexao.php";

$sucesso = "";
$erro = "";

if (isset($_GET["excluir"])) {
    $id = intval($_GET["excluir"]);
    $res = mysqli_query($conexao, "SELECT capa FROM cursos WHERE id = $id");
    $capa = mysqli_fetch_assoc($res)["capa"] ?? "";
    $sql = "DELETE FROM cursos WHERE id = $id";
    if (mysqli_query($conexao, $sql)) {
        if (strpos($capa, "uploads/") === 0 && is_file(__DIR__ . "/" . $capa)) {
            unlink(__DIR__ . "/" . $capa);
        }
        $sucesso = "Curso excluído com sucesso!";
    } else {
        $erro = "Erro ao excluir curso.";
    }
}
if (isset($_GET["salvo"])) {
    $sucesso = "Curso salvo com sucesso!";
}

require_once "includes/menu.php";

$sql = "SELECT c.*, m.titulo AS modulo
        FROM cursos c
        LEFT JOIN modulos m ON m.id = c.modulo_id
        ORDER BY c.id DESC";
$cursos = mysqli_query($conexao, $sql);
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Cursos</h1>
                <p class="text-sm text-gray-500">Cadastre, edite e organize os cursos da plataforma</p>
            </div>
            <a href="curso_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Novo Curso
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
                            <th class="px-4 py-3 text-left">Título</th>
                            <th class="px-4 py-3 text-center">Módulo</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <?php while ($c = mysqli_fetch_assoc($cursos)): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <p class="text-gray-700 font-semibold"><?= htmlspecialchars($c["titulo"]) ?></p>
                                <p class="text-xs text-gray-400"><?= htmlspecialchars(mb_strimwidth($c["descricao"], 0, 80, "...")) ?></p>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?= htmlspecialchars($c["modulo"] ?? "-") ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if ($c["ativo"]): ?>
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">Ativo</span>
                                <?php else: ?>
                                    <span class="bg-gray-200 text-gray-600 text-xs font-semibold px-2 py-1 rounded">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <?php if ($c["modulo_id"]): ?>
                                        <a href="aulas.php?modulo_id=<?= $c["modulo_id"] ?>" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition" title="Ver Aulas do Módulo">🎬 Aulas</a>
                                    <?php endif; ?>
                                    <a href="curso_form.php?editar=<?= $c["id"] ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Editar">✏ Editar</a>
                                    <a onclick="return confirm('Tem certeza disso?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" href="?excluir=<?= $c["id"] ?>">Excluir</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo <?= mysqli_num_rows($cursos) ?> cursos</p>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
