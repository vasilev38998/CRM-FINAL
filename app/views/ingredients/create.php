<h1>Новый ингредиент</h1>
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
        <label>Единица измерения (г, мл, шт)</label>
        <input type="text" name="unit" required>
    </div>
    <div class="form-group">
        <label>Начальный остаток</label>
        <input type="number" step="0.01" name="qty" value="0">
    </div>
    <div class="form-group">
        <label>Средняя цена</label>
        <input type="number" step="0.01" name="price" value="0">
    </div>
    <div class="form-group">
        <label>Минимальный остаток</label>
        <input type="number" step="0.01" name="min_qty" value="0">
    </div>
    <button type="submit">Сохранить</button>
</form>
