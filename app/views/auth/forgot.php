<h1>Восстановление пароля</h1>
<p>Введите email. Система сформирует ссылку для сброса (пока показывается на экране).</p>
<form method="post">
<<<<<<< HEAD
    <?php echo csrf_field(); ?>
=======
>>>>>>> origin/main
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <button type="submit">Получить ссылку</button>
</form>
