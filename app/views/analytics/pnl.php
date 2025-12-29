<h1>P&L отчёт</h1>
<form method="get">
    <input type="hidden" name="route" value="analytics/pnl">
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
    <div class="card">
        <h3>Точка безубыточности</h3>
        <p><?php echo $breakEven > 0 ? money_format_ru($breakEven) : 'Н/Д'; ?></p>
    </div>
</div>

<h2>Маржинальность по напиткам</h2>
<table class="table">
    <thead>
        <tr>
            <th>Напиток</th>
            <th>Выручка</th>
            <th>COGS</th>
            <th>Прибыль</th>
            <th>Маржа</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productMargins as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo money_format_ru($row['revenue']); ?></td>
                <td><?php echo money_format_ru($row['cogs']); ?></td>
                <td><?php echo money_format_ru($row['profit']); ?></td>
                <td><?php echo number_format($row['margin'], 2, ',', ' '); ?> %</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
