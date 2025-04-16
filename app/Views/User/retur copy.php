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
            <h3>Sedang memuat data...</h3>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    // Inisialisasi elemen yang diperlukan
    const btnSearch = document.getElementById('searchModel');

    btnSearch.addEventListener('click', function() {
        const area = document.getElementById('area').value;
        const model = document.getElementById('no_model').value;
        const role = <?= json_encode($role) ?>;
        const loading = document.getElementById('loading');
        const info = document.getElementById('info');

        console.log(`Area: ${area}`);
        console.log(`Model: ${model}`);

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
    });

    /**
     * Fungsi untuk membuat baris tabel dari data yang didapat
     * @param {Object} data - Data JSON respons dari AJAX
     * @returns {String} - HTML string untuk baris tabel
     */
    function buildTableRows(data, aggregateKeys) {
        let rows = '';
        let index = 0;
        // Iterasi tiap properti object dan lewati properti agregat
        for (const key in data) {
            if (aggregateKeys.includes(key)) continue;

            const item = data[key];
            const kgsOutVal = parseFloat(item.kgs_out);
            const validKgsOut = isNaN(kgsOutVal) ? '0' : kgsOutVal.toFixed(2);
            const estimasi = parseFloat(kgsOutVal - parseFloat(item.pph)).toFixed(2);

            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.no_model}</td>
                    <td>${item.area}</td>
                    <td>${item.item_type}</td>
                    <td>${item.kode_warna}</td>
                    <td>${parseFloat(item.ttl_kebutuhan).toFixed(2)} kg</td>
                    <td>${parseFloat(item.pph).toFixed(2)} kg</td>
                    <td>${validKgsOut} kg</td>
                    <td>${estimasi} kg</td>
                </tr>
            `;
            index++;
        }
        return rows;
    }

    /**
     * Fungsi untuk membuat input list pada modal dari item dengan estimasi > 0
     * @param {Object} data - Data JSON respons dari AJAX
     * @returns {String} - HTML string untuk input list di modal
     */
    function buildModalInputs(data, aggregateKeys) {
        let inputs = '';
        let i = 0;

        for (const key in data) {
            if (aggregateKeys.includes(key)) continue;

            const item = data[key];
            const kgsOutVal = parseFloat(item.kgs_out);
            const pphVal = parseFloat(item.pph);
            const estimasi = parseFloat(kgsOutVal - pphVal).toFixed(2);

            if (kgsOutVal > 0) {
                inputs += `
                <div class="mb-4 p-3 border rounded shadow-sm bg-white">
                    <h6 class="mb-3">${i + 1}. <strong>${item.item_type} | ${item.kode_warna} | ${item.warna}</strong></h6>
                    <input type="hidden" name="items[${i}][no_model]" value="${item.no_model}">
                    <input type="hidden" name="items[${i}][area]" value="${item.area}">
                    <input type="hidden" name="items[${i}][item_type]" value="${item.item_type}">
                    <input type="hidden" name="items[${i}][kode_warna]" value="${item.kode_warna}">
                    <input type="hidden" name="items[${i}][warna]" value="${item.warna}">
                    <input type="hidden" name="items[${i}][lot]" value="${item.lot_out}">

                    <div class="row">
                        <div class="col-md-4">
                            <label for="kgs_${i}" class="form-label">Jml KGS</label>
                            <div class="input-group">
                            <input type="number" step="0.01" class="form-control" name="items[${i}][kgs]" id="kgs_${i}" value="${estimasi}" required>
                            <span class="input-group-text text-bold">KG</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="cones_${i}" class="form-label">Jml Cones</label>
                            <div class="input-group">
                            <input type="number" class="form-control" name="items[${i}][cones]" id="cones_${i}" required>
                            <span class="input-group-text text-bold">CNS</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="karung_${i}" class="form-label">Jml Karung</label>
                            <div class="input-group">
                            <input type="number" class="form-control" name="items[${i}][karung]" id="karung_${i}">
                            <span class="input-group-text text-bold">KRG</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                i++;
            }
        }

        return inputs;
    }


    /**
     * Fungsi utama untuk merender data ke dalam tabel dan modal
     * @param {Object} data - Data JSON respons dari AJAX
     * @param {String} model - Nomor model
     * @param {String} area - Nama area
     */
    function fetchData(data, model, area) {
        const aggregateKeys = ["qty", "sisa", "bruto", "bs_setting", "bs_mesin"];
        // tanggal hari ini
        const today = new Date();
        // Render Header & Modal
        const baseUrl = "<?= base_url($role . '/retur/') ?>";
        const headerContainer = document.getElementById('HeaderRow');
        headerContainer.innerHTML = `
            <div class="header-container">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <h3 class="model-title mb-0">${model}</h3>
                    <div class="d-flex align-items-center">
                        <a href="${baseUrl}${area}/exportExcel" class="btn btn-success me-2" id="btnExportExcel">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalPengajuanRetur">
                            <i class="fas fa-paper-plane"></i> Pengajuan Retur
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalPengajuanRetur" tabindex="-1" aria-labelledby="modalPengajuanReturLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-xl">
                    <form action="${baseUrl}${area}/pengajuanRetur" method="POST" id="formPengajuanRetur">
                        <input type="hidden" name="model" value="${model}">
                        <input type="hidden" name="area" value="${area}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalPengajuanReturLabel">Pengajuan Retur ${today.toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' })}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3 row">
                                    <div class="col-sm-6">
                                        <label class="col-sm-2 col-form-label">No Model</label>
                                            <input type="text" class="form-control" name="model" value="${model}" readonly>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-sm-2 col-form-label">Area</label>
                                            <input type="text" class="form-control" name="area" value="${area}" readonly>
                                    </div>
                                </div>
                                <hr>
                                <div id="listReturInputs">
                                    ${buildModalInputs(data, aggregateKeys)}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-info w-100"><i class="fas fa-paper-plane"></i> Ajukan Retur</button>
                                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        `;

        // Render Tabel Data
        const tableBody = document.getElementById('bodyData');
        tableBody.innerHTML = `
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
                            <th class="text-center">Kirim</th>
                            <th class="text-center">Estimasi Retur</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${buildTableRows(data, aggregateKeys)}
                    </tbody>
                </table>
            </div>
        `;

        // Inisialisasi DataTables (pastikan DataTables sudah terinclude)
        $(document).ready(function() {
            $('#dataTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        });
    }
</script>


<?php $this->endSection(); ?>