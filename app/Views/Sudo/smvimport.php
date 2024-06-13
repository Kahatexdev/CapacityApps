<form action="<?= base_url('sudo/importsmv') ?>" method="post" enctype="multipart/form-data">
    <input type="file" name="excel_file" multiple accept=".xls, .xlsx">
    <button type="submit">Import</button>
</form>