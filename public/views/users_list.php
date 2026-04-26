<?php

$title = 'Usuarios';

require __DIR__ . '/_header.php';
?>

<h2>Listado de usuarios</h2>
<p class="muted">Solo ADMIN puede ver esta lista.</p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user->id()->value(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($user->name()->value(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($user->email()->value(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($user->role()->value, ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <div class="actions">
                        <a href="index.php?route=user-edit&id=<?= urlencode($user->id()->value()) ?>">Editar</a>
                        <form method="post" action="index.php?route=user-delete" onsubmit="return confirm('Eliminar usuario?');">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($user->id()->value(), ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="secondary">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require __DIR__ . '/_footer.php';
