<h1>Дашборд</h1>
<form method="get">
    <input type="hidden" name="route" value="dashboard">
    <div class="form-group">
        <label>Период с</label>
        <input type="date" name="from" value="<?php echo htmlspecialchars($start); ?>">
    </div>
    <div class="form-group">
        <label>По</label>
        <input type="date" name="to" value="<?php echo htmlspecialchars($end); ?>">
    </div>
    <button type="submit">Применить</button>
</form>
<div class="card-grid">
    <div class="card">
        <h3>Выручка</h3>
        <p><?php echo money_format_ru($revenue); ?></p>
    </div>
    <div class="card">
        <h3>COGS</h3>
        <p><?php echo money_format_ru($cogs); ?></p>
    </div>
    <div class="card">
        <h3>Валовая прибыль</h3>
        <p><?php echo money_format_ru($grossProfit); ?></p>
    </div>
    <div class="card">
        <h3>Валовая маржа</h3>
        <p><?php echo number_format($grossMargin, 2, ',', ' '); ?> %</p>
    </div>
    <div class="card">
        <h3>Операционные расходы</h3>
        <p><?php echo money_format_ru($expenses); ?></p>
    </div>
    <div class="card">
        <h3>Чистая прибыль</h3>
        <p><?php echo money_format_ru($netProfit); ?></p>
    </div>
    <div class="card">
        <h3>Рентабельность продаж</h3>
        <p><?php echo number_format($profitability, 2, ',', ' '); ?> %</p>
    </div>
</div>

<h2>Ингредиенты с низким остатком</h2>
<?php if ($lowStock): ?>
    <table class="table">
        <thead>
            <tr>
                <th>Ингредиент</th>
                <th>Остаток</th>
                <th>Мин. остаток</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lowStock as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo number_format((float) $item['stock_qty'], 2, ',', ' ') . ' ' . htmlspecialchars($item['unit']); ?></td>
                    <td><?php echo number_format((float) $item['min_stock_qty'], 2, ',', ' ') . ' ' . htmlspecialchars($item['unit']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Все ингредиенты выше минимального остатка.</p>
<?php endif; ?>
