<h1>Новая закупка</h1>
<form method="post">
<<<<<<< HEAD
    <?php echo csrf_field(); ?>
=======
>>>>>>> origin/main
    <div class="form-group">
        <label>Ингредиент</label>
        <select name="ingredient_id" required>
            <option value="">Выберите</option>
            <?php foreach ($ingredients as $ingredient): ?>
                <option value="<?php echo (int) $ingredient['id']; ?>"><?php echo htmlspecialchars($ingredient['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Количество</label>
        <input type="number" step="0.01" name="qty" required>
    </div>
    <div class="form-group">
        <label>Цена за единицу</label>
        <input type="number" step="0.01" name="price" required>
    </div>
    <div class="form-group">
        <label>Дата</label>
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <button type="submit">Сохранить</button>
</form>
