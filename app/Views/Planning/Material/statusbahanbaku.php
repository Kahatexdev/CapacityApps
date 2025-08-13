<?php $this->extend($role . '/statusbahanbaku/header'); ?>

<?php $this->section('content'); ?>

<style>
    .table-wrapper {
        overflow-x: auto;
        position: relative;
        /* penting biar sticky posisi relatif ke wrapper */
    }

    .table-freeze th,
    .table-freeze td {
        white-space: nowrap;
        background: white;
        /* biar gak transparan pas scroll */
        z-index: 1;
    }

    /* Kolom pertama */
    .table-freeze .sticky-col {
        position: sticky;
        left: 0;
        z-index: 2;
        background: white;
    }

    /* Kolom kedua */
    .table-freeze .sticky-col-2 {
        position: sticky;
        left: 120px;
        /* sesuaikan lebar kolom pertama */
        z-index: 2;
        background: white;
    }

    /* Kolom ketiga */
    .table-freeze .sticky-col-3 {
        position: sticky;
        left: 240px;
        /* lebar kolom pertama + kedua */
        z-index: 2;
        background: white;
    }

    /* Header di sticky kolom */
    .table-freeze thead th.sticky-col,
    .table-freeze thead th.sticky-col-2,
    .table-freeze thead th.sticky-col-3 {
        z-index: 3;
        /* biar header di atas data */
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
                        <div class="col-6">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Status Bahan Baku
                                </h5>
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-center text-end gap-2">
                            <input type="text" class="form-control" id="model" value="" placeholder="No Model" required>
                            <input type="text" class="form-control" id="filter" value="" placeholder="Kode Warna/Lot">
                            <button id="filterButton" class="btn btn-info ms-2" disabled><i class="fas fa-search"></i></button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="resultContainer">
    </div>
</div>
<div class="row my-3">

</div>


</div>


</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    const modelInput = document.getElementById('model');
    const filterInput = document.getElementById('filter');
    const filterButton = document.getElementById('filterButton');

    // Aktifkan tombol saat field model tidak kosong
    modelInput.addEventListener('input', function() {
        filterButton.disabled = modelInput.value.trim() === '';
    });

    filterButton.addEventListener('click', function() {
        let keyword = filterInput.value.trim();
        let model = modelInput.value.trim();

        let apiUrl = `<?= base_url($role . '/filterstatusbahanbaku') ?>/${model}?search=${keyword}`;

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                console.log('Filtered Data:', data);
                displayData(data);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    });



    function displayData(data) {
        let resultContainer = document.getElementById('resultContainer');
        resultContainer.innerHTML = '';

        // console.log();
        // const dataStatus = [];

        // if (!Array.isArray(data['status'])) {
        //     // Mengubah objek menjadi array
        //     dataStatus = Object.values(data['status']);
        // }

        // Pastikan data['status'] ada dan berbentuk array
        let dataStatus = Array.isArray(data['status']) ? data['status'] : [];

        if (dataStatus.length === 0) {
            resultContainer.innerHTML = '<p class="text-center text-muted">No data found</p>';
            return;
        }

        resultContainer.innerHTML += `
            <div class="row my-4">
                <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row d-flex align-items-center justify-content-center">
                              <div class="col-2 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">No Model</p>
                                        <h5 class="font-weight-bolder mb-0">${data['master']['no_model'] ?? '-' }</h5>
                                    </div>
                                </div>
                                <div class="col-2 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Buyer</p>
                                        <h5 class="font-weight-bolder mb-0">${data['master']['kd_buyer_order']}</h5>
                                    </div>
                                </div>
                                <div class="col-2 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Delivery Awal</p>
                                        <h5 class="font-weight-bolder mb-0">${data['master']['delivery_awal']}</h5>
                                    </div>
                                </div>
                                <div class="col-2 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Delivery Akhir</p>
                                        <h5 class="font-weight-bolder mb-0">${data['master']['delivery_akhir']}</h5>
                                    </div>
                                </div>
                              <div class="col-2 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Start MC</p>
                                        <h5 class="font-weight-bolder mb-0">${data['master']['start_mc'] ?? '-' }</h5>
                                    </div>
                                </div>
                                <div class="col-2 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                     <a class="btn btn-success" href='http://172.23.44.14/MaterialSystem/public/api/apiexportGlobalReport/${model}'>Excel Bahan Baku</a>

                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        let htmlCelupHeader = `
<div class="row my-4">
    <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <h5 class="mt-4">🧵 Status CELUP (Benang & Nylon)</h5>
                <div class="table-wrapper" style="overflow-x:auto;">
                    <table class="table table-bordered table-striped table-sm table-freeze">
                        <thead class="table-light">
                            <tr>
                                <th class="sticky-col">Jenis</th>
                                <th class="sticky-col-2">Kode Warna</th>
                                <th class="sticky-col-3">Warna</th>
                                <th>Status Celup</th>
                                <th>Qty PO</th>
                                <th>Qty Celup</th>
                                <th>Lot Celup</th>
                                <th>Tgl Schedule</th>
                                <th>Tgl Bon</th>
                                <th>Tgl Celup</th>
                                <th>Tgl Bongkar</th>
                                <th>Tgl Press/Oven</th>
                                <th>Tgl TL</th>
                                <th>Tgl Rajut Pagi</th>
                                <th>Serah Terima ACC</th>
                                <th>Tgl ACC KK</th>
                                <th>Tgl Kelos</th>
                                <th>Tgl Reject KK</th>
                                <th>Tgl Matching</th>
                                <th>Tgl Perbaikan</th>
                                <th>Ket Daily Cek</th>
                                <th>Stock Gbn (Kg)</th>
                            </tr>
                        </thead>
                        <tbody>
`;

        let htmlCelupBody = "";

        let htmlCoveringHeader = `
<div class="row my-4">
    <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <h5 class="mt-4">🧵 Status COVERING (Spandex & Karet)</h5>
                <div class="table-wrapper" style="overflow-x:auto;">
                    <table class="table table-bordered table-striped table-sm table-freeze">
                        <thead class="table-light">
                            <tr>
                                <th class="sticky-col">Model</th>
                                <th class="sticky-col-2">Jenis</th>
                                <th class="sticky-col-3">Kode Warna</th>
                                <th>Warna</th>
                                <th>Status Covering</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
`;

        let htmlCoveringBody = "";

        // Loop data
        dataStatus.forEach(item => {
            const statusClasses = {
                'done': 'bg-gradient-success text-white',
                'retur': 'bg-gradient-warning text-dark',
            };

            const statusClass = statusClasses[item?.last_status] || 'bg-gradient-info text-white';
            const statusCov = statusClasses[item?.status] || 'bg-gradient-info text-white';

            const keteranganBadge = item?.keterangan ?
                item.keterangan.split(',').map(ket => `<div>${ket.trim()}</div>`).join('') :
                '-';

            const jenis = (item?.jenis || '').toUpperCase();

            if (['BENANG', 'NYLON'].includes(jenis)) {
                htmlCelupBody += `
<tr>
    <td  class="sticky-col">${item.item_type}</td>
    <td  class="sticky-col-2">${item.kode_warna}</td>
    <td class="sticky-col-3">${item.color}</td>
    <td><span class="badge ${statusClass} px-3 py-2">${item.last_status || '-'}</span></td>
    <td class="text-end">${formatNumber(item.qty_po)}</td>
    <td class="text-end">${formatNumber(item.kg_celup)}</td>
    <td>${item.lot_celup || '-'}</td>
    <td>${formatDate(item.tanggal_schedule)}</td>
    <td>${formatDate(item.tanggal_bon)}</td>
    <td>${formatDate(item.tanggal_celup)}</td>
    <td>${formatDate(item.tanggal_bongkar)}</td>
    <td>${formatDate(item.tanggal_press_oven)}</td>
    <td>${formatDate(item.tanggal_tl)}</td>
    <td>${formatDate(item.tanggal_rajut_pagi)}</td>
    <td>${formatDate(item.serah_terima_acc)}</td>
    <td>${formatDate(item.tanggal_acc)}</td>
    <td>${formatDate(item.tanggal_kelos)}</td>
    <td>${formatDate(item.tanggal_reject)}</td>
    <td>${formatDate(item.tanggal_matching)}</td>
    <td>${formatDate(item.tanggal_perbaikan)}</td>
    <td>${item.ket_daily_cek || '-'}</td>
    <td class="text-end">${formatNumber(item.kg_stock)}</td>
</tr>
`;
            }

            if (['SPANDEX', 'KARET'].includes(jenis)) {
                htmlCoveringBody += `
<tr>
    <td>${item?.no_model || '-'}</td>
    <td>${item?.item_type || '-'}</td>
    <td>${item?.kode_warna || '-'}</td>
    <td>${item?.color || '-'}</td>
    <td><span class="badge ${statusCov} px-3 py-2">${item?.status || '-'}</span></td>
    <td>${keteranganBadge}</td>
</tr>
`;
            }
        });

        // Tutup tabel
        let htmlCelup = htmlCelupHeader + htmlCelupBody + `</tbody></table></div></div></div></div></div>`;
        let htmlCovering = htmlCoveringHeader + htmlCoveringBody + `</tbody></table></div></div></div></div></div>`;

        // Render
        resultContainer.innerHTML += htmlCelup + htmlCovering;
    }

    // Fungsi untuk format tanggal agar tidak error
    function formatDate(dateString) {
        if (!dateString) return '-';
        let date = new Date(dateString);
        if (isNaN(date)) return '-';
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short'
        });
    }

    function formatNumber(value) {
        return value != null && !isNaN(value) ?
            parseFloat(value).toLocaleString('id-ID', {
                minimumFractionDigits: 2
            }) :
            '-';
    }
</script>

<?php $this->endSection(); ?>