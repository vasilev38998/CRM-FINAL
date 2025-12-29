<h1>Сброс пароля</h1>
<form method="post">
<<<<<<< HEAD
    <?php echo csrf_field(); ?>
=======
>>>>>>> origin/main
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
    <div class="form-group">
        <label>Новый пароль</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Сохранить</button>
</form>
