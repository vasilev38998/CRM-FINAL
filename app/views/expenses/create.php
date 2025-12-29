<h1>Новый расход</h1>
<form method="post">
<<<<<<< HEAD
    <?php echo csrf_field(); ?>
=======
>>>>>>> origin/main
    <div class="form-group">
        <label>Категория</label>
        <input type="text" name="category" required>
    </div>
    <div class="form-group">
        <label>Сумма</label>
        <input type="number" step="0.01" name="amount" required>
    </div>
    <div class="form-group">
        <label>Дата</label>
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="form-group">
        <label>Комментарий</label>
        <textarea name="note"></textarea>
    </div>
    <button type="submit">Сохранить</button>
</form>
