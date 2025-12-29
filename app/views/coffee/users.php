<h1>Команда кофейни</h1>
<form method="post" action="index.php?route=coffee/users/add">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label>Email пользователя</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Роль</label>
        <select name="role" required>
            <option value="manager">Менеджер</option>
            <option value="accountant">Бухгалтер</option>
            <option value="owner">Владелец</option>
        </select>
    </div>
    <button type="submit">Добавить</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Имя</th>
            <th>Email</th>
            <th>Роль</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
