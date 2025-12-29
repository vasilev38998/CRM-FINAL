<h1>Калькулятор цен и маржи</h1>
<form method="get">
    <input type="hidden" name="route" value="products/pricing">
    <div class="form-group">
        <label>Желаемая маржа, %</label>
        <input type="number" step="0.1" name="margin" value="<?php echo htmlspecialchars((string) $margin); ?>">
    </div>
    <button type="submit">Пересчитать</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Напиток</th>
            <th>Себестоимость</th>
            <th>Текущая цена</th>
            <th>Рекомендуемая цена</th>
            <th>Маржа %</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo money_format_ru($item['cost']); ?></td>
                <td><?php echo money_format_ru($item['current_price']); ?></td>
                <td><?php echo money_format_ru($item['recommended_price']); ?></td>
                <td><?php echo number_format($item['margin'], 1, ',', ' '); ?> %</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
