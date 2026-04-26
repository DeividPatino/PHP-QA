<?php

declare(strict_types=1);

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'User Module', ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            margin: 0;
            padding: 0;
            color: #1f2933;
        }
        header {
            background: #1f2933;
            color: #fff;
            padding: 16px 24px;
        }
        header a {
            color: #fff;
            text-decoration: none;
            margin-right: 12px;
        }
        .container {
            max-width: 900px;
            margin: 24px auto;
            background: #fff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        form {
            display: grid;
            gap: 12px;
        }
        label {
            font-weight: bold;
            font-size: 14px;
        }
        input, select {
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #cbd2d9;
            border-radius: 6px;
        }
        button {
            padding: 10px 14px;
            background: #1d72f3;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        button.secondary {
            background: #65737e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px 12px;
            border-bottom: 1px solid #e4e7eb;
            text-align: left;
        }
        .flash {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .flash.success {
            background: #e6f4ea;
            color: #1e7e34;
        }
        .flash.error {
            background: #fdecea;
            color: #d64545;
        }
        .actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .muted {
            color: #616e7c;
            font-size: 13px;
        }
    </style>
</head>
<body>
<header>
    <nav>
        <?php if (empty($currentUser)): ?>
            <a href="index.php?route=register">Registro</a>
            <a href="index.php?route=login">Login</a>
        <?php else: ?>
            <a href="index.php?route=profile">Mi perfil</a>
            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                <a href="index.php?route=users">Usuarios</a>
            <?php endif; ?>
            <a href="index.php?route=logout">Salir</a>
        <?php endif; ?>
    </nav>
</header>
<div class="container">
    <?php if (!empty($flash)): ?>
        <div class="flash <?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
