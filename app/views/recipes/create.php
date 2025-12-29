<h1>Добавление ингредиента в рецепт</h1>
<form method="post">
<<<<<<< HEAD
    <?php echo csrf_field(); ?>
=======
>>>>>>> origin/main
    <div class="form-group">
        <label>Напиток</label>
        <select name="product_id" required>
            <option value="">Выберите</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo (int) $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
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
        <label>Количество на 1 напиток</label>
        <input type="number" step="0.01" name="qty" required>
    </div>
    <button type="submit">Сохранить</button>
</form>
