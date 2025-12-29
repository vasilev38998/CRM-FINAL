<h1>Регистрация</h1>
<form method="post">
<<<<<<< HEAD
    <?php echo csrf_field(); ?>
=======
>>>>>>> origin/main
    <div class="form-group">
        <label>Имя</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Пароль</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Создать аккаунт</button>
</form>
