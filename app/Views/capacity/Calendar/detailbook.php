<?php $this->extend($role . '/Calendar/booking'); ?>
<?php $this->section('generatebook'); ?>

<div class="row mt-5">
    <div class="col-lg-12">

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">

                    <h4 class="card-title"> Kebutuhan Mesin <?= $judul ?> </h4>

                </div>
            </div>
            <div class="card-body">
                <div class="row text">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-4 text-lg">
                                Tanggal Planning
                            </div>
                            <div class="col-lg-8 text-lg">
                                <strong>

                                    : <?= $tglplan ?>

                                </strong>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-4 text-lg">
                                Range Planning
                            </div>
                            <div class="col-lg-8 text-lg">
                                <strong>

                                    : <?= $range['awal'] ?> - <?= $range['akhir'] ?>

                                </strong>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <?php if ($booking != null): ?>
                        <h5 class="mt-3">
                            Data Booking
                        </h5>
                        <table class="table align-items-center mt-3" id="tablebook">
                            <thead class="bg-dark">
                                <tr class="text-center text-white">
                                    <th class="text-white">Jarum</th>
                                    <th class="text-white">Sisa Booking</th>
                                    <th class="text-white">Kebutuhan Mesin</th>

                                </tr>

                            </thead>
                            <tbody>
                                <?php foreach ($booking as $plan) : ?>

                                    <tr class="text-center">
                                        <td>
                                            <?= $plan['jarum'] ?>
                                        </td>
                                        <td>
                                            <?= $plan['sisa'] ?> dz
                                        </td>
                                        <td>
                                            <?= $plan['mesin'] ?>
                                        </td>

                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot>
                                <tr class="text-center">
                                    <th>Total</th>
                                    <th><?= $totalSisaBooking ?> dz</th>
                                    <th><?= $jumlahMcBooking ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php endif ?>
                    <?php if ($order != null): ?>
                        <h5 class="mt3">
                            Data Order
                        </h5>
                        <table class="table align-items-center mt-3" id="table">
                            <thead class="bg-dark">
                                <tr class="text-center text-white">
                                    <th class="text-white">Jarum</th>
                                    <th class="text-white">Sisa Order</th>
                                    <th class="text-white">Kebutuhan Mesin</th>

                                </tr>

                            </thead>
                            <tbody>
                                <?php foreach ($order as $plan) : ?>

                                    <tr class="text-center">
                                        <td>
                                            <?= $plan['jarum'] ?>
                                        </td>
                                        <td>
                                            <?= $plan['sisa'] ?> dz
                                        </td>
                                        <td>
                                            <?= $plan['mesin'] ?>
                                        </td>

                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot>
                                <tr class="text-center">
                                    <th>Total</th>
                                    <th><?= $totalSisaOrder ?> dz</th>
                                    <th><?= $jumlahMcOrder ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    <?php endif ?>

                </div>

            </div>
        </div>
    </div>

</div>


<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    let data = <?php echo json_encode($chartstat); ?>;
    console.log(data)
    // Ekstraksi tanggal dan jumlah produksi dari data
    let labels = data.map(item => item.jarum);
    console.log(labels)
    let values = data.map(item => item.mesin);
    console.log(values)



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
                    label: "Kebutuhan Mesin",
                    borderWidth: 0,
                    pointRadius: 30,

                    backgroundColor: "#3A416F",
                    fill: true,
                    data: values,
                    maxBarThickness: 20

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

    var ctx4 = document.getElementById("pie-chart").getContext("2d");

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
                backgroundColor: ['#17c1e8', '#cb0c9f', '#3A416F', '#a8b8d8'],
                data: values,
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