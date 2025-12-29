<h1>API токены</h1>
<form method="post">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label>Название токена</label>
        <input type="text" name="label" required>
    </div>
    <button type="submit">Создать токен</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Название</th>
            <th>Токен</th>
            <th>Дата создания</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tokens as $token): ?>
            <tr>
                <td><?php echo htmlspecialchars($token['label']); ?></td>
                <td><?php echo htmlspecialchars($token['token']); ?></td>
                <td><?php echo htmlspecialchars($token['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
