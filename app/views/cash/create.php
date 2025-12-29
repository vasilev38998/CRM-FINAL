<h1>Новая операция ДДС</h1>
<form method="post">
    <div class="form-group">
        <label>Тип операции</label>
        <select name="type" required>
            <option value="in">Приход</option>
            <option value="out">Расход</option>
        </select>
    </div>
    <div class="form-group">
        <label>Источник</label>
        <select name="source" required>
            <option value="cash">Касса</option>
            <option value="bank">Банк</option>
        </select>
    </div>
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
