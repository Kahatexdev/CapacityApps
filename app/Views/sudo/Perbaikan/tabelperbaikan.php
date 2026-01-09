<?php $this->extend($role . '/Perbaikan/perbaikan'); ?>
<?php $this->section('tabelperbaikan'); ?>

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

                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-0">Overall Equipment Effectiveness</h5>
                        <h6 class="fw-bold mb-0">
                            Summary KPI
                            <?=
                            (!empty($filter['awal']) && !empty($filter['akhir']) ? " Tanggal {$filter['awal']} s/d {$filter['akhir']}" : '') .
                                (!empty($filter['area'])  ? " Area {$filter['area']}" : '') .
                                (!empty($filter['buyer']) ? " Buyer {$filter['buyer']}" : '') .
                                (!empty($filter['pdk'])   ? " No Model {$filter['pdk']}" : '')
                            ?>
                        </h6>
                    </div>
                </div>

                <div class="row align-items-center border-top pt-3">
                    <div class="col-md-6">
                        <span class="fw-bold">Total Produksi :</span>
                        <span class="fw-bold text-primary ms-1"><?= ($total['prod'] > 0) ? round($total['prod'], 2) . "dz" : '-' ?></span><br>
                        <span class="fw-bold">In Perbaikan :</span>
                        <span class="fw-bold text-primary ms-1"><?= ($total['pb'] > 0) ? round($total['pb'], 2) . "dz" : '-' ?></span><br>
                        <span class="fw-bold">Out Perbaikan Good :</span>
                        <span class="fw-bold text-primary ms-1"><?= ($total['goodPb'] > 0) ? round($total['goodPb'], 2) . "dz" : '-' ?></span> <br>
                        <span class="fw-bold">Deffect Perbaikan :</span>
                        <span class="fw-bold text-primary ms-1"><?= ($total['stcPb'] > 0) ? round($total['stcPb'], 2) . "dz" : '-' ?></span><br>
                        <span class="fw-bold">Total Stocklot :</span>
                        <span class="fw-bold text-primary ms-1"><?= ($total['stc'] > 0) ? round($total['stc'], 2) . "dz" : '-' ?></span>
                    </div>
                </div>
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
                        Data In Perbaikan <?= $awal ?> sampai <?= $akhir ?>
                        <?php if (!empty($area)) { ?>
                            Area <?= $area ?>
                        <?php } ?>
                        <?php if (!empty($pdk)) { ?>
                            No Model <?= $pdk ?>
                        <?php } ?>
                    </h5>
                    Total in Perbaikan : <?= ceil($totalbs / 24) ?> dz (<?= $totalbs ?> pcs)
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
                                    <th></th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Array warna yang akan digunakan
                                $chartColors = ['#845ec2', '#d65db1', '#ff6f91', '#ff9671', '#ffc75f', '#f9f871', '#008f7a', '#b39cd0', '#c34a36', '#4b4453', '#4ffbdf', '#936c00', '#c493ff', '#296073'];

                                $no = 0;
                                foreach ($topTen as $index => $ch) :
                                    $no++;
                                    // Ulangi warna jika index lebih besar dari jumlah warna yang tersedia
                                    $color = $chartColors[$index % count($chartColors)];
                                ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td> <i class="ni ni-button-play" style="color: <?= $color ?>;"></i><?= $ch['Keterangan'] ?></td>
                                        <td><?= $ch['qty'] ?> Pcs</td>
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
    <div class="col">
        <div class="card mt-2">

            <div class="card-body">
                <div class="datatable">
                    <table id="dataTable1" class="display  striped" style="width:100%">
                        <thead>

                            <tr>
                                <th>TANGGAL</th>
                                <th>PROD (DZ)</th>
                                <th>IN PB (DZ)</th>
                                <th>ALL IN STOCKLOT (DZ)</th>
                                <th>DEFFECT PERBAIKAN</th>
                                <th>REPAIR (%)</th>
                                <th>BS REPAIR (%)</th>
                                <th>GOOD REPAIR (%)</th>
                                <th>PURE STOCKLOT (%)</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($getData as $tgl => $id) : ?>
                                <tr>
                                    <td><?= $tgl ?></td>
                                    <td><?= round($id['prod'], 2) ?></td>
                                    <td><?= round($id['pb'], 2) ?></td>
                                    <td><?= round($id['stc'], 2) ?></td>
                                    <td><?= round($id['stcPb'], 2) ?></td>
                                    <td><?= $id['repair'] ?>%</td>
                                    <td><?= $id['bsRepair'] ?>%</td>
                                    <td><?= $id['goodRepair'] ?>%</td>
                                    <td><?= $id['pureStc'] ?>%</td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>

                    </table>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#dataTable1').DataTable({
            "order": [
                [0, "desc"]
            ]
        });

    });
</script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>

<script>
    let data = <?php echo json_encode($chart); ?>;

    let labels = data.map(item => item.Keterangan);
    let value = data.map(item => item.qty);

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