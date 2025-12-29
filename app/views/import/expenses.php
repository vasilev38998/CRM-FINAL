<h1>Импорт расходов (CSV)</h1>
<p>Шаблон: <a href="assets/csv/expenses_template.csv">скачать</a>. Формат: категория; сумма; дата; комментарий.</p>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <div><?php echo htmlspecialchars($error); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="preview">
    <div class="form-group">
        <label>CSV файл</label>
        <input type="file" name="csv_file" accept=".csv" required>
    </div>
    <button type="submit">Предпросмотр</button>
</form>

<?php if ($rows): ?>
    <h2>Предпросмотр</h2>
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
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['note']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="post">
        <input type="hidden" name="action" value="import">
        <button type="submit">Импортировать</button>
    </form>
<?php endif; ?>
