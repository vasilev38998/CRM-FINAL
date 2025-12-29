<h1>Движение денежных средств</h1>
<form method="get">
    <input type="hidden" name="route" value="cash">
    <div class="form-group">
        <label>Период с</label>
        <input type="date" name="from" value="<?php echo htmlspecialchars($start); ?>">
    </div>
    <div class="form-group">
        <label>По</label>
        <input type="date" name="to" value="<?php echo htmlspecialchars($end); ?>">
    </div>
    <button type="submit">Применить</button>
    <a class="btn" href="index.php?route=cash/create">Добавить операцию</a>
</form>

<div class="card-grid">
    <div class="card">
        <h3>Приток</h3>
        <p><?php echo money_format_ru($inflow); ?></p>
    </div>
    <div class="card">
        <h3>Отток</h3>
        <p><?php echo money_format_ru($outflow); ?></p>
    </div>
    <div class="card">
        <h3>Чистый поток</h3>
        <p><?php echo money_format_ru($net); ?></p>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Дата</th>
            <th>Тип</th>
            <th>Источник</th>
            <th>Категория</th>
            <th>Сумма</th>
            <th>Комментарий</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo htmlspecialchars($transaction['transacted_at']); ?></td>
                <td><?php echo $transaction['type'] === 'in' ? 'Приход' : 'Расход'; ?></td>
                <td><?php echo $transaction['source'] === 'cash' ? 'Касса' : 'Банк'; ?></td>
                <td><?php echo htmlspecialchars($transaction['category']); ?></td>
                <td><?php echo money_format_ru((float) $transaction['amount']); ?></td>
                <td><?php echo htmlspecialchars($transaction['note']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
