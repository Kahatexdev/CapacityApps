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
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Planning Order for area <?= $area ?> needle <?= $jarum ?> Total <?= $mesin ?> Machine
                        </h5>
                        <a href="<?= base_url($role . '/detailplnmc/' . $id_pln) ?>" class="btn btn-secondary ml-auto">Back</a>
                    </div>

                </div>
                <div class="card-body p-3">
                    <form action="<?= base_url($role . '/saveplanning'); ?>" method="post">
                        <?php foreach ($planning as $key => $item) : ?>
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
                                        <input class="form-control" type="text" name="targetawal" value="<?= round(3600 / $item['smv'], 2) ?>" readonly id="target-100-<?= $key ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="form-group row">
                                        <div class="col-lg-6 col-sm-12">
                                            <label for="" class="form-control-label">Percentage</label>
                                            <div class="input-group">
                                                <input class="form-control" type="number" name="persen_target" value="80" readonly id="percentage-<?= $key ?>" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <label for="" class="form-control-label"><span style="color: orange">Target Aktual</span></label>
                                            <input class="form-control" type="text" name="target_akhir" value="" id="calculated-target-<?= $key ?>" oninput="updatePercentage(<?= $key ?>)">
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
                                                <input class="form-control holiday-count" type="number" name="holiday_count" oninput="updateLibur()" id="holiday-count-<?= $key ?>">
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
                            <input type="hidden" name="judul" value=<?= $judul ?>>
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
                    <?php foreach ($planning as $key => $items) : ?>
                        <h5>
                            Detail Planning for Model <?= $items['model'] ?> & Delivery <?= date('d-M-Y', strtotime($items['delivery'])); ?>
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
                                    <th>Action</th>
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
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;">
                                            <button class="btn btn-danger btn-update" data-toggle="modal" data-target="#modalUpdate" data-start="<?= $order['start_date'] ?>"
                                                data-idplan="<?= $order['id_detail_pln'] ?>"
                                                data-idpl="<?= $id_pln ?>"
                                                data-stop="<?= $order['stop_date'] ?>">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan=" 7" style="text-align: right;">Total Estimated Production:</th>
                                    <th id="total-est-qty" style="text-align: center; vertical-align: middle;"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdate" aria-hidden="true">
        <div class="modal-dialog   role=" document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Plan Mesin</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-lg-12 col-sm-6">
                                <div class="form-group">

                                    <input type="text" name="id">
                                    <input type="text" name="idpl">
                                    Anda yakin ingin menghapus?
                                </div>


                            </div>

                        </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn bg-gradient-danger">Hapus Data</button>
                </div>
                </form>
            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function() {
        var table = $('#dataTable').DataTable({
            // Add your DataTables options here if needed
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();
                var total = api.column(7, {
                    page: 'current'
                }).data().reduce(function(acc, val) {
                    var num = parseFloat(val.replace(/[^\d.-]/g, '')); // Extract numeric values
                    return acc + (isNaN(num) ? 0 : num); // Add numeric values, treat NaN as 0
                }, 0);

                // Update the footer with the total sum
                $('#total-est-qty').text(total.toLocaleString('en', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }) + ' Dz');
            }
        });
    });

    function calculateTarget(key) {
        var percentageInput = document.getElementById('percentage-' + key);
        var target100Input = document.getElementById('target-100-' + key);
        var target100 = parseFloat(target100Input.value);
        var percentage = parseFloat(percentageInput.value) || 80; // Default 80% jika kosong atau tidak valid

        // Validasi persentase antara 50 dan 100, jika tidak, set ke 80%
        if (isNaN(percentage) || percentage < 50 || percentage > 100) {
            percentage = 80;
            percentageInput.value = percentage; // Set persentase ke 80% sebagai default
        }

        // Hitung target akhir
        var calculatedTarget = (target100 * (percentage / 100)).toFixed(2);
        document.getElementById('calculated-target-' + key).value = calculatedTarget;

        fillMachineSuggestion();
    }

    // Tambahkan event listener untuk perubahan target_akhir
    function updatePercentage(key) {
        var target100Input = document.getElementById('target-100-' + key);
        var target100 = parseFloat(target100Input.value);
        var calculatedTargetInput = document.getElementById('calculated-target-' + key);
        var calculatedTarget = parseFloat(calculatedTargetInput.value);

        if (!isNaN(target100) && target100 > 0 && !isNaN(calculatedTarget)) {
            var newPercentage = ((calculatedTarget / target100) * 100).toFixed(2);

            // Update persentase di input persentase
            document.getElementById('percentage-' + key).value = newPercentage;
        }
    }




    $(document).on('click', '.btn-update', function() {
        var idplan = $(this).data('idplan');

        var idpl = $(this).data('idpl');
        $('#modalUpdate').find('form').attr('action', '<?= base_url($role . '/deleteplanmesin') ?>');
        $('#modalUpdate').find('input[name="id"]').val(idplan);
        $('#modalUpdate').find('input[name="idpl"]').val(idpl);




        $('#modalUpdate').modal('show'); // Show the modal
    });

    function initCalculations() {
        var keys = <?= json_encode(array_keys($planning)) ?>;
        keys.forEach(function(key) {
            calculateTarget(key);
        });
        calculateDaysCount(function() {
            fillUnplannedQty();
            fillMachineSuggestion();
            var startDate = document.querySelector('input[name="start_date"]').value;
            updateAvailableMachines(startDate); // Call updateAvailableMachines with the start date
        });
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
            data: {
                startDate: isoStartDate,
                endDate: isoStopDate
            },
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
                            var deliveryDate = new Date(document.getElementById('delivery-<?= $key ?>').value);
                            var newStopDate = new Date(deliveryDate.getTime() - (3 * 24 * 60 * 60 * 1000)); // 3 days before delivery
                            var newStartDate = new Date(newStopDate.getTime() - (7 * 24 * 60 * 60 * 1000)); // 7 days before delivery

                            document.querySelector('.stop-date').value = newStopDate.toISOString().split('T')[0];
                            document.querySelector('.start-date').value = newStartDate.toISOString().split('T')[0];
                            calculateDaysCount(function() {
                                fillMachineSuggestion();
                                fillUnplannedQty();
                            });
                        });
                    }
                    var daysWithoutHolidays = totalDays - totalHolidays;

                    document.querySelector('.days-count').value = daysWithoutHolidays;
                    document.querySelector('.holiday-count').value = totalHolidays;

                    calculateEstimatedQty();

                    if (typeof callback === 'function') {
                        callback();
                    }
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

    function fillMachineSuggestion() {
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
        var unplannedQty = Math.ceil(remainingQty - totalEstQty);
        document.getElementById('unplanned-qty-<?= $key ?>').value = unplannedQty.toFixed(2);

        var saveButton = document.querySelector('button[type="submit"]');
        if (unplannedQty <= 0) {
            saveButton.disabled = true;
            saveButton.textContent = 'Qty Has Been Planned Successfully';
        } else {
            saveButton.disabled = false;
            saveButton.textContent = 'Save Planning';
        }
    }

    function updateAvailableMachines(date) {
        $.ajax({
            url: '<?= base_url("aps/getMesinByDate/") . $id_pln ?>', // Adjust the URL to pass the ID if needed
            type: 'GET',
            dataType: 'json',
            data: {
                date: date
            },
            success: function(response) {
                if (response && response.available !== undefined) {
                    // Calculate the reduced available machines value
                    var reducedAvailableMachines = <?= $mesin ?> - response.available;

                    // Update the HTML element with the new value
                    $('#available_machine').text("(Available : " + reducedAvailableMachines + ")");
                } else {
                    console.error('Error: Invalid response format.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + error);
            }
        });
    }

    document.querySelector('input[name="start_date"]').addEventListener('change', function() {
        var startDate = this.value;
        updateAvailableMachines(startDate);
        calculateDaysCount(function() {
            fillMachineSuggestion();
            fillUnplannedQty();
        });
    });

    document.querySelector('.stop-date').addEventListener('change', function() {
        calculateDaysCount(function() {
            fillMachineSuggestion();
            fillUnplannedQty();
        });
    });

    var percentageInputs = document.querySelectorAll('input[name="persen_target"]');
    percentageInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            var key = input.id.split('-').pop();
            calculateTarget(key);
        });

        input.addEventListener('blur', function() {
            fillMachineSuggestion();
        });
    });

    initCalculations();
</script>

<?php $this->endSection(); ?>