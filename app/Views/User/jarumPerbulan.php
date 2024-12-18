<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card pb-0">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Penggunaan Jarum bulan <?= $month ?></h5>
                        <a href="<?= base_url('excelPenggunaanPerbulan/' . $area . '/' . $month) ?>" class="btn btn-success">Export</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTable0" class="display  striped" style="width:100%">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Nama</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jarum as $jrm) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $jrm['tanggal']; ?></td>
                                            <td class="text-sm"><?= $jrm['nama_karyawan']; ?></td>
                                            <td class="text-sm"><?= $jrm['total_jarum']; ?> pcs</td>
                                            <td class="text-sm"><?= $jrm['area']; ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>



</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    // AJAX form submission
    $(document).ready(function() {
        $('#dataTable0').DataTable({
            "order": [
                [0, "desc"]
            ]
        });
        $('#dataTable').DataTable({});


    });
</script>

<?php $this->endSection(); ?>