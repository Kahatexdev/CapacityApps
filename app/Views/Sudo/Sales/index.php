<?php $this->extend('Sudo/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Sales Position</h3>
                    <a href="<?= base_url('sudo/exportsales') ?>" class="btn btn-info"> Generate report</a>
                </div>
                <div class="card-body p-3 ">

                </div>
            </div>
        </div>
    </div>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#tabel').DataTable({});
    });
</script>
<?php $this->endSection(); ?>