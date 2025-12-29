<h1>Сброс пароля</h1>
<form method="post">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
    <div class="form-group">
        <label>Новый пароль</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Сохранить</button>
</form>
