<h1>Сводная аналитика по сети кофеен</h1>
<form method="get">
    <input type="hidden" name="route" value="analytics/network">
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
        <h3>Операционные расходы</h3>
        <p><?php echo money_format_ru($expenses); ?></p>
    </div>
    <div class="card">
        <h3>Чистая прибыль</h3>
        <p><?php echo money_format_ru($netProfit); ?></p>
    </div>
    <div class="card">
        <h3>Рентабельность</h3>
        <p><?php echo number_format($profitability, 2, ',', ' '); ?> %</p>
    </div>
</div>
