<?php $this->extend($role . '/layout'); ?>
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
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Start Stop Mc
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 d-flex align-items-center text-end gap-2">
                            <input type="text" class="form-control" id="model" value="" placeholder="No Model" required>
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
<script>
    const filterInput = document.getElementById('model');
    const filterButton = document.getElementById('filterButton');
    const resultContainer = document.getElementById('resultContainer');

    // Aktifkan tombol hanya kalau model tidak kosong
    filterInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase(); // otomatis kapital
        filterButton.disabled = filterInput.value.trim() === '';
    });

    filterButton.addEventListener('click', function() {
        const model = filterInput.value.trim();

        if (model === '') return; // stop kalau model kosong

        // Tampilkan loading
        resultContainer.innerHTML = '<p class="text-center text-muted">Loading data...</p>';

        fetch(`<?= base_url($role . '/startStopMcByPdk') ?>?no_model=${encodeURIComponent(model)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(obj => {
                // obj.dataMc adalah array hasil query dari server
                console.log('Filtered Data:', obj.dataMc);
                displayData(obj.dataMc);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                resultContainer.innerHTML = '<p class="text-center text-danger">Error fetching data</p>';
            });
    });

    function displayData(data) {
        resultContainer.innerHTML = ''; // kosongkan dulu

        console.log(data);
        let dataStatus = Array.isArray(data) ? data : [];

        if (dataStatus.length === 0) {
            resultContainer.innerHTML = '<p class="text-center text-muted">Tidak Ada Data</p>';
            return;
        }

        // Generate tabel
        let tableHtml = `
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">No Model</th>
                            <th class="text-center">Start Mc</th>
                            <th class="text-center">Stop Mc</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        dataStatus.forEach((item, index) => {
            tableHtml += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td class="text-center">${item.no_model}</td>
                    <td class="text-center">${item.start_mc}</td>
                    <td class="text-center">${item.stop_mc}</td>
                </tr>
            `;
        });

        tableHtml += `
                    </tbody>
                </table>
            </div>
        `;

        resultContainer.innerHTML = tableHtml;
    }
</script>

<?php $this->endSection(); ?>