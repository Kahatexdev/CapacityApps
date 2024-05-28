<?php $this->extend('Aps/layout'); ?>
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
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Planning Order for area <?= $area ?> needle <?= $jarum ?>  Total <?= $mesin ?> Machine 
                        </h5>
                        <a href="<?= base_url('aps/detailplnmc/'.$id_pln.'?judul='.$judul.'&area='.$area.'&jarum='.$jarum) ?>" class="btn btn-secondary ml-auto">Back</a>
                    </div>

                </div>
                <div class="card-body p-3">
                <form action="<?= base_url('aps/saveplanning'); ?>" method="post">
                <?php foreach($planning as $key => $item): ?>
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">No Model</label>
                                <input class="form-control" type="text" name="model" value="<?= $item['model'] ?>" readonly id="model-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Delivery</label>
                                <?php
                                    // Format delivery date to Y-m-d for input value
                                    $deliveryDate = date('Y-m-d', strtotime($item['delivery']));
                                ?>
                                <input class="form-control" type="date" name="delivery" value="<?= $deliveryDate ?>" readonly id="delivery-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Qty</label>
                                <input class="form-control" type="text" name="qty" value="<?= $item['qty'] ?>" readonly id="qty-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Remaining Qty</label>
                                <input class="form-control" type="text" name="sisa" value="<?= $item['sisa'] ?>" readonly id="remaining-qty-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Target 100%</label>
                                <input class="form-control" type="text" name="targetawal" value="<?= round(3600/$item['smv'],2) ?>" readonly id="target-100-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group row">
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">Percentage</label>
                                    <div class="input-group">
                                        <input class="form-control" type="number" name="persen_target" value="80" min="50" max="100" id="percentage-<?= $key ?>" oninput="calculateTarget(<?= $key ?>)" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label"><span style="color: orange">Target</span></label>
                                    <input class="form-control" type="text" name="target_akhir" value="" readonly id="calculated-target-<?= $key ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group row">
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">Start
                                        <span id="available_machine" class="ml-2">(Available : 0)</span>
                                    </label>
                                    <div class="input-group">
                                        <?php
                                            // Get today's date
                                            $todayDate = date('Y-m-d');
                                        ?>
                                        <input class="form-control" type="date" name="start_date" value="<?= $todayDate ?>" id="start-date-<?= $key ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">Stop</label>
                                    <?php
                                        // Calculate stop date 3 days before delivery
                                        $stopDate = date('Y-m-d', strtotime('-3 days', strtotime($item['delivery'])));
                                    ?>
                                    <div class="input-group">
                                        <input class="form-control stop-date" type="date" name="stop_date" value="<?= $stopDate ?>" id="stop-date-<?= $key ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group row">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="" class="form-control-label"><span style="color: orange">Days</span> (Exclude Holidays)</label>
                                        <input class="form-control days-count" type="number" name="days_count" readonly id="days-count-<?= $key ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">Holiday Count</label>
                                    <div class="input-group">
                                        <input class="form-control holiday-count" type="number" name="holiday_count" readonly id="holiday-count-<?= $key ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group row">
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">Unplanned Qty</label>
                                    <div class="input-group">
                                        <input class="form-control holiday-count" type="number" name="unplanned_qty" readonly id="unplanned-qty-<?= $key ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">
                                        <span style="color: orange">Machines Usages</span>
                                        <span id="machine_suggestion" class="ml-2">(Suggested: 0)</span>
                                    </label>
                                    <input class="form-control" type="number" id="machine_count" name="machine_usage" oninput="calculateEstimatedQty()" required>
                                </div>    
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Estimated Qty <span style="color: orange">(Target x Days x Machine Usage)</span></label>
                                <input class="form-control estimated-qty" type="number" value="" name="estimated_qty" id="estimated-qty-<?= $key ?>" readonly>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>

                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="id_save" value=<?= $id_save ?>>
                            <input type="hidden" name="id_pln" value=<?= $id_pln ?>>
                            <input type="hidden" name="mesin" value=<?= $mesin ?>>
                            <input type="hidden" name="area" value=<?= $area ?>>
                            <input type="hidden" name="jarum" value=<?= $jarum ?>>
                            <button type="submit" class="btn btn-info btn-block" style="width: 100%;">Save Planning</button>
                        </div>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                <?php foreach($planning as $key => $items): ?>
                    <h5>
                        Detail Planning for Model <?= $items['model'] ?> & Delivery <?= date('d-M-Y',strtotime($items['delivery'])); ?>
                <?php endforeach ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Start Machine</th>
                                    <th>Stop Machine</th>
                                    <th>Precentage of Target</th>
                                    <th>Target</th>
                                    <th>Days</th>                                    
                                    <th>Machine</th>
                                    <th>Estimated Production</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $no = 1;
                                    foreach ($listPlanning as $order) : ?>
                                    <tr>
                                        <td style="text-align: center; vertical-align: middle;"><?= $no++; ?></td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= date('d-M-Y', strtotime($order['start_date'])); ?></td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= date('d-M-Y', strtotime($order['stop_date'])); ?></td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($order['precentage_target']); ?> %</td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($order['target']); ?> Dz</td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($order['hari']); ?> Days</td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($order['mesin']); ?> Mc</td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= number_format($order['Est_qty'], 0, '.', ','); ?> Dz</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" style="text-align: right;">Total Estimated Production:</th>
                                    <th id="total-est-qty" style="text-align: center; vertical-align: middle;"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function() {
        var table = $('#dataTable').DataTable({
            // Add your DataTables options here if needed
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();
                var total = api.column(7, { page: 'current' }).data().reduce(function (acc, val) {
                    var num = parseFloat(val.replace(/[^\d.-]/g, '')); // Extract numeric values
                    return acc + (isNaN(num) ? 0 : num); // Add numeric values, treat NaN as 0
                }, 0);

                // Update the footer with the total sum
                $('#total-est-qty').text(total.toLocaleString('en', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' Dz');
            }
        });

        // Optionally, recalculate the total on table redraw (e.g., when sorting or filtering)
        table.on('draw', function() {
            table.api().draw(false);
        });
    });

    function calculateTarget(key) {
        var percentageInput = document.getElementById('percentage-' + key);
        var percentage = parseFloat(percentageInput.value);
        
        // Check if the value is not a number or if it's less than or equal to 0 or greater than 100
        if (percentage <= 4 || percentage > 100) {
            // Handle the invalid input (e.g., display an error message)
            percentageInput.value = 80; // Clear the input
            return; // Exit the function
        }
        
        var target100Input = document.getElementById('target-100-' + key);
        var target100 = parseFloat(target100Input.value);
        var calculatedTarget = (target100 * (percentage / 100)).toFixed(2);
        document.getElementById('calculated-target-' + key).value = calculatedTarget + " (" + percentage + "%)";
        fillMachineSuggestion();
    }

    // Add event listener to percentage input fields
    var percentageInputs = document.querySelectorAll('input[name="persen_target"]');
    percentageInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            // Get the key from the input id
            var key = input.id.split('-').pop();
            calculateTarget(key);
        });
        
        // Add blur event listener
        input.addEventListener('blur', function() {
            fillMachineSuggestion(); // Call your desired function when the input loses focus
        });
    });

    function initCalculations() {
        var keys = <?= json_encode(array_keys($planning)) ?>;
        keys.forEach(function(key) {
            calculateTarget(key);
        });
        calculateDaysCount(); // Calculate days count on initial load
        fillMachineSuggestion();
    }

    function calculateDaysCount(callback) {
        var startDateString = document.querySelector('input[name="start_date"]').value;
        var stopDateString = document.querySelector('.stop-date').value;
        var startDate = new Date(startDateString);
        var stopDate = new Date(stopDateString);
        var isoStartDate = startDate.toISOString().split('T')[0];
        var isoStopDate = stopDate.toISOString().split('T')[0];

        $.ajax({
            url: '<?php echo base_url("aps/getDataLibur") ?>',
            type: 'POST',
            dataType: 'json',
            data: { startDate: isoStartDate, endDate: isoStopDate },
            success: function(response) {
                if (response.status == 'success') {
                    var totalHolidays = response.total_libur;
                    var totalDays = ((stopDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                    if (totalDays < 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Dates',
                            text: 'Stop date and start date are invalid.',
                        }).then((result) => {
                        // Reset the stop date
                        var deliveryDate = new Date(document.getElementById('delivery-<?= $key ?>').value);
                        var newStopDate = new Date(deliveryDate.getTime() - (3 * 24 * 60 * 60 * 1000)); // 3 days before delivery

                        document.querySelector('.stop-date').value = newStopDate.toISOString().split('T')[0];
                        calculateDaysCount(function() {
                            fillMachineSuggestion(); // Call fillMachineSuggestion() after calculateDaysCount() finishes
                        });
                        });
                    }
                    var daysWithoutHolidays = totalDays - totalHolidays;

                    document.querySelector('.days-count').value = daysWithoutHolidays;
                    document.querySelector('.holiday-count').value = totalHolidays;

                    calculateEstimatedQty(); // Calculate estimated quantity after days count is updated

                    // Call the callback function if it's provided
                    if (typeof callback === 'function') {
                        callback();
                    }
                    fillMachineSuggestion();
                } else {
                    console.error('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + error);
            }
        });
    }

    function calculateEstimatedQty() {
        var daysCount = parseFloat(document.querySelector('.days-count').value);
        var machineCount = parseFloat(document.getElementById('machine_count').value);
        var targetPercentageInput = document.querySelector('[id^="calculated-target-"]').value;
        var targetPercentage = parseFloat(targetPercentageInput.split(' ')[0]);

        if (!isNaN(daysCount) && !isNaN(machineCount) && !isNaN(targetPercentage)) {
            var estimatedQty = daysCount * machineCount * targetPercentage;
            document.querySelector('.estimated-qty').value = estimatedQty.toFixed(2);
        }
    }

    function fillMachineSuggestion(){
        var daysCount = parseFloat(document.querySelector('.days-count').value);
        var targetPercentageInput = document.querySelector('[id^="calculated-target-"]').value;
        var targetPercentage = parseFloat(targetPercentageInput.split(' ')[0]);
        
        var remainingQty = parseFloat(document.querySelector('[id^="unplanned-qty-"]').value);
        var machineSuggestion = remainingQty / daysCount / targetPercentage;
        if (machineSuggestion < 0) {
            machineSuggestion = 0; // Set machineSuggestion to 0
        }
        document.getElementById('machine_suggestion').innerText = "(Suggested: " + machineSuggestion.toFixed(2) + " Mc)";
    }

    function fillUnplannedQty() {
        var remainingQty = parseFloat(document.querySelector('[id^="remaining-qty-"]').value);
        var totalEstQty = parseFloat(document.getElementById('total-est-qty').innerText.replace(/[^\d.-]/g, ''));
        var unplannedQty = Math.ceil(remainingQty - totalEstQty); // Round up the unplanned quantity
        document.getElementById('unplanned-qty-<?= $key ?>').value = unplannedQty.toFixed(2);

        // Get the save button element
        var saveButton = document.querySelector('button[type="submit"]');
        // Check if unplannedQty is less than or equal to 0
        if (unplannedQty <= 0) {
            // If unplannedQty is less than or equal to 0, disable the save button and change its text
            saveButton.disabled = true;
            saveButton.textContent = 'Qty Has Been Planned Successfully';
        } else {
            // If unplannedQty is greater than 0, enable the save button and revert its text
            saveButton.disabled = false;
            saveButton.textContent = 'Save Planning';
        }
    }
    

    document.querySelector('.stop-date').addEventListener('change', calculateDaysCount);
    document.querySelector('input[name="start_date"]').addEventListener('change', calculateDaysCount);
    window.onload = function() {
    initCalculations(); // Assuming this function is synchronous and doesn't involve AJAX calls
    calculateDaysCount(function() {
        fillMachineSuggestion(); // Call fillMachineSuggestion() after calculateDaysCount() finishes
        fillUnplannedQty();
    });
};
</script>

<?php $this->endSection(); ?>