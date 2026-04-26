<?php

$title = 'Registro';

require __DIR__ . '/_header.php';
?>

<h2>Registro de usuario</h2>
<p class="muted">Crea una cuenta nueva en el sistema.</p>

<form method="post" action="index.php?route=register">
    <div>
        <label>Nombre</label>
        <input type="text" name="name" required minlength="2" maxlength="100">
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div>
        <label>Contrasena</label>
        <input type="password" name="password" required minlength="8">
    </div>
    <div>
        <label>Rol</label>
        <select name="role">
            <option value="user" selected>USER</option>
            <option value="admin">ADMIN</option>
        </select>
    </div>
    <button type="submit">Crear cuenta</button>
</form>

<?php
require __DIR__ . '/_footer.php';
