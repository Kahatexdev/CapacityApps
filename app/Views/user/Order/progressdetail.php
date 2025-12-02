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
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Progress Detail</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $model ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= base_url($role . '/statusorder/' . $area) ?>" class="btn btn-info">Back</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div id="loading" style="display: none;">
        <div class="card">
            <div class="card-body ">

                <h3>Sedang Menghitung...</h3>
                <img src="<?= base_url('assets/spinner.gif') ?>" alt="Loading...">
            </div>
        </div>
    </div>


    <div class="row my-3">
        <div class="card">
            <div class="col-lg-12">
                <div class="card-header d-flex align-items-center">
                    <div class="col-9">
                        <h5>
                            Order status <?= $model ?>
                        </h5>
                    </div>
                    <div class="col-3 d-flex justify-content-end ">
                        <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                        <input type="hidden" class="form-control" id="no_model" value="<?= $model ?>" placeholder="No Model" required>
                        <a href="<?= base_url($role . '/form-potambahan/' . $area) ?>" class="btn btn-info ms-2">PO <i class="fas fa-plus"> </i> </a>
                        <button id="searchModel" class="btn btn-info ms-2"><i class="fas fa-search"></i> Hitung PPH</button>
                    </div>
                </div>
                <div class="card-body">

                    <div id="progress-container">
                        <?php foreach ($perjarum as $jarum => $key): ?>
                            <div class="row mt-3" style="padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
                                <div class="col-lg-2">
                                    <h6 class="font-weight-bold" style="color: #333;"><?= $key['jarum'] ?></h6>
                                </div>
                                <div class="col-lg-8">
                                    <div class="progress-wrapper">
                                        <div class="progress-info">
                                            <span class="text-sm font-weight-bold" style="color: #555;">
                                                <?= $key['percentage'] ?>% (<?= $key['target'] - $key['remain'] ?> dz / <?= $key['target'] ?> dz)
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div id="<?= $key['mastermodel'] ?>-progress-bar"
                                                class="progress-bar <?php if ($key['percentage'] < 100): ?>
                                    bg-gradient-info
                                <?php elseif ($key['percentage'] == 100): ?>
                                    bg-gradient-success
                                <?php else: ?>
                                    bg-gradient-danger
                                <?php endif; ?>"
                                                role="progressbar"
                                                style="width: <?= $key['percentage'] ?>%; height: 8px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $key['jarum'] ?>" aria-expanded="false">
                                        Details
                                    </button>
                                </div>
                            </div>

                            <!-- Section for collapsible delivery details -->
                            <div class="collapse" id="collapse-<?= $key['jarum'] ?>" style="padding-left: 20px;">
                                <?php foreach ($key['detail'] as $deliveryDate => $row): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-2">
                                            <h6 class="text-muted"> <?= $deliveryDate ?></h6>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="progress-wrapper">
                                                <div class="progress-info">
                                                    <span class="text-sm font-weight-bold" style="color: #777;">
                                                        <?= $row['percentage'] ?>% (<?= round($row['target'] - $row['remain']) ?> dz / <?= round($row['target']) ?> dz)
                                                    </span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div id="<?= $row['mastermodel'] ?>-progress-bar"
                                                        class="progress-bar <?php if ($row['percentage'] < 100): ?>
                                    bg-gradient-info
                                <?php elseif ($row['percentage'] == 100): ?>
                                    bg-gradient-success
                                <?php else: ?>
                                    bg-gradient-danger
                                <?php endif; ?>"
                                                        role="progressbar"
                                                        style="width: <?= $row['percentage'] ?>%; height: 6px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $row['delivery'] ?>-<?= $jarum ?>" aria-expanded="false">
                                                Sizes
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Section for collapsible size details -->
                                    <div class="collapse" id="collapse-<?= $row['delivery'] ?>-<?= $jarum ?>" style="padding-left: 40px;">
                                        <?php foreach ($row['size'] as $size => $sizeDetail): ?>
                                            <div class="row mt-2">
                                                <div class="col-lg-3">
                                                    <h6 class="text-muted"> <?= $size ?></h6>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="progress-wrapper">
                                                        <div class="progress-info">
                                                            <span class="text-sm" style="color: #999;">
                                                                <?= $sizeDetail['percentage'] ?>% (<?= round($sizeDetail['target'] - $sizeDetail['remain']) ?> dz / <?= round($sizeDetail['target']) ?> dz)
                                                            </span>
                                                        </div>
                                                        <div class="progress" style="height: 4px;">
                                                            <div id="<?= $sizeDetail['mastermodel'] ?>-progress-bar"
                                                                class="progress-bar <?php if ($sizeDetail['percentage'] < 100): ?>
                                            bg-gradient-info
                                        <?php elseif ($sizeDetail['percentage'] == 100): ?>
                                            bg-gradient-success
                                        <?php else: ?>
                                            bg-gradient-danger
                                        <?php endif; ?>"
                                                                role="progressbar"
                                                                style="width: <?= $sizeDetail['percentage'] ?>%; height: 4px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
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

        </div>

    </div>
</div>


</div>


</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">

</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 35,
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();

                // Calculate the total of the 4th column (Qty in dozens) - index 3
                var totalQty = api.column(3, {
                    page: 'current'
                }).data().reduce(function(a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);

                // Calculate the total of the 5th column (Remaining Qty in dozens) - index 4
                var totalRemainingQty = api.column(4, {
                    page: 'current'
                }).data().reduce(function(a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);

                var totalqty = numberWithDots(totalQty) + " Dz";
                var totalsisa = numberWithDots(totalRemainingQty) + " Dz";

                // Update the footer cell for the total Qty
                $(api.column(3).footer()).html(totalqty);

                // Update the footer cell for the total Remaining Qty
                $(api.column(4).footer()).html(totalsisa);
            }
        });
    });
    let btnSearch = document.getElementById('searchModel');

    btnSearch.onclick = function() {
        let area = document.getElementById('area').value;
        let model = document.getElementById('no_model').value;
        let role = <?= json_encode($role) ?>;
        let loading = document.getElementById('loading');

        console.log("Area: " + area);
        console.log("Model: " + model);

        loading.style.display = 'block'; // T

        $.ajax({
            url: "<?= base_url($role . '/filterpph/') ?>" + area,
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
        let qty = parseFloat(data.qty / 24).toFixed(2);
        let sisa = parseFloat(data.sisa / 24).toFixed(2);
        let bruto = parseFloat(data.bruto / 24).toFixed(2);
        let bs_setting = parseFloat(data.bs_setting / 24).toFixed(2);
        let bs_mesin = parseInt(data.bs_mesin).toLocaleString();

        let header = document.getElementById('HeaderRow');

        let baseUrl = "<?= base_url($role . '/excelPPHNomodel/') ?>";

        header.innerHTML = ` 
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="mb-0">${model}</h3>
                <a href="${baseUrl}${area}/${model}" id="exportExcel" class="btn btn-success">
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
</script>
<script>

</script>
<?php $this->endSection(); ?>