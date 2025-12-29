<section class="hero-section">
    <div class="hero">
        <div class="hero-info">
            <span class="badge">Платформа для собственников кофеен</span>
            <h1>Финансовая аналитика, себестоимость и прибыль — в одном окне</h1>
            <p>Сервис CoffeeFin объединяет финансовый учёт, рецептуры, управляемую маржинальность и отчёты P&L. Всё работает из корня домена и подходит для shared-хостинга.</p>
            <div class="hero-actions">
                <a class="btn" href="index.php?route=auth/register">Запустить учёт</a>
                <a class="btn btn-secondary" href="index.php?route=subscription/plans">Смотреть тарифы</a>
            </div>
            <div class="hero-metrics">
                <div>
                    <strong>+12%</strong>
                    <span>рост маржи по напиткам</span>
                </div>
                <div>
                    <strong>−18%</strong>
                    <span>снижение списаний</span>
                </div>
                <div>
                    <strong>24/7</strong>
                    <span>контроль прибыли и ДДС</span>
                </div>
            </div>
        </div>
        <div class="hero-image">
            <h3>Что внутри</h3>
            <ul class="feature-list">
                <li>Себестоимость по рецептам и калькулятор цены</li>
                <li>Продажи, закупки, расходы и ДДС</li>
                <li>План/факт бюджеты и отчёты по сети кофеен</li>
                <li>Импорт CSV с валидацией</li>
            </ul>
        </div>
    </div>
</section>

<section class="info-section">
    <h2>Почему CoffeeFin помогает зарабатывать</h2>
    <div class="card-grid">
        <div class="card">
            <strong>Прозрачная себестоимость</strong>
            <p>Средневзвешенная стоимость ингредиентов и автоматический COGS на каждую продажу.</p>
        </div>
        <div class="card">
            <strong>Отчёты P&L и Cash Flow</strong>
            <p>Смотрите валовую прибыль, чистую прибыль и денежные потоки по каждому периоду.</p>
        </div>
        <div class="card">
            <strong>Маржинальность по напиткам</strong>
            <p>Определяйте, какие позиции дают прибыль, а какие съедают маржу.</p>
        </div>
        <div class="card">
            <strong>Сеть кофеен</strong>
            <p>Сводная аналитика по всем точкам и мониторинг низких остатков.</p>
        </div>
    </div>
</section>

<section class="info-section">
    <h2>Отзывы владельцев кофеен</h2>
    <div class="card-grid">
        <div class="card testimonial">
            <p>«Сразу увидели, что несколько напитков работали в минус. Подняли цены и маржа выросла на 10%»</p>
            <strong>Анна, сеть кофеен “Сова”</strong>
        </div>
        <div class="card testimonial">
            <p>«Отчёт P&L и ДДС — это то, чего нам не хватало. Теперь видно, сколько реально остаётся»</p>
            <strong>Денис, Coffee Lab</strong>
        </div>
        <div class="card testimonial">
            <p>«Импорт CSV сработал с первого раза, перенесли историю продаж за два года»</p>
            <strong>Мария, “Пенка”</strong>
        </div>
    </div>
</section>

<section class="info-section">
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
</section>

<section class="cta-section">
    <div class="cta-card">
        <h2>Запустите финансовый контроль за 15 минут</h2>
        <p>Создайте кофейню, добавьте рецепты и загрузите CSV — весь учёт в одном месте.</p>
        <a class="btn" href="index.php?route=auth/register">Начать бесплатно</a>
    </div>
</section>
