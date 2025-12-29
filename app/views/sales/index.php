<h1>Продажи</h1>
<a class="btn" href="index.php?route=sales/create">Добавить продажу</a>
<a class="btn btn-secondary" href="index.php?route=import/sales">Импорт CSV</a>
<<<<<<< HEAD
<a class="btn btn-secondary" href="index.php?route=export/sales">Экспорт XLS</a>
=======
>>>>>>> origin/main
<table class="table">
    <thead>
        <tr>
            <th>Напиток</th>
            <th>Количество</th>
            <th>Цена</th>
            <th>Выручка</th>
            <th>COGS</th>
            <th>Дата</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                <td><?php echo number_format((float) $sale['qty'], 2, ',', ' '); ?></td>
                <td><?php echo money_format_ru((float) $sale['price']); ?></td>
                <td><?php echo money_format_ru((float) $sale['total']); ?></td>
                <td><?php echo money_format_ru((float) $sale['cogs']); ?></td>
                <td><?php echo htmlspecialchars($sale['sold_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
