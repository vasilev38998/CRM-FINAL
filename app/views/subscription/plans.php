<h1>Тарифы и доступ</h1>
<div class="card-grid">
    <?php foreach ($plans as $plan): ?>
        <div class="card">
            <span class="badge"><?php echo htmlspecialchars($plan['duration_days']); ?> дней</span>
            <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
            <p><?php echo htmlspecialchars($plan['description']); ?></p>
            <p><strong><?php echo money_format_ru((float) $plan['price']); ?></strong></p>
            <?php if (current_user()): ?>
                <a class="btn" href="index.php?route=subscription/pay&plan_id=<?php echo (int) $plan['id']; ?>">Оплатить</a>
            <?php else: ?>
                <a class="btn" href="index.php?route=auth/register">Зарегистрироваться</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
