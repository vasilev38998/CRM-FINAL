<h1>Сезонность продаж</h1>
<table class="table">
    <thead>
        <tr>
            <th>Период</th>
            <th>Выручка</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['period']); ?></td>
                <td><?php echo money_format_ru((float) $row['revenue']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
