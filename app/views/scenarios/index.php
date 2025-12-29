<h1>Сценарии</h1>
<a class="btn" href="index.php?route=scenarios/create">Создать сценарий</a>
<table class="table">
    <thead>
        <tr>
            <th>Название</th>
            <th>Маржа %</th>
            <th>Себестоимость %</th>
            <th>Объём %</th>
            <th>Отчёт</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($scenarios as $scenario): ?>
            <tr>
                <td><?php echo htmlspecialchars($scenario['name']); ?></td>
                <td><?php echo number_format((float) $scenario['margin_change'], 2, ',', ' '); ?></td>
                <td><?php echo number_format((float) $scenario['cost_change'], 2, ',', ' '); ?></td>
                <td><?php echo number_format((float) $scenario['volume_change'], 2, ',', ' '); ?></td>
                <td><a class="btn" href="index.php?route=scenarios/report&id=<?php echo (int) $scenario['id']; ?>">Смотреть</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
