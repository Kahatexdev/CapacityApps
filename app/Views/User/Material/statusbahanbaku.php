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
                            <label for="">Tanggal Schedule</label>
                            <input type="date" class="form-control" id="filter_date" value="">
                            <input type="text" class="form-control" id="filter" value="" placeholder="No Model/Kode Warna/Lot">
                            <button id="filterButton" class="btn btn-info ms-2"><i class="fas fa-search"></i></button>
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
    document.getElementById('filterButton').addEventListener('click', function() {
        let keyword = document.getElementById('filter').value.trim();
        let keyword2 = document.getElementById('filter_date').value.trim();

        // Use a ternary operator to set 'cari'
        const cari = isNaN(keyword) ? encodeURIComponent(keyword) : encodeURIComponent(keyword2);
        console.log("Keyword: ", keyword); // Debugging

        // Pastikan $role dan $area ada dan diterjemahkan dengan benar oleh PHP
        let apiUrl = `<?= base_url() ?>/${'<?= $role ?>'}/filterstatusbahanbaku/<?= $area ?>?search=${cari}`;


        // Mengirim data ke controller internal
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                console.log("Filtered Data: ", data); // Debugging
                displayData(data); // Tampilkan data yang sudah difilter
            })
            .catch(error => console.error('Error fetching data:', error));
    });

    function displayData(data) {
        let resultContainer = document.getElementById('resultContainer');
        resultContainer.innerHTML = '';

        if (!Array.isArray(data)) {
            // Mengubah objek menjadi array
            data = Object.values(data);
        }

        if (data.length === 0) {
            resultContainer.innerHTML = '<p class="text-center text-muted">No data found</p>';
            return;
        }

        data.forEach(item => {
            let statusClasses = {
                'done': 'bg-gradient-success',
                'retur': 'bg-gradient-warning',
            };

            let statusClass = statusClasses[item.last_status] || 'bg-gradient-info';

            let card = `
            <div class="row my-3 mx-2">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="text-header mb-0">Model: ${item.no_model}</h5>
                        <span class="badge ${statusClass} text-sm">${item.last_status}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <span class="d-block"><strong>Jenis:</strong> ${item.item_type}</span>
                                <span class="d-block"><strong>Kode Warna:</strong> ${item.kode_warna}</span>
                                <span class="d-block"><strong>Warna:</strong> ${item.color}</span>
                                <span class="d-block"><strong>Lot Celup:</strong> ${item.lot_celup}</span>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block"><strong>Qty PO:</strong> ${parseFloat(item.qty_po).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</span>
                                <span class="d-block"><strong>Qty Celup:</strong> ${parseFloat(item.kg_celup).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</span>
                                <span class="d-block"><strong>Start MC:</strong> ${formatDate(item.start_mc)}</span>
                                <span class="d-block"><strong>Tgl Schedule:</strong> ${formatDate(item.tanggal_schedule)}</span>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block"><strong>Tgl Bon:</strong> ${formatDate(item.tanggal_bon)}</span>
                                <span class="d-block"><strong>Tgl Celup:</strong> ${formatDate(item.tanggal_celup)}</span>
                                <span class="d-block"><strong>Tgl Bongkar:</strong> ${formatDate(item.tanggal_bongkar)}</span>
                                <span class="d-block"><strong>Tgl Press:</strong> ${formatDate(item.tanggal_press)}</span>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block"><strong>Tgl Oven:</strong> ${formatDate(item.tanggal_oven)}</span>
                                <span class="d-block"><strong>Tgl TL:</strong> ${formatDate(item.tanggal_tl)}</span>
                                <span class="d-block"><strong>Tgl Rajut Pagi:</strong> ${formatDate(item.tanggal_rajut_pagi)}</span>
                                <span class="d-block"><strong>Tgl ACC:</strong> ${formatDate(item.tanggal_acc)}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-3">
                                <span class="d-block"><strong>Tgl Kelos:</strong> ${formatDate(item.tanggal_kelos)}</span>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block"><strong>Tgl Reject:</strong> ${formatDate(item.tanggal_reject)}</span>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block"><strong>Tgl Perbaikan:</strong> ${formatDate(item.tanggal_perbaikan)}</span>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block"><strong>Ket:</strong> ${item.ket_daily_cek}</span>
                            </div>
                        </div>
                    </div>
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