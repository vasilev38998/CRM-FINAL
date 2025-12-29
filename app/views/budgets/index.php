<h1>Бюджеты</h1>
<a class="btn" href="index.php?route=budgets/create">Создать бюджет</a>
<table class="table">
    <thead>
        <tr>
            <th>Название</th>
            <th>Период</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($budgets as $budget): ?>
            <tr>
                <td><?php echo htmlspecialchars($budget['name']); ?></td>
                <td><?php echo htmlspecialchars($budget['period_start']); ?> — <?php echo htmlspecialchars($budget['period_end']); ?></td>
                <td>
                    <a class="btn btn-secondary" href="index.php?route=budgets/edit&id=<?php echo (int) $budget['id']; ?>">Статьи</a>
                    <a class="btn" href="index.php?route=budgets/report&id=<?php echo (int) $budget['id']; ?>">Отчёт план/факт</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
