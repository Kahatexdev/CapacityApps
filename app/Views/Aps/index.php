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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Analytical Dashboard
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php foreach ($progress as $area => $models) : ?>

        <div class="row my-3">
            <div class="card">
                <div class="col-lg-12">
                    <div class="card-header">

                        <h5>
                            Order status <?= $area ?>
                        </h5>
                    </div>
                    <div class="card-body">

                        <div id="progress-container">
                            <?php foreach ($models as $key): // Ganti $area dengan $models 
                            ?>
                                <div class="row mt-2">
                                    <div class="col-lg-2">
                                        <h6 class="card-title"><?= $key['mastermodel'] ?></h6>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="progress-wrapper">
                                            <div class="progress-info">
                                                <div class="progress-percentage">
                                                    <span class="text-sm font-weight-bold " id="<?= $key['mastermodel'] ?>-progressText"> <?= $key['percentage'] ?>% (<?= $key['target'] - $key['remain'] ?> dz / <?= $key['target'] ?> dz)</span>
                                                </div>
                                            </div>
                                            <!-- Tambahkan ID ke elemen progress bar -->
                                            <div class="progress">
                                                <div id="<?= $key['mastermodel'] ?>-progress-bar"
                                                    class="progress-bar 
                                                    <?php if ($key['percentage'] < 100): ?>
                                                        bg-gradient-info
                                                    <?php elseif ($key['percentage'] == 100): ?>
                                                        bg-gradient-success
                                                    <?php else: ?>
                                                        bg-gradient-danger
                                                    <?php endif; ?>"
                                                    role="progressbar"
                                                    aria-valuenow="<?= $key['percentage'] ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                    style="width: <?= $key['percentage'] ?>%; height: 10px;">
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="col-lg-2">
                                        <a class="btn btn-sm btn-info" href="<?= base_url($role . '/progressdetail/' . $key['mastermodel'] . '/' . $area) ?>">Details</a>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    <?php endforeach ?>

</div>


</div>


</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
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
</script>
<script>
    let data = <?php echo json_encode($order); ?>;
    console.log(data)
    // Ekstraksi tanggal dan jumlah produksi dari data
    let labels = data.map(item => item.created_at);
    let values = data.map(item => item.total_produksi);


    var ctx2 = document.getElementById("mixed-chart").getContext("2d");

    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

    var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
    gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

    new Chart(ctx2, {

        data: {
            labels: labels,
            datasets: [{
                    type: "bar",
                    label: "Data Turun Order",
                    borderWidth: 0,
                    pointRadius: 30,

                    backgroundColor: "#3A416F",
                    fill: true,
                    data: values,
                    maxBarThickness: 20

                },
                {
                    type: "line",

                    tension: 0.1,
                    borderWidth: 0,
                    pointRadius: 0,
                    borderColor: "#3A416F",
                    borderWidth: 2,
                    backgroundColor: gradientStroke1,
                    fill: true,
                    data: values,
                },
            ],
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
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#b2b9bf',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#b2b9bf',
                        padding: 20,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });
</script>
<?php $this->endSection(); ?>