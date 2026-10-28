<!-- Cabeçalho da tabela -->
<thead class="bg-senai-blue text-white">
    <tr>
        <th class="px-4 py-3 text-left">Título</th>
        <th class="px-4 py-3 text-center">Professor</th> <!-- NOVA COLUNA -->
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
        <!-- EXIBIÇÃO DO PROFESSOR -->
        <td class="px-4 py-3 text-center text-gray-600"><?= htmlspecialchars($c["professor"] ?? "-") ?></td>
        
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