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
        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Cancel Booking</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cancel Weekdate</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Buyer</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qty Cancel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($details as $detail) : ?>
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    <?php
                                                    // Assuming $detail['week_number'] contains the week number in the format 'YYYYWW'
                                                    $year = substr($detail['week_number'], 0, 4); // Extract year from week number
                                                    $week = substr($detail['week_number'], 4, 2); // Extract week from week number

                                                    // Calculate the date of the first day of the week
                                                    $first_day_of_week = date('Y-m-d', strtotime($year . 'W' . $week)); 

                                                    // Calculate the month of the week
                                                    $month_of_week = date('F', strtotime($first_day_of_week)); 

                                                    // Get the week number relative to the month
                                                    $week_of_month = ceil(date('d', strtotime($first_day_of_week)) / 7); 

                                                    // Get the ordinal suffix for the week number (e.g., 1st, 2nd, 3rd)
                                                    if ($week_of_month % 10 == 1 && $week_of_month != 11) {
                                                        $suffix = 'st';
                                                    } elseif ($week_of_month % 10 == 2 && $week_of_month != 12) {
                                                        $suffix = 'nd';
                                                    } elseif ($week_of_month % 10 == 3 && $week_of_month != 13) {
                                                        $suffix = 'rd';
                                                    } else {
                                                        $suffix = 'th';
                                                    }

                                                    // Output the formatted date with month, year, week number, and suffix
                                                    echo $month_of_week . ' ' . $year . ' ' . $week_of_month . $suffix . ' Week';
                                                    ?>
                                                </p>
                                            </td>

                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $detail['kd_buyer_booking'] ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?= $detail['qty'] ?></p>
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