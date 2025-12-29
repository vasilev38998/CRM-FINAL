<h1>Вход</h1>
<form method="post">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Пароль</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Войти</button>
    <a class="btn btn-secondary" href="index.php?route=auth/forgot">Забыли пароль?</a>
</form>
