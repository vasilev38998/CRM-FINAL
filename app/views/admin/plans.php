<h1>Админка тарифов</h1>
<a class="btn" href="index.php?route=admin/plans/edit">Добавить тариф</a>
<table class="table">
    <thead>
        <tr>
            <th>Название</th>
            <th>Цена</th>
            <th>Длительность</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($plans as $plan): ?>
            <tr>
                <td><?php echo htmlspecialchars($plan['name']); ?></td>
                <td><?php echo money_format_ru((float) $plan['price']); ?></td>
                <td><?php echo htmlspecialchars($plan['duration_days']); ?> дней</td>
                <td><a class="btn btn-secondary" href="index.php?route=admin/plans/edit&id=<?php echo (int) $plan['id']; ?>">Редактировать</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
