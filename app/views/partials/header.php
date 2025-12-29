<?php $user = current_user(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>CoffeeFin</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<header>
    <div class="nav">
        <a href="index.php">CoffeeFin</a>
        <?php if ($user): ?>
            <a href="index.php?route=dashboard">Дашборд</a>
            <a href="index.php?route=ingredients">Склад</a>
            <a href="index.php?route=purchases">Закупки</a>
            <a href="index.php?route=products">Напитки</a>
            <a href="index.php?route=recipes">Рецепты</a>
            <a href="index.php?route=sales">Продажи</a>
            <a href="index.php?route=expenses">Расходы</a>
            <a href="index.php?route=analytics/pnl">P&L</a>
            <a href="index.php?route=subscription/manage">Подписки</a>
            <a href="index.php?route=coffee/select">Кофейни</a>
            <?php if (is_admin()): ?>
                <a href="index.php?route=admin/plans">Админка тарифов</a>
            <?php endif; ?>
            <a href="index.php?route=auth/logout">Выход</a>
        <?php else: ?>
            <a href="index.php?route=subscription/plans">Тарифы</a>
            <a href="index.php?route=auth/login">Вход</a>
            <a href="index.php?route=auth/register">Регистрация</a>
        <?php endif; ?>
    </div>
</header>
<div class="container">
    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
