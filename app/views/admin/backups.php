<h1>Резервные копии</h1>
<form method="post" action="index.php?route=admin/backups/create">
    <?php echo csrf_field(); ?>
    <button type="submit">Создать бэкап</button>
</form>

<form method="post" action="index.php?route=admin/backups/restore" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label>Восстановить из файла SQL</label>
        <input type="file" name="sql_file" accept=".sql" required>
    </div>
    <button type="submit">Восстановить</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Файл</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($files as $file): ?>
            <tr>
                <td><?php echo htmlspecialchars($file); ?></td>
                <td><a class="btn" href="index.php?route=admin/backups/download&file=<?php echo urlencode($file); ?>">Скачать</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
