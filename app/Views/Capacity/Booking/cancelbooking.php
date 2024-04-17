<?php $this->extend('Capacity/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row mt-2 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <h6>Monthly Booking Cancellation Chart</h6>
                    <div>
                        <a href="<?= base_url('capacity/databooking') ?>" class="btn btn-info">Back</a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="barChartCancelBooking" class="chart-canvas " height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php foreach ($details as $bulan => $dataBulan) : ?>
        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Cancel Booking <?= $bulan ?></h6>
                        <div>
                            <span class="badge bg-warning">Total : <?= count($dataBulan) ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cancel Date</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Buyer</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No Booking</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dataBulan as $detail) : ?>
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= date('d-m-Y', strtotime($detail['updated_at'])) ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $detail['kd_buyer_booking'] ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $detail['no_booking'] ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $detail['desc'] ?></p>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>


</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("barChartCancelBooking").getContext("2d");

        var labels = [];
        var data = [];
        <?php foreach ($totals as $bulan => $total) : ?>
            labels.push("<?= $bulan; ?>");
            data.push(<?= $total; ?>);
        <?php endforeach; ?>

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Cancel',
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1,
                    data: data,
                    barThickness: 50, // Ukuran bar diperkecil
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1, // Menambahkan stepSize untuk memastikan skala y hanya bilangan bulat
                            precision: 0 // Memastikan tidak ada nilai desimal
                        }
                    }]
                },
                legend: {
                    display: true
                },
                maintainAspectRatio: false
            }
        });
    });
</script>


<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>