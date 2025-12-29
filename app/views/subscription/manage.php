<h1>Управление подпиской</h1>
<a class="btn" href="index.php?route=subscription/plans">Продлить доступ</a>
<?php if ($subscription): ?>
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
            <tr>
                <td><?php echo htmlspecialchars($subscription['plan_name']); ?></td>
                <td><?php echo htmlspecialchars($subscription['status']); ?></td>
                <td><?php echo htmlspecialchars($subscription['start_at']); ?></td>
                <td><?php echo htmlspecialchars($subscription['end_at']); ?></td>
            </tr>
        </tbody>
    </table>
<?php else: ?>
    <p>Подписка ещё не оформлена.</p>
<?php endif; ?>
