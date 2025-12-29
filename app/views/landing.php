<h1>SaaS для финансового учёта кофейни</h1>
<div class="hero">
    <div class="hero-info">
        <p>Управляйте себестоимостью, продажами и прибылью в одном сервисе. Средневзвешенная себестоимость, P&L отчёты, импорт CSV и платный доступ без автопродления.</p>
        <div class="card-grid">
            <div class="card">
                <strong>Финансовый учёт</strong>
                <p>Доходы, расходы и прибыль в рублях.</p>
            </div>
            <div class="card">
                <strong>Себестоимость</strong>
                <p>Рецепты и контроль маржинальности.</p>
            </div>
            <div class="card">
                <strong>Подписка</strong>
                <p>Разовая оплата за период, без автопродления.</p>
            </div>
        </div>
    </div>
    <div class="hero-image">
        <h3>Готовы попробовать?</h3>
        <p>7-дневный Trial или полный доступ на 30 дней.</p>
        <a class="btn" href="index.php?route=auth/register">Начать работу</a>
    </div>
</div>

<h2>Тарифы</h2>
<div class="card-grid">
    <?php foreach ($plans as $plan): ?>
        <div class="card">
            <span class="badge"><?php echo htmlspecialchars($plan['duration_days']); ?> дней</span>
            <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
            <p><?php echo htmlspecialchars($plan['description']); ?></p>
            <p><strong><?php echo money_format_ru((float) $plan['price']); ?></strong></p>
            <a class="btn" href="index.php?route=subscription/plans">Выбрать</a>
        </div>
    <?php endforeach; ?>
</div>
