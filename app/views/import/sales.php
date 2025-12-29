<h1>Импорт продаж (CSV)</h1>
<p>Шаблон: <a href="assets/csv/sales_template.csv">скачать</a>. Формат: название напитка; количество; цена; дата.</p>
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
                <th>Напиток</th>
                <th>Количество</th>
                <th>Цена</th>
                <th>Дата</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['qty']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="post">
        <input type="hidden" name="action" value="import">
        <button type="submit">Импортировать</button>
    </form>
<?php endif; ?>
