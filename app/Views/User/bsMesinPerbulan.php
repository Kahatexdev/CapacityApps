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
                                            <td><?= $ch['totalGram'] ?> Pcs</td>
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

    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card pb-0">
                    <div class="card-header d-flex justify-content-between">
                        <h5>BS Mesin bulan <?= $month ?></h5>
                        <a href="" class="btn btn-success">Export</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTable0" class="display  striped" style="width:100%">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Nama</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">NO Mesin</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Pcs</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Gram</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dataBs as $bs) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $bs['tanggal_produksi']; ?></td>
                                            <td class="text-sm"><?= $bs['nama_karyawan']; ?></td>
                                            <td class="text-sm"><?= $bs['no_mesin']; ?> </td>
                                            <td class="text-sm"><?= $bs['no_model']; ?> </td>
                                            <td class="text-sm"><?= $bs['size']; ?> </td>
                                            <td class="text-sm"><?= $bs['qty_pcs']; ?> pcs</td>
                                            <td class="text-sm"><?= $bs['qty_gram']; ?> gr</td>
                                            <td class="text-sm"><?= $bs['area']; ?></td>

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
            "order": [
                [0, "desc"]
            ]
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