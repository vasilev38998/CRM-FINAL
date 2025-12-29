<h1>Новая продажа</h1>
<form method="post">
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
        <label>Количество</label>
        <input type="number" step="0.01" name="qty" required>
    </div>
    <div class="form-group">
        <label>Цена продажи</label>
        <input type="number" step="0.01" name="price" required>
    </div>
    <div class="form-group">
        <label>Дата</label>
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <button type="submit">Сохранить</button>
</form>
