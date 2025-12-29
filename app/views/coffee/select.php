<h1>Выбор кофейни</h1>
<?php if (!$shops): ?>
    <p>У вас ещё нет кофеен.</p>
    <a class="btn" href="index.php?route=coffee/create">Создать кофейню</a>
<?php else: ?>
    <form method="post">
        <div class="form-group">
            <label>Кофейня</label>
            <select name="coffee_shop_id">
                <?php foreach ($shops as $shop): ?>
                    <option value="<?php echo (int) $shop['id']; ?>"><?php echo htmlspecialchars($shop['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Выбрать</button>
    </form>
<?php endif; ?>
