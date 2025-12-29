<h1>Управление подпиской</h1>
<a class="btn" href="index.php?route=subscription/plans">Продлить доступ</a>
<table class="table">
    <thead>
        <tr>
            <th>Тариф</th>
            <th>Статус</th>
            <th>Начало</th>
            <th>Окончание</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($subscriptions as $subscription): ?>
            <tr>
                <td><?php echo htmlspecialchars($subscription['plan_name']); ?></td>
                <td><?php echo htmlspecialchars($subscription['status']); ?></td>
                <td><?php echo htmlspecialchars($subscription['start_at']); ?></td>
                <td><?php echo htmlspecialchars($subscription['end_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
