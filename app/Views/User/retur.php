<?php $this->extend('user/layout'); ?>
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
                        <div class="col-9">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $title . ' ' . $area ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-3 d-flex align-items-center text-end gap-2">
                            <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                            <input type="text" class="form-control" id="no_model" value="" placeholder="No Model">
                            <button id="searchModel" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="resultContainer">
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
                    Silakan masukkan No Model untuk mencari data.
                </div>
            </div>
        </div>
        <div id="loading" style="display: none;">
            <h3>Sedang Menghitung...</h3>
        </div>
    </div>
</div>
<div class="row my-3">

</div>


</div>


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
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    let btnSearch = document.getElementById('searchModel');

    btnSearch.onclick = function() {
        let area = document.getElementById('area').value;
        let model = document.getElementById('no_model').value;
        let role = <?= json_encode($role) ?>;
        let loading = document.getElementById('loading');
        let info = document.getElementById('info');

        console.log("Area: " + area);
        console.log("Model: " + model);

        loading.style.display = 'block';
        info.style.display = 'none';

        $.ajax({
            url: "<?= base_url($role . '/filterRetur/') ?>" + area,
            type: "GET",
            data: {
                model: model
            },
            dataType: "json",
            success: function(response) {
                fetchData(response, model, area);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            },
            complete: function() {
                loading.style.display = 'none';
            }
        });
    };

    function fetchData(data, model, area) {
        // Ambil nilai totalQty dari properti agregat "qty"
        let totalQty = data.qty ? parseFloat(data.qty) : 0;
        let totalKgsOut = 0;
        let rows = "";
        let index = 0;

        // Daftar properti agregat yang tidak perlu diiterasi sebagai item
        let aggregateKeys = ["qty", "sisa", "bruto", "bs_setting", "bs_mesin"];

        // Iterasi tiap properti pada object data
        for (let key in data) {
            // Lewati properti agregat
            if (aggregateKeys.includes(key)) {
                continue;
            }
            let item = data[key];
            // Pastikan field numeric pada kgs_out dapat diparse, jika bukan angka maka diabaikan
            let kgsOutVal = parseFloat(item.kgs_out);
            if (!isNaN(kgsOutVal)) {
                totalKgsOut += kgsOutVal;
            }

            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.no_model}</td>
                    <td>${item.area}</td>
                    <td>${item.item_type}</td>
                    <td>${item.kode_warna}</td>
                    <td>${parseFloat(item.ttl_kebutuhan).toFixed(2)} kg</td>
                    <td>${parseFloat(item.pph).toFixed(2)}</td>
                    <td>${isNaN(kgsOutVal) ? '-' : kgsOutVal.toFixed(2)} kg</td>
                </tr>
            `;
            index++;
        }

        // Update header dengan info total dan link export Excel
        let header = document.getElementById('HeaderRow');
        let baseUrl = "<?= base_url($role . '/#/') ?>";
        header.innerHTML = `
            <div class="header-container mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="model-title mb-0">${model}</h3>
                    <a href="${baseUrl}${area}/${model}" id="exportExcel" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>
        `;

        // Update tabel body dengan hasil iterasi
        let body = document.getElementById('bodyData');
        body.innerHTML = `
            <div class="table-responsive">
                <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">No Model</th>
                            <th class="text-center">Area</th>
                            <th class="text-center">Item Type</th>
                            <th class="text-center">Kode Warna</th>
                            <th class="text-center">PO (KGS)</th>
                            <th class="text-center">PPH</th>
                            <th class="text-center">KGS Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows}
                    </tbody>
                </table>
            </div>`;

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


<?php $this->endSection(); ?>