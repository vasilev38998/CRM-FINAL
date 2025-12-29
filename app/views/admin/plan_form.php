<h1><?php echo $plan ? 'Редактирование тарифа' : 'Новый тариф'; ?></h1>
<form method="post">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label>Название</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($plan['name'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label>Цена (₽)</label>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($plan['price'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label>Длительность (дни)</label>
        <input type="number" name="duration_days" value="<?php echo htmlspecialchars($plan['duration_days'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label>Описание</label>
        <textarea name="description"><?php echo htmlspecialchars($plan['description'] ?? ''); ?></textarea>
    </div>
    <button type="submit">Сохранить</button>
</form>
