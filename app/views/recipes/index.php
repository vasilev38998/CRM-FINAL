<h1>Рецепты</h1>
<a class="btn" href="index.php?route=recipes/create">Добавить ингредиент в рецепт</a>
<table class="table">
    <thead>
        <tr>
            <th>Напиток</th>
            <th>Ингредиент</th>
            <th>Кол-во</th>
            <th>Себестоимость</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recipes as $recipe): ?>
            <tr>
                <td><?php echo htmlspecialchars($recipe['product_name']); ?></td>
                <td><?php echo htmlspecialchars($recipe['ingredient_name']); ?></td>
                <td><?php echo number_format((float) $recipe['qty'], 2, ',', ' '); ?></td>
                <td><?php echo money_format_ru((float) $recipe['qty'] * (float) $recipe['avg_price']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
