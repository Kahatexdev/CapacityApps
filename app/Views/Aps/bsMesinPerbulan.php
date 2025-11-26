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
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="text-dark fw-bold mb-0">
                        <i class="ni ni-filter-alt text-info me-2"></i> Filter Data BS Mesin
                    </h5>
                </div>

                <div class="card-body pt-3">
                    <form action="<?= base_url($role . '/bsMesinPerbulan/' . $ar . '/' . $bulan) ?>" method="get" class="row g-3 align-items-end">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="buyer" class="form-label fw-semibold small mb-1">Buyer</label>
                                <select class="form-select text-dark" id="buyer" name="buyer">
                                    <option value="">Pilih Buyer</option>
                                    <?php foreach ($buyerList as $b) : ?>
                                        <option value="<?= $b['kd_buyer_order']; ?>"
                                            <?= isset($buyerFilter) && $buyerFilter == $b['kd_buyer_order'] ? 'selected' : '' ?>>
                                            <?= $b['kd_buyer_order']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small mb-1"> </label>
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="fas fa-search me-1"></i> Tampilkan
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card mt-2">
                <div class="card-header">
                    <h5>
                        <h5>
                            Data BS Mesin <?= $area ?> Bulan <?= $month ?>
                        </h5>
                        Total in BS Mesin : <?= $totalbsgram ?> gr (<?= $totalbspcs ?> pcs)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 col-md-8">

                            <div class="chart">
                                <canvas id="bs-chart" class="chart-canvas" height="500"></canvas>
                            </div>

                        </div>
                        <div class="col-lg-4 col-md-4">
                            <table class="table-responsive">
                                <thead>
                                    <tr>
                                        <th>Keterangan</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Array warna yang akan digunakan
                                    $chartColors = ['#845ec2', '#d65db1', '#ff6f91', '#ff9671', '#ffc75f', '#f9f871', '#008f7a', '#b39cd0', '#c34a36', '#4b4453', '#4ffbdf', '#936c00', '#c493ff', '#296073'];

                                    foreach ($chart as $index => $ch) :
                                        // Ulangi warna jika index lebih besar dari jumlah warna yang tersedia
                                        $color = $chartColors[$index % count($chartColors)];
                                    ?>
                                        <tr>
                                            <td> <i class="ni ni-button-play" style="color: <?= $color ?>;"></i><?= $ch['no_model'] ?></td>
                                            <td><?= $ch['totalGram'] ?> gr</td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card pb-0">
                <div class="card-header d-flex justify-content-between">
                    <h5>Total <?= $month ?></h5>
                    <!-- <a href="<?= base_url($role . '/exportBsMesinPerbulan/' . $area . '/' . $month) ?>" class="btn btn-success">Export</a> -->
                    <p class="text-danger">Rumus persentase (%) = (Bs Mesin + Perbaikan) / Produksi</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable1" class="display  striped" style="width:100%">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Produksi</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Produksi (Pcs)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Bs Mesin (Pcs)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Perbaikan (Pcs)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Persentase (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dataTotal as $bs) : ?>
                                    <tr>
                                        <td class="text-sm"><?= $bs['tanggal']; ?></td>
                                        <td class="text-sm"><?= $bs['qty_prod']; ?></td>
                                        <td class="text-sm"><?= $bs['totalBsMc']; ?></td>
                                        <td class="text-sm"><?= $bs['qty_perbaikan']; ?></td>
                                        <td class="text-sm"><?= round($bs['persentase'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card pb-0">
                    <div class="card-header d-flex justify-content-between">
                        <h5>BS Mesin bulan <?= $month ?></h5>
                        <a href="<?= base_url($role . '/exportBsMesinPerbulan/' . $area . '/' . $month) ?>" class="btn btn-success">Export</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTable0" class="display  striped" style="width:100%">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Nama</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Bs (Dz)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dataBs as $bs) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $bs['nama_karyawan']; ?></td>
                                            <td class="text-sm"><?= $bs['totalBsMc']; ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    // AJAX form submission
    $(document).ready(function() {
        $('#dataTable0').DataTable({
            "order": []
        });
        $('#dataTable').DataTable({});

        $('#dataTable1').DataTable({
            "order": []
        });
        $('#dataTable').DataTable({});


    });
</script>
<script>
    let data = <?php echo json_encode($chart); ?>;

    let labels = data.map(item => item.no_model);
    let value = data.map(item => item.totalGram);

    // Warna yang diulang jika jumlah data lebih banyak dari warna yang tersedia
    let chartColors = <?php echo json_encode($chartColors); ?>;
    let colors = data.map((_, index) => chartColors[index % chartColors.length]);

    var ctx4 = document.getElementById("bs-chart").getContext("2d");

    new Chart(ctx4, {
        type: "pie",
        data: {
            labels: labels,
            datasets: [{
                label: "Projects",
                weight: 9,
                cutout: 0,
                tension: 0.9,
                pointRadius: 2,
                borderWidth: 2,
                backgroundColor: colors,
                data: value,
                fill: false
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                    },
                    ticks: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                    },
                    ticks: {
                        display: false,
                    }
                },
            },
        },
    });
</script>


<?php $this->endSection(); ?>