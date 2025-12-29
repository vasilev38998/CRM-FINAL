<h1>Новый бюджет</h1>
<form method="post">
<<<<<<< HEAD
    <?php echo csrf_field(); ?>
=======
>>>>>>> origin/main
    <div class="form-group">
        <label>Название</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Дата начала</label>
        <input type="date" name="start" required>
    </div>
    <div class="form-group">
        <label>Дата окончания</label>
        <input type="date" name="end" required>
    </div>
    <button type="submit">Создать</button>
</form>
