<h1>Сценарий: <?php echo htmlspecialchars($scenario['name']); ?></h1>
<div class="card-grid">
    <div class="card">
        <h3>Базовая выручка</h3>
        <p><?php echo money_format_ru($base['revenue']); ?></p>
    </div>
    <div class="card">
        <h3>Базовый COGS</h3>
        <p><?php echo money_format_ru($base['cogs']); ?></p>
    </div>
    <div class="card">
        <h3>Базовые расходы</h3>
        <p><?php echo money_format_ru($base['expenses']); ?></p>
    </div>
</div>

<h2>Прогноз по сценарию</h2>
<div class="card-grid">
    <div class="card">
        <h3>Выручка</h3>
        <p><?php echo money_format_ru($new['revenue']); ?></p>
    </div>
    <div class="card">
        <h3>COGS</h3>
        <p><?php echo money_format_ru($new['cogs']); ?></p>
    </div>
    <div class="card">
        <h3>Валовая прибыль</h3>
        <p><?php echo money_format_ru($new['gross_profit']); ?></p>
    </div>
    <div class="card">
        <h3>Чистая прибыль</h3>
        <p><?php echo money_format_ru($new['net_profit']); ?></p>
    </div>
</div>
