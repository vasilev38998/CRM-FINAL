<h1>Админка: сводка</h1>
<div class="card-grid">
    <div class="card">
        <h3>Пользователи</h3>
        <p><?php echo (int) $totalUsers; ?></p>
    </div>
    <div class="card">
        <h3>Кофейни</h3>
        <p><?php echo (int) $totalShops; ?></p>
    </div>
    <div class="card">
        <h3>Подписки всего</h3>
        <p><?php echo (int) $totalSubscriptions; ?></p>
    </div>
    <div class="card">
        <h3>Активные подписки</h3>
        <p><?php echo (int) $activeSubscriptions; ?></p>
    </div>
    <div class="card">
        <h3>Оплаты всего</h3>
        <p><?php echo money_format_ru($paymentsTotal); ?></p>
    </div>
</div>

<h2>Новые пользователи</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Дата регистрации</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentUsers as $user): ?>
            <tr>
                <td><?php echo (int) $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Подписки</h2>
<table class="table">
    <thead>
        <tr>
            <th>Email</th>
            <th>Тариф</th>
            <th>Статус</th>
            <th>Начало</th>
            <th>Окончание</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($subscriptions as $subscription): ?>
            <tr>
                <td><?php echo htmlspecialchars($subscription['email']); ?></td>
                <td><?php echo htmlspecialchars($subscription['plan_name']); ?></td>
                <td><?php echo htmlspecialchars($subscription['status']); ?></td>
                <td><?php echo htmlspecialchars($subscription['start_at']); ?></td>
                <td><?php echo htmlspecialchars($subscription['end_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
