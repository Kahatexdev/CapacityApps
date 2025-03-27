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
                        <div class="col-7">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Stock Bahan Baku
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 d-flex align-items-center text-end gap-2">
                            <label for="">Filters:</label>
                            <input type="text" class="form-control" id="model" value="" placeholder="No Model">
                            <input type="text" class="form-control" id="kode_warna" value="" placeholder="Kode Warna">
                            <button id="filterButton" class="btn btn-info ms-2"><i class="fas fa-search"></i></button>
                        </div>
                        <div class="col-1 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
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
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('filterButton').addEventListener('click', function() {
            let noModel = document.getElementById('model').value.trim();
            let warna = document.getElementById('kode_warna').value.trim();

            // Cek apakah No Model kosong
            if (noModel === "") {
                alert("No Model wajib diisi untuk memfilter Kode Warna!");
                document.getElementById('model').focus();
                return; // Hentikan eksekusi jika No Model kosong
            }

            let apiUrl = `<?= base_url() ?>/${'<?= $role ?>'}/filterstockbahanbaku/<?= $area ?>?noModel=${noModel}&warna=${warna}`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    console.log("Filtered Data: ", data);
                    displayData(data);
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    });

    function displayData(data) {
        let resultContainer = document.getElementById('resultContainer');
        resultContainer.innerHTML = '';

        if (!Array.isArray(data)) {
            data = Object.values(data);
        }

        if (data.length === 0) {
            resultContainer.innerHTML = '<p class="text-center text-muted">No data found</p>';
            return;
        }

        let output = ''; // Tambahkan inisialisasi output

        data.forEach(item => {
            let totalKgs = item.Kgs && item.Kgs > 0 ? item.Kgs : item.KgsStockAwal;
            let totalKrg = item.Krg && item.Krg > 0 ? item.Krg : item.KrgStockAwal;

            if (totalKgs == 0 && totalKrg == 0) {
                return;
            }

            output += `
            <div class="result-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="badge bg-info">Cluster: ${item.nama_cluster} | No Model: ${item.no_model}</h5>
                    <span class="badge bg-secondary">Jenis: ${item.item_type}</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <p><strong>Lot Jalur:</strong> ${item.lot_stock || item.lot_awal}</p>
                        <p><strong>Space:</strong> ${item.kapasitas || 0} KG</p>
                        <p><strong>Sisa Space:</strong> ${item.sisa_space || 0} KG</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Kode Warna:</strong> ${item.kode_warna}</p>
                        <p><strong>Warna:</strong> ${item.warna}</p>
                        <p><strong>Total KGs:</strong> ${totalKgs} KG | ${item.cns_stock_awal && item.cns_stock_awal > 0 ? item.cns_stock_awal : item.cns_in_out} Cones | ${totalKrg} KRG </p>
                    </div>
                    <div class="col-md-4 d-flex flex-column gap-2">
                        <button class="btn btn-outline-info btn-sm">In/Out</button>
                        <button class="btn btn-outline-info btn-sm pindahPalet" data-id="${item.id_stock}" data-cluster="${item.nama_cluster}" data-lot="${item.lot_stock}" data-kgs="${totalKgs}" data-cones="${item.cns_stock_awal && item.cns_stock_awal > 0 ? item.cns_stock_awal : item.cns_in_out}" data-krg="${totalKrg}">Pindah Palet</button>
                        <button class="btn btn-outline-info btn-sm pindahOrder" data-id="${item.id_stock}" data-noModel="${item.no_model}" data-cluster="${item.nama_cluster}" data-lot="${item.lot_stock}" data-kgs="${totalKgs}" data-cones="${item.cns_stock_awal && item.cns_stock_awal > 0 ? item.cns_stock_awal : item.cns_in_out}" data-krg="${totalKrg}">Pindah Order</button>
                    </div>
                </div>
            </div>`;
        });

        resultContainer.innerHTML = output; // Tambahkan hasil ke dalam container
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