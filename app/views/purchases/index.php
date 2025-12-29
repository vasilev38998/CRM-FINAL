<h1>Закупки</h1>
<a class="btn" href="index.php?route=purchases/create">Добавить закупку</a>
<a class="btn btn-secondary" href="index.php?route=import/purchases">Импорт CSV</a>
<a class="btn btn-secondary" href="index.php?route=export/purchases">Экспорт XLS</a>
<table class="table">
    <thead>
        <tr>
            <th>Ингредиент</th>
            <th>Количество</th>
            <th>Цена</th>
            <th>Сумма</th>
            <th>Дата</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($purchases as $purchase): ?>
            <tr>
                <td><?php echo htmlspecialchars($purchase['ingredient_name']); ?></td>
                <td><?php echo number_format((float) $purchase['qty'], 2, ',', ' '); ?></td>
                <td><?php echo money_format_ru((float) $purchase['price']); ?></td>
                <td><?php echo money_format_ru((float) $purchase['total']); ?></td>
                <td><?php echo htmlspecialchars($purchase['purchased_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
