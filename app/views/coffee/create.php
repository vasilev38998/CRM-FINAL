<h1>Создание кофейни</h1>
<form method="post">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label>Название кофейни</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Город</label>
        <input type="text" name="city">
    </div>
    <button type="submit">Создать</button>
</form>
