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
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    PPH: Per Model
                                </h5>
                            </div>
                        </div>
                        <div class="col-3 d-flex align-items-center text-end gap-2">
                            <label for="">Filters:</label>
                            <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                            <input type="text" class="form-control" id="model" value="" placeholder="No Model">
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
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info text-center text-white" id="info" role="alert">
                    Silakan masukkan No Model untuk mencari data.
                </div>
            </div>
        </div>
        <div id="loading" style="display: none;">
            <h3>Sedang Menghitung...</h3>
            <img src="<?= base_url('assets/spinner.gif') ?>" alt="Loading...">
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('filterButton').addEventListener('click', function() {
            let noModel = document.getElementById('model').value.trim();
            let area = document.getElementById('area').value.trim();
            let role = <?= json_encode($role) ?>;
            let loading = document.getElementById('loading');
            let info = document.getElementById('info');
            loading.style.display = 'block'; // T
            info.style.display = 'none'; // 
            $.ajax({
                let apiUrl = `<?= base_url() ?>${'<?= $role ?>'}/filterpph/<?= $area ?>?noModel=${noModel}`;

                fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    console.log("Filtered Data: ", data);
                    if (data.length > 0) {
                        displayData(data[0], noModel, area); // Ambil elemen pertama dari array
                    } else {
                        alert("Data tidak ditemukan!");
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
             });
        });
    });

    function displayData(data, noModel, area) {
        let qty = parseFloat(data.qty / 24).toFixed(2);
        let sisa = parseFloat(data.sisa / 24).toFixed(2);
        let bruto = parseFloat(data.bruto / 24).toFixed(2);
        let bs_setting = parseFloat(data.bs_setting / 24).toFixed(2);
        let bs_mesin = parseInt(data.bs_mesin).toLocaleString();

        let header = document.getElementById('HeaderRow');

        let baseUrl = "<?= base_url($role . '/excelPPHNomodel/') ?>";

        header.innerHTML = ` 
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="mb-0">${noModel}</h3>
                <a href="${baseUrl}${area}/${noModel}" id="exportExcel" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Area:</strong> ${area}</td>
                        <td><strong>Produksi:</strong> ${bruto} dz</td>
                    </tr>
                    <tr>
                    <td><strong>Qty:</strong> ${qty} dz</td>
                        <td><strong>Bs Setting:</strong> ${bs_setting} dz</td>
                    </tr>
                    <tr>
                        <td><strong>Sisa:</strong> ${sisa} dz</td>
                        <td><strong>Bs Mesin:</strong> ${bs_mesin} gr</td>
                    </tr>
                
                </table>
            </div>`;

        let body = document.getElementById('bodyData')
        // Ambil kunci utama dalam objek
        let keys = Object.keys(data);

        // Filter untuk mendapatkan bahan baku (exclude qty, sisa, dll.)
        let materials = keys.filter(key => !["qty", "sisa", "bruto", "bs_setting", "bs_mesin"].includes(key));

        materials.sort((a, b) => {
            return data[a].item_type.localeCompare(data[b].item_type) ||
                data[a].kode_warna.localeCompare(data[b].kode_warna);
        });

        // Looping untuk buat baris tabel
        let rows = materials.map((material, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${data[material].item_type}</td>
                <td>${data[material].kode_warna}</td>
                <td>${data[material].warna}</td>
                <td>${data[material].kgs.toFixed(2)} kg</td>
                <td>${data[material].pph.toFixed(2)} kg</td>
            </tr>
        `).join('');

        body.innerHTML = `
            <div class="table-responsive">
                <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">Kode Warna</th>
                            <th class="text-center">Warna</th>
                            <th class="text-center">PO (KGS)</th>
                            <th class="text-center">PPH</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows} <!-- Data bahan baku masuk sini -->
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