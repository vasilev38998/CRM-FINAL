<h1>Журнал действий</h1>
<table class="table">
    <thead>
        <tr>
            <th>Дата</th>
            <th>Пользователь</th>
            <th>Сущность</th>
            <th>Действие</th>
            <th>Данные</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                <td><?php echo htmlspecialchars($log['email'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($log['entity']); ?></td>
                <td><?php echo htmlspecialchars($log['action']); ?></td>
                <td><?php echo htmlspecialchars($log['payload']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
