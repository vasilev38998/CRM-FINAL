<h1>План/факт: <?php echo htmlspecialchars($budget['name']); ?></h1>
<p>Период: <?php echo htmlspecialchars($budget['period_start']); ?> — <?php echo htmlspecialchars($budget['period_end']); ?></p>
<table class="table">
    <thead>
        <tr>
            <th>Тип</th>
            <th>Категория</th>
            <th>План</th>
            <th>Факт</th>
            <th>Отклонение</th>
            <th>%</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($report as $row): ?>
            <tr>
                <td><?php echo $row['type'] === 'income' ? 'Доход' : 'Расход'; ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo money_format_ru($row['plan']); ?></td>
                <td><?php echo money_format_ru($row['fact']); ?></td>
                <td><?php echo money_format_ru($row['diff']); ?></td>
                <td><?php echo number_format($row['percent'], 2, ',', ' '); ?> %</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
