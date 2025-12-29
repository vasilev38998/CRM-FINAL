<h1>ABC/XYZ анализ</h1>
<table class="table">
    <thead>
        <tr>
            <th>Напиток</th>
            <th>Выручка</th>
            <th>Доля %</th>
            <th>ABC</th>
            <th>XYZ</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo money_format_ru($item['revenue']); ?></td>
                <td><?php echo number_format($item['share'], 2, ',', ' '); ?> %</td>
                <td><?php echo htmlspecialchars($item['abc']); ?></td>
                <td><?php echo htmlspecialchars($item['xyz']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
