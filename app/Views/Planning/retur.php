<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<style>
    #loading {
        display: none;
        /* Sembunyikan awalnya */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: rgba(255, 255, 255, 0.7);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .input-group-text {
        position: static !important;
        z-index: auto !important;
    }
</style>
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
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row d-flex align-items-center">
                        <div class="col-10">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $title ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <form action="<?= base_url($role . '/retur') ?>" method="get" class="d-flex justify-content-end align-items-center gap-2">
                                <select name="area" id="area" class="form-control">
                                    <option value="">Pilih Area</option>
                                    <?php foreach ($areas as $ar) : ?>
                                        <option value="<?= $ar ?>"><?= $ar ?></option>
                                    <?php endforeach ?>
                                </select>
                                <!-- <input type="text" class="form-control" id="no_model" value="" placeholder="No Model"> -->
                                <button type="submit" id="searchModel" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">


            <div class="d-flex align-items-center justify-content-between">
                <h3 class="model-title mb-0">List Returan <?= $area ?></h3>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= base_url($role . '/exportExcelRetur/' . $area) ?>" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>

                </div>
            </div>
            <div class="table-responsive">
                <table class="table display text-center text-uppercase text-xs font-bolder table-bordered" id="dataTableRetur" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal Retur</th>
                            <th class="text-center">No Model</th>
                            <th class="text-center">Item Type</th>
                            <th class="text-center">Kode Warna</th>
                            <th class="text-center">Warna</th>
                            <th class="text-center">PO (Kg)</th>
                            <th class="text-center">PO+ (Kg)</th>
                            <th class="text-center">BS (Kg)</th>
                            <th class="text-center">Kirim (Kg)</th>
                            <th class="text-center">Pakai (Kg)</th>
                            <th class="text-center">Lot Retur</th>
                            <th class="text-center">KG Retur</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Keterangan GBN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $ls): ?>
                            <tr>
                                <td><?= $ls['tgl_retur'] ?></td>
                                <td><?= $ls['no_model'] ?></td>
                                <td><?= $ls['item_type'] ?></td>
                                <td><?= $ls['kode_warna'] ?></td>
                                <td><?= $ls['warna'] ?></td>
                                <td><?= round($ls['total_kg_po'] ?? 0, 2) ?></td>
                                <td><?= round($ls['ttl_tambahan_kg'] ?? 0, 2) ?></td>
                                <td><?= round(($ls['total_bs_mc_kg'] ?? 0) + ($ls['total_bs_st_kg'] ?? 0), 2) ?></td>
                                <td><?= round($ls['total_kgs_out'] ?? 0, 2) ?></td>
                                <!-- Total Pakai KG (opsional, bisa disesuaikan rumusnya) -->
                                <td><?= round(($ls['total_kg_po'] ?? 0) + ($ls['total_bs_mc_kg'] ?? 0) + ($ls['total_bs_st_kg'] ?? 0) - ($ls['total_kgs_out'] ?? 0), 2) ?></td>
                                <td><?= $ls['lot_retur'] ?></td>
                                <td><?= $ls['kgs_retur'] ?></td>
                                <td><?= $ls['kategori'] ?></td>
                                <td><?= $ls['keterangan_gbn'] ?></td>

                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<div id="loading">
    <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2">Sedang memuat data...</p>
</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    // Inisialisasi DataTables (pastikan plugin DataTables sudah disertakan)
    $(document).ready(function() {
        $('#dataTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true
        });
        $('#dataTableRetur').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true
        });
        // Saat form filter dikirim, tampilkan loader
        $("form").on("submit", function() {
            $("#loading").fadeIn();
        });

        // Opsional: jika page sudah selesai load, sembunyikan loader
        $(window).on("load", function() {
            $("#loading").fadeOut();
        });
    });
</script>
<?php $this->endSection(); ?>