<?php

$title = 'Editar usuario';

require __DIR__ . '/_header.php';
?>

<h2>Editar usuario</h2>
<p class="muted">Actualiza los datos del perfil.</p>

<form method="post" action="index.php?route=user-edit">
    <input type="hidden" name="id" value="<?= htmlspecialchars($user->id()->value(), ENT_QUOTES, 'UTF-8') ?>">

    <div>
        <label>Nombre</label>
        <input type="text" name="name" required minlength="2" maxlength="100" value="<?= htmlspecialchars($user->name()->value(), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($user->email()->value(), ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
        <label>Nueva contrasena (opcional)</label>
        <input type="password" name="password" minlength="8">
    </div>

    <?php if (!empty($isAdmin)): ?>
        <div>
            <label>Rol</label>
            <select name="role">
                <option value="user" <?= $user->role()->value === 'user' ? 'selected' : '' ?>>USER</option>
                <option value="admin" <?= $user->role()->value === 'admin' ? 'selected' : '' ?>>ADMIN</option>
            </select>
        </div>
    <?php else: ?>
        <div>
            <label>Rol</label>
            <input type="text" value="<?= htmlspecialchars($user->role()->value, ENT_QUOTES, 'UTF-8') ?>" readonly>
        </div>
    <?php endif; ?>

    <button type="submit">Guardar cambios</button>
</form>

<?php
require __DIR__ . '/_footer.php';
