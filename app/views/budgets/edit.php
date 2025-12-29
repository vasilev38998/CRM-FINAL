<h1>Бюджет: <?php echo htmlspecialchars($budget['name']); ?></h1>
<p>Период: <?php echo htmlspecialchars($budget['period_start']); ?> — <?php echo htmlspecialchars($budget['period_end']); ?></p>
<form method="post">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label>Тип статьи</label>
        <select name="type" required>
            <option value="income">Доход</option>
            <option value="expense">Расход</option>
        </select>
    </div>
    <div class="form-group">
        <label>Категория</label>
        <input type="text" name="category" required>
    </div>
    <div class="form-group">
        <label>Плановая сумма</label>
        <input type="number" step="0.01" name="amount" required>
    </div>
    <button type="submit">Добавить статью</button>
</form>

<h2>Статьи бюджета</h2>
<table class="table">
    <thead>
        <tr>
            <th>Тип</th>
            <th>Категория</th>
            <th>План</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo $item['type'] === 'income' ? 'Доход' : 'Расход'; ?></td>
                <td><?php echo htmlspecialchars($item['category']); ?></td>
                <td><?php echo money_format_ru((float) $item['amount_plan']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
