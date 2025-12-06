<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row mt-2 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <h6>Grafik Confirm Order per Week</h6>
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

        <?php
        $currentWeek = null; // Initialize a variable to keep track of the current week
        $subtotal = 0; // Initialize a variable to store the subtotal

        // Loop through each detail
        foreach ($details as $index => $detail) {
            // If the week number changes, close the previous card and start a new one for the current week
            if ($detail['week_number'] != $currentWeek) {
                // If it's not the first week, close the previous card and output the subtotal
                if ($currentWeek !== null) {
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>';
                    echo '<td colspan="2"><strong>Total:</strong></td>';
                    echo '<td><strong>' . number_format(round($subtotal), 0, ',', '.') . ' Dz</strong></td>'; // Add "Dz" here
                    echo '</tr>';
                    echo '</tfoot>';
                    echo '</table></div></div></div>'; // Closing tags for previous card
                    echo '<hr>'; // Add separation between cards
                }

                // Start a new card for the current week
                $currentWeek = $detail['week_number'];
                $subtotal = 0; // Reset the subtotal for the new week
        ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0 d-flex justify-content-between">
                            <h6>Confirm Order Week <?php
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
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Confirm Weekdate</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Buyer</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qty Confirm</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th> <!-- Add Actions column header -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                }
                                    ?>
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
                                            <p class="text-xs font-weight-bold mb-0"><?= $detail['kd_buyer_order'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0"><?= number_format(round($detail['qty_turun'] / 24), 0, ',', '.') ?> Dz</p>
                                        </td>
                                        <td> <!-- Actions column -->
                                            <form id="detailForm" action="<?= base_url($role . '/detailturunorder/' . $detail['week_number'] . '/' . $detail['kd_buyer_order']) ?>" method="POST">
                                                <button type="submit" class="btn btn-success">Detail</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                // Add the current quantity to the subtotal
                                $subtotal += $detail['qty_turun'] / 24;

                                // If it's the last detail, close the last card and output the final subtotal
                                if ($index === count($details) - 1) {
                                    echo '</tbody>';
                                    echo '<tfoot>';
                                    echo '<tr>';
                                    echo '<td colspan="2"><strong>Total:</strong></td>';
                                    echo '<td><strong>' . number_format(round($subtotal), 0, ',', '.') . ' Dz</strong></td>'; // Add "Dz" here
                                    echo '<td></td>'; // Add an empty column for consistency
                                    echo '</tr>';
                                    echo '</tfoot>';
                                    echo '</table></div></div></div>'; // Closing tags for last card
                                }
                            }
                                ?>


                            </div>
                        </div>


                    </div>
                </div>
    </div>
</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("barChartCancelBooking").getContext("2d");

        var labels = [];
        var data = [];
        <?php foreach ($totalChart as $bulan => $total) : ?>
            labels.push("<?= $bulan; ?>");
            data.push(<?= $total; ?>);
        <?php endforeach; ?>

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Confirm in Dz',
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

<?php $this->endSection(); ?>