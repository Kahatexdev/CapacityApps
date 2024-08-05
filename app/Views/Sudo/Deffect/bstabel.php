<?php $this->extend($role . '/Deffect/databs'); ?>
<?php $this->section('bstabel'); ?>

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
        <div class="card">
            <div class="card-header">
                <h5>
                    Data In Stocklot <?= $awal ?> sampai <?= $akhir ?>
                    <?php if (!empty($area)) { ?>
                        Area <?= $area ?>
                    <?php } ?>
                    <?php if (!empty($pdk)) { ?>
                        No Model <?= $pdk ?>
                    <?php } ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="datatable">
                    <table id="dataTable1" class="display  striped" style="width:100%">
                        <thead>

                            <tr>
                                <th>Tgl In Stocklot</th>
                                <th>Area</th>
                                <th>No Model</th>
                                <th>Style</th>
                                <th>No Label</th>
                                <th>No Box</th>
                                <th>Qty Deffect</th>
                                <th>Kode Deffect</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($databs as $bs) : ?>
                                <tr>
                                    <td><?= $bs['tgl_instocklot'] ?></td>
                                    <td><?= $bs['area'] ?></td>
                                    <td><?= $bs['mastermodel'] ?></td>
                                    <td><?= $bs['size'] ?></td>
                                    <td><?= $bs['no_label'] ?></td>
                                    <td><?= $bs['no_box'] ?></td>
                                    <td><?= $bs['qty'] ?> pcs</td>
                                    <td><?= $bs['kode_deffect'] ?></td>
                                    <td><?= $bs['Keterangan'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>

                    </table>

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

                    Total in Stocklot : <?= ceil($totalbs / 24) ?> dz (<?= $totalbs ?> pcs)
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8 col-md-8">

                        <div class="chart">
                            <canvas id="bs-chart" class="chart-canvas" height="300"></canvas>
                        </div>

                    </div>



                    <div class="col-lg-4 col-md-4">
                        <table class=" table-responsive">
                            <thead>
                                <tr>
                                    <th> Keterangan</th>
                                    <th> Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chart as $ch) : ?>
                                    <tr>
                                        <td><?= $ch['Keterangan'] ?></td>
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
    console.log(labels)
    // Membuat elemen canvas untuk setiap chart
    var canvasId = "bs-chart";
    var canvas = document.createElement('canvas');
    canvas.id = canvasId;
    document.body.appendChild(canvas);

    var ctx4 = document.getElementById(canvasId).getContext("2d");

    // Membuat pie chart
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
                backgroundColor: ['#845ec2', '#d65db1', '#ff6f91', '#ff9671', '#ffc75f', '#f9f871', '#008f7a', '#b39cd0', '#c34a36', '#4b4453', '#4ffbdf', '#936c00', '#c493ff', '#296073'],
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