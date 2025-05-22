<?php $this->extend('user/layout'); ?>
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
                                <div class="col-3 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Buyer</p>
                                        <h5 class="font-weight-bolder mb-0">${data['master']['kd_buyer_order']}</h5>
                                    </div>
                                </div>
                                <div class="col-3 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Delivery Awal</p>
                                        <h5 class="font-weight-bolder mb-0">${data['master']['delivery_awal']}</h5>
                                    </div>
                                </div>
                                <div class="col-3 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Delivery Akhir</p>
                                        <h5 class="font-weight-bolder mb-0">${data['master']['delivery_akhir']}</h5>
                                    </div>
                                </div>
                                <div class="col-3 d-flex flex-column align-items-center">
                                    <div class="numbers text-center">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Start MC</p>
                                        <h5 class="font-weight-bolder mb-0">${data['master']['start_mc'] ?? '-' }</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        dataStatus.forEach(item => {
            let statusClasses = {
                'done': 'bg-gradient-success',
                'retur': 'bg-gradient-warning',
            };

            let statusClass = statusClasses[item.last_status] || 'bg-gradient-info';
            let statusCov = statusClasses[item.status] || 'bg-gradient-info';

            let keteranganBadge = '';
            if (item.keterangan) {
                item.keterangan.split(',').forEach(ket => {
                    keteranganBadge += `
                    <div class="mb-1"><span>${ket.trim()}</span></div>`;
                });
            }

            // Ubah ke huruf besar agar konsisten
            let jenis = (item.jenis || '').toUpperCase();

            // CELUP SECTION (jika BENANG)
            let celupSection = '';
            if (['BENANG', 'NYLON'].includes(jenis)) {
                celupSection = `
                    <div class="mb-4 border-bottom pb-3">
                        <h6 class="text-primary border-start border-4 ps-2 mb-3">🧪 Status CELUP</h6>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong>Status:</strong>
                            <span class="badge ${statusClass} px-3 py-2">${item.last_status}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>Qty PO:</strong> ${parseFloat(item.qty_po).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</p>
                                <p><strong>Qty Celup:</strong> ${parseFloat(item.kg_celup).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</p>
                                <p><strong>Lot Celup:</strong> ${item.lot_celup}</p>
                                <p><strong>Start MC:</strong> ${formatDate(item.start_mc)}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Tgl Schedule:</strong> ${formatDate(item.tanggal_schedule)}</p>
                                <p><strong>Tgl Bon:</strong> ${formatDate(item.tanggal_bon)}</p>
                                <p><strong>Tgl Celup:</strong> ${formatDate(item.tanggal_celup)}</p>
                                <p><strong>Tgl Bongkar:</strong> ${formatDate(item.tanggal_bongkar)}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Tgl Press:</strong> ${formatDate(item.tanggal_press)}</p>
                                <p><strong>Tgl Oven:</strong> ${formatDate(item.tanggal_oven)}</p>
                                <p><strong>Tgl TL:</strong> ${formatDate(item.tanggal_tl)}</p>
                                <p><strong>Tgl Rajut Pagi:</strong> ${formatDate(item.tanggal_rajut_pagi)}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Tgl ACC:</strong> ${formatDate(item.tanggal_acc)}</p>
                                <p><strong>Tgl Kelos:</strong> ${formatDate(item.tanggal_kelos)}</p>
                                <p><strong>Tgl Reject:</strong> ${formatDate(item.tanggal_reject)}</p>
                                <p><strong>Tgl Perbaikan:</strong> ${formatDate(item.tanggal_perbaikan)}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Ket Daily Cek:</strong></p>
                        </div>
                        <div class="col-md-3">
                            <p>${item.ket_daily_cek || '-'}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Stock Gbn:</strong></p>
                        </div>
                        <div class="col-md-3">
                            <p>${parseFloat(item.kg_stock).toLocaleString('id-ID', { minimumFractionDigits: 2 }) || '-'} Kg</p>
                        </div>
                    </div>
                `;
            }

            // COVERING SECTION (jika KARET, NYLON, SPANDEX)
            let coveringSection = '';
            if (['KARET', 'SPANDEX'].includes(jenis)) {
                coveringSection = `
                    <div class="mb-3">
                        <h6 class="text-success border-start border-4 ps-2 mb-3">🧵 Status COVERING</h6>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong>Status:</strong>
                            <span class="badge ${statusCov} px-3 py-2">${item.status || '-'}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Keterangan:</strong><br/>
                            </div>
                            <div class="col-md-8">
                                ${keteranganBadge || '<span class="text-muted">-</span>'}
                            </div>
                        </div>
                    </div>
                `;
            }


            let card = `
                <div class="card shadow-sm my-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Model: <strong>${item.no_model}</strong> | Jenis: ${item.item_type}</h5>
                        <small>Kode Warna: ${item.kode_warna} | Warna: ${item.color}</small>
                    </div>
                    <div class="card-body">
                        ${celupSection}
                        ${coveringSection}
                    </div>
                </div>
            `;

            resultContainer.innerHTML += card;
        });

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
</script>

<?php $this->endSection(); ?>