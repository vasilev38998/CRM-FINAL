<h1>Ингредиенты</h1>
<a class="btn" href="index.php?route=ingredients/create">Добавить ингредиент</a>
<table class="table">
    <thead>
        <tr>
            <th>Название</th>
            <th>Ед.</th>
            <th>Остаток</th>
            <th>Мин. остаток</th>
            <th>Средняя цена</th>
            <th>Статус</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ingredients as $ingredient): ?>
            <?php $isLow = (float) $ingredient['stock_qty'] <= (float) $ingredient['min_stock_qty']; ?>
            <tr>
                <td><?php echo htmlspecialchars($ingredient['name']); ?></td>
                <td><?php echo htmlspecialchars($ingredient['unit']); ?></td>
                <td><?php echo number_format((float) $ingredient['stock_qty'], 2, ',', ' '); ?></td>
                <td><?php echo number_format((float) $ingredient['min_stock_qty'], 2, ',', ' '); ?></td>
                <td><?php echo money_format_ru((float) $ingredient['avg_price']); ?></td>
                <td><?php echo $isLow ? 'Низкий остаток' : 'ОК'; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
