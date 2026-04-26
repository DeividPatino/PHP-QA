<?php

$title = 'Login';

require __DIR__ . '/_header.php';
?>

<h2>Login</h2>
<p class="muted">Ingresa tus credenciales.</p>

<form method="post" action="index.php?route=login">
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div>
        <label>Contrasena</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Ingresar</button>
</form>

<?php
require __DIR__ . '/_footer.php';
