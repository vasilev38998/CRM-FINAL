<h1>Статус системы</h1>
<table class="table">
    <thead>
        <tr>
            <th>Параметр</th>
            <th>Значение</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Версия PHP</td>
            <td><?php echo htmlspecialchars($status['php_version']); ?></td>
        </tr>
        <tr>
            <td>База данных</td>
            <td><?php echo htmlspecialchars($status['db_status']); ?></td>
        </tr>
        <tr>
            <td>Запись в uploads</td>
            <td><?php echo $status['uploads_writable'] ? 'OK' : 'Нет прав'; ?></td>
        </tr>
        <tr>
            <td>Запись в storage</td>
            <td><?php echo $status['storage_writable'] ? 'OK' : 'Нет прав'; ?></td>
        </tr>
    </tbody>
</table>
