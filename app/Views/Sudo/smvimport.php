<form action="<?= base_url('sudo/importsmv') ?>" method="post" enctype="multipart/form-data">
    import smv
    <input type="file" name="excel_file" multiple accept=".xls, .xlsx">
    <button type="submit">Import</button>
</form>

<form action="<?= base_url('sudo/updateSisa') ?>" method="post" enctype="multipart/form-data">
    update sisa
    <input type="file" name="excel_file" multiple accept=".xls, .xlsx">
    <button type="submit">Import</button>
</form>