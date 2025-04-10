<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<style>
    #loading {
        display: none;
        /* Sembunyikan awalnya */
        position: fixed;
        /* Tetap di tengah layar */
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        /* Biar di atas elemen lain */
        background: rgba(255, 255, 255, 0.7);
        /* Efek transparan */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }
</style>
<div class="container-fluid py-4">
    <!-- Filter Data / Form Pencarian -->
    <div class="row my-4">

        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">

            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">PPH: Per Style</h5>
                            </div>
                        </div>
                        <div>

                            <div class="d-flex align-items-center gap-3">
                                <input type="hidden" name="area" id="area" class="form-control" value="<?= $area ?>">
                                <input type="text" name="no_model" id="no_model" class="form-control" placeholder="Masukkan No Model">

                                <button type="button" id="searchModel" class="btn bg-gradient-info text-white">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Tampilkan Tabel Hanya Jika Data Tersedia -->

    <div class="row mt-3">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="row" id="HeaderRow">

                    </div>
                </div>
                <div class="card-body" id="bodyData">

                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-info text-center text-white" id="info" role="alert">
                Silakan masukkan Area & No Model untuk mencari data.
            </div>
        </div>
    </div>
    <div id="loading" style="display: none;">
        <h3>Sedang Menghitung...</h3>
        <img src="<?= base_url('assets/spinner.gif') ?>" alt="Loading...">
    </div>

    <!-- Notifikasi Flashdata -->
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <!-- Script untuk inisialisasi DataTable dan Chart.js -->
    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        let btnSearch = document.getElementById('searchModel');

        btnSearch.onclick = function() {
            let area = document.getElementById('area').value;
            let model = document.getElementById('no_model').value;
            let role = <?= json_encode($role) ?>;
            let loading = document.getElementById('loading');
            let info = document.getElementById('info');

            loading.style.display = 'block'; // T
            info.style.display = 'none'; // Sembunyikan loading setelah selesai


            $.ajax({
                url: "<?= base_url($role . '/pphinisial/') ?>" + area,
                type: "GET",
                data: {
                    model: model
                },
                dataType: "json",
                success: function(response) {
                    fethcData(response, model, area);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                },
                complete: function() {
                    loading.style.display = 'none'; // Sembunyikan loading setelah selesai
                }
            });
        };

        function fethcData(data, model, area) {
            let header = document.getElementById('HeaderRow');

            let baseUrl = "<?= base_url($role . '/excelPPHInisial/') ?>";

            header.innerHTML = `
            <div class="d-flex align-items-center justify-content-between">
                <h3>${model} || ${area}</h3>
                <a href="${baseUrl}${area}/${model}" id="exportExcel" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>`;

            let body = document.getElementById('bodyData')

            // Looping untuk buat baris tabel
            let rows = data.map((item, index) => `
    <tr>
        <td>${index + 1}</td>
        <td>${item.jarum}</td>
        <td>${item.inisial}</td>
        <td>${item.style_size}</td>
        <td>${item.item_type}</td>
        <td>${item.kode_warna}</td>
        <td>${item.color}</td>
        <td>${parseFloat(item.loss || 0).toFixed(2)}</td>
        <td>${parseFloat(item.composition || 0).toFixed(2)}</td>
        <td>${parseFloat(item.gw || 0).toFixed(2)}</td>
        <td>${(parseFloat(item.qty || 0) / 24).toFixed(2)}</td>
        <td>${parseFloat(item.kgs || 0).toFixed(2)}</td>
        <td>${(parseFloat(item.netto || 0) / 24).toFixed(2)}</td>
        <td>${parseFloat(item.bs_mesin || 0)}</td>
        <td>${(parseFloat(item.bs_setting || 0) / 24).toFixed(2)}</td>
        <td>${parseFloat(item.pph || 0).toFixed(2)}</td>
        <td>${parseFloat(item.pph_persen || 0).toFixed(2)} %</td>
    </tr>
`).join('');

            body.innerHTML = `
    <div class="table-responsive">
        <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Jarum</th>
                    <th class="text-center">Inisial</th>
                    <th class="text-center">Style Size</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Kode Warna</th>
                    <th class="text-center">Warna</th>
                    <th class="text-center">Loss (%)</th>
                    <th class="text-center">Komposisi (%)</th>
                    <th class="text-center">GW (gr)</th>
                    <th class="text-center">Qty PO (dz)</th>
                    <th class="text-center">Total Kebutuhan (kg)</th>
                    <th class="text-center">Netto (dz)</th>
                    <th class="text-center">Bs MC (gr)</th>
                    <th class="text-center">Bs Setting (dz)</th>
                    <th class="text-center">PPH (kg)</th>
                    <th class="text-center">PPH (%)</th>
                </tr>
            </thead>
            <tbody>
                ${rows}
            </tbody>
        </table>
    </div>
`;


            // Inisialisasi DataTables
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true
                });
            });
        }
    </script>
</div>
<?php $this->endSection(); ?>