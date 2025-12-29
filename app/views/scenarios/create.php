<h1>Новый сценарий</h1>
<form method="post">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label>Название сценария</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Изменение маржи (%)</label>
        <input type="number" step="0.1" name="margin_change" value="0">
    </div>
    <div class="form-group">
        <label>Изменение себестоимости (%)</label>
        <input type="number" step="0.1" name="cost_change" value="0">
    </div>
    <div class="form-group">
        <label>Изменение объёма продаж (%)</label>
        <input type="number" step="0.1" name="volume_change" value="0">
    </div>
    <button type="submit">Сохранить</button>
</form>
