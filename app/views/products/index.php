<h1>Напитки</h1>
<a class="btn" href="index.php?route=products/create">Добавить напиток</a>
<table class="table">
    <thead>
        <tr>
            <th>Название</th>
            <th>Цена продажи</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo money_format_ru((float) $product['price_sell']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
