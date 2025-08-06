<?php $this->extend('rosso/layout'); ?>
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
                    <div class="d-flex justify-content-between">
                        <div class="col-7">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                List Returan <?= $area ?>
                            </h5>
                        </div>
                        <div class="col-5 d-flex align-items-center text-end gap-2">
                            <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                            <input type="text" class="form-control" id="no_model" value="" placeholder="No Model">
                            <input type="date" class="form-control" id="tgl_buat" value="" required>
                            <button id="searchFilter" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
                            <!-- <button class="btn btn-info ms-2">
                                <a href="<?= base_url($role . '/generate_form_retur/' . $area) ?>" class="fa fa-list text-white" style="text-decoration: none;"> List</a>
                            </button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <!-- 2. Button Export -->
            <div class="d-flex align-items-center justify-content-end mb-3 d-none" id="exportButtons">
                <div class="d-flex gap-2">
                    <button id="generatePdfBtn" class="btn btn-danger"><i class="fas fa-file-pdf" target="_blank"></i> Export PDF</button>
                    <button id="generateExcelBtn" class="btn bg-gradient-success"><i class="fas fa-file-excel"></i> Export Excel</button>
                    <!-- <a id="exportExcelBtn" href="#" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a> -->
                </div>
            </div>
            <!-- 3. Tabel Retur -->
            <div class="table-responsive">
                <table id="dataTableRetur" class="table table-bordered text-center text-uppercase text-xs font-weight-bold" style="width:100%">
                    <thead>
                        <tr>
                            <th>Tanggal Retur</th>
                            <th>No Model</th>
                            <th>Item Type</th>
                            <th>Kode Warna</th>
                            <th>Warna</th>
                            <th>Lot Retur</th>
                            <th>KG Retur</th>
                            <th>Kategori</th>
                            <th>Keterangan GBN</th>
                        </tr>
                    </thead>
                    <tbody><!-- akan di‐populate via JS --></tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Inisialisasi DataTable dan simpan instancenya
        const dt = $('#dataTableRetur').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            data: <?= json_encode($list) ?>, // data awal dari PHP
            columns: [{
                    data: 'tgl_retur'
                },
                {
                    data: 'no_model'
                },
                {
                    data: 'item_type'
                },
                {
                    data: 'kode_warna'
                },
                {
                    data: 'warna'
                },
                {
                    data: 'lot_retur'
                },
                {
                    data: 'kgs_retur'
                },
                {
                    data: 'kategori'
                },
                {
                    data: 'keterangan_gbn'
                }
            ]
        });

        // Utility: update link Export PDF & Excel
        function updateExportLinks(area, model, tgl) {
            const basePdf = "<?= base_url($role . '/exportPdfRetur/') ?>";
            const baseExcel = "<?= base_url($role . '/generateFormRetur/') ?>";
            const baseXls = "<?= base_url($role . '/exportExcelRetur/') ?>";
            // contoh: /exportPdfRetur/AREA?model=XXX&tglBuat=YYYY-MM-DD
            $('#generatePdfBtn').off('click').on('click', () => {
                window.location = `${basePdf}${area}?model=${model}&tglBuat=${tgl}`;
            });
            $('#generateExcelBtn').off('click').on('click', () => {
                window.location = `${baseExcel}${area}?model=${model}&tglBuat=${tgl}`;
            });
            $('#exportExcelBtn').attr(
                'href',
                `${baseXls}${area}?model=${model}&tglBuat=${tgl}`
            );
        }

        // Default export link (tanpa filter)
        updateExportLinks("<?= $area ?>", "", "");

        // Tombol Filter
        $('#searchFilter').on('click', function() {
            const area = "<?= $area ?>";
            const model = $('#no_model').val().trim();
            const tgl = $('#tgl_buat').val();

            // Validasi sederhana
            if (!tgl) {
                alert('Tolong Isi Tgl Retur Terlebih Dahulu !');
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/listRetur/') ?>" + area,
                type: "GET",
                dataType: "json",
                data: {
                    noModel: model,
                    tglBuat: tgl
                },
                success: function(data) {
                    // 1) Kosongkan tabel
                    dt.clear();
                    // 2) Tambah data baru
                    dt.rows.add(data);
                    // 3) Gambar ulang
                    dt.draw();
                    // 4) Update link Export
                    updateExportLinks(area, model, tgl);
                    // 5) Tampilkan tombol Export
                    $('#exportButtons').removeClass('d-none');
                },
                error: function() {
                    alert('Gagal memuat data. Coba lagi.');
                }
            });
        });
    });
</script>
<?php $this->endSection(); ?>