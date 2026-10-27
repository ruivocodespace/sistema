<?php
session_start();
require_once "conexao.php";
require_once "includes/menu.php";

$sql = "SELECT
    (SELECT COUNT(*) FROM cursos) AS cursos,
    (SELECT COUNT(*) FROM modulos) AS modulos,
    (SELECT COUNT(*) FROM aulas) AS aulas,
    (SELECT COUNT(*) FROM usuarios) AS usuarios,
    (SELECT COUNT(*) FROM inscricoes) AS inscricoes";
$totais = mysqli_fetch_assoc(mysqli_query($conexao, $sql));

$cards = [
    ["📚", "Cursos", $totais["cursos"], "cursos.php", "bg-senai-blue"],
    ["📦", "Módulos", $totais["modulos"], "modulos.php", "bg-senai-orange"],
    ["🎬", "Aulas", $totais["modulos"], "aulas.php", "bg-senai-green"],
    ["👤", "Usuários", $totais["usuarios"], "usuario.php", "bg-senai-red"],
];
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <h1 class="text-xl font-extrabold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-500">Visão geral da plataforma · <?= $totais["inscricoes"] ?> inscrições</p>
        </div>

        <div class="p-6 flex-1">

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <?php foreach ($cards as $c): ?>
                    <a href="<?= $c[3] ?>" class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                        <div class="<?= $c[4] ?> text-white text-2xl w-12 h-12 rounded-lg flex items-center justify-center"><?= $c[0] ?></div>
                        <div>
                            <p class="text-2xl font-extrabold text-gray-800"><?= $c[2] ?></p>
                            <p class="text-xs text-gray-500 uppercase font-semibold"><?= $c[1] ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800">Últimos cursos</h2>
                    <a href="cursos.php" class="text-xs text-senai-blue hover:underline">Ver todos →</a>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Título</th>
                            <th class="px-4 py-3 text-center">Módulo</th>
                            <th class="px-4 py-3 text-center">Inscritos</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    <?php
                        $sql = "SELECT c.id, c.titulo, c.ativo, m.titulo AS modulo,
                                    (SELECT COUNT(*) FROM inscricoes i WHERE i.curso_id = c.id) AS total_inscritos
                                FROM cursos c
                                LEFT JOIN modulos m ON m.id = c.modulo_id
                                ORDER BY c.id DESC LIMIT 5";
                        $cursos = mysqli_query($conexao, $sql);
                        while ($c = mysqli_fetch_assoc($cursos)):
                    ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($c["titulo"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= htmlspecialchars($c["modulo"] ?? "-") ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= $c["total_inscritos"] ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if ($c["ativo"]): ?>
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">Ativo</span>
                                <?php else: ?>
                                    <span class="bg-gray-200 text-gray-600 text-xs font-semibold px-2 py-1 rounded">Inativo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

</body>
</html>
