<?php
session_start();
require_once "conexao.php";

$sucesso = "";
$erro = "";

if (isset($_GET["excluir"])) {
    $id = intval($_GET["excluir"]);
    $sql = "DELETE FROM modulos WHERE id = $id";
    if (mysqli_query($conexao, $sql)) {
        $sucesso = "Módulo excluído com sucesso!";
    } else {
        $erro = "Erro ao excluir módulo.";
    }
}
if (isset($_GET["salvo"])) {
    $sucesso = "Módulo salvo com sucesso!";
}

require_once "includes/menu.php";

$sql = "SELECT m.*,
            (SELECT COUNT(*) FROM cursos c WHERE c.modulo_id = m.id) AS total_cursos,
            (SELECT COUNT(*) FROM aulas a WHERE a.modulo_id = m.id) AS total_aulas
        FROM modulos m
        ORDER BY m.ordem, m.titulo";
$modulos = mysqli_query($conexao, $sql);
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Módulos</h1>
                <p class="text-sm text-gray-500">Cadastre os módulos e vincule-os aos cursos</p>
            </div>
            <a href="modulo_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Novo Módulo
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
                            <th class="px-4 py-3 text-center">Cursos</th>
                            <th class="px-4 py-3 text-center">Aulas</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <?php while ($m = mysqli_fetch_assoc($modulos)): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?= $m["ordem"] ?></td>
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($m["titulo"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?= $m["total_cursos"] ?></td>
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold"><?= $m["total_aulas"] ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="aulas.php?modulo_id=<?= $m["id"] ?>" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition" title="Ver Aulas">🎬 Aulas</a>
                                    <a href="modulo_form.php?editar=<?= $m["id"] ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Editar">✏ Editar</a>
                                    <a onclick="return confirm('Excluir o módulo apaga também suas aulas e desvincula os cursos. Tem certeza?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" href="?excluir=<?= $m["id"] ?>">Excluir</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo <?= mysqli_num_rows($modulos) ?> módulos</p>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
