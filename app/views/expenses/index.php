<h1>Расходы</h1>
<a class="btn" href="index.php?route=expenses/create">Добавить расход</a>
<a class="btn btn-secondary" href="index.php?route=import/expenses">Импорт CSV</a>
<a class="btn btn-secondary" href="index.php?route=export/expenses">Экспорт XLS</a>
<table class="table">
    <thead>
        <tr>
            <th>Категория</th>
            <th>Сумма</th>
            <th>Дата</th>
            <th>Комментарий</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($expenses as $expense): ?>
            <tr>
                <td><?php echo htmlspecialchars($expense['category']); ?></td>
                <td><?php echo money_format_ru((float) $expense['amount']); ?></td>
                <td><?php echo htmlspecialchars($expense['spent_at']); ?></td>
                <td><?php echo htmlspecialchars($expense['note']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
