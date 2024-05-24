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
                    </div>

                </div>
                <div class="card-body p-3">
                <?php foreach($planning as $key => $item): ?>
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">No Model</label>
                                <input class="form-control" type="text" value="<?= $item['model'] ?>" readonly id="model-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Delivery</label>
                                <?php
                                    // Format delivery date to Y-m-d for input value
                                    $deliveryDate = date('Y-m-d', strtotime($item['delivery']));
                                ?>
                                <input class="form-control" type="date" value="<?= $deliveryDate ?>" readonly id="delivery-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Qty</label>
                                <input class="form-control" type="text" value="<?= $item['qty'] ?>" readonly id="qty-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Remaining Qty</label>
                                <input class="form-control" type="text" value="<?= $item['sisa'] ?>" readonly id="remaining-qty-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Target 100%</label>
                                <input class="form-control" type="text" value="<?= round(3600/$item['smv'],2) ?>" readonly id="target-100-<?= $key ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group row">
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">Percentage</label>
                                    <div class="input-group">
                                        <input class="form-control" type="number" value="80" min="0" max="100" id="percentage-<?= $key ?>" oninput="calculateTarget(<?= $key ?>)">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">Target</label>
                                    <input class="form-control" type="text" value="" readonly id="calculated-target-<?= $key ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group row">
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">Start</label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="start" readonly value="<?= date('d-M-Y') ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label for="" class="form-control-label">Stop</label>
                                    <?php
                                        // Calculate stop date 3 days before delivery
                                        $stopDate = date('Y-m-d', strtotime('-3 days', strtotime($item['delivery'])));
                                    ?>
                                    <div class="input-group">
                                        <input class="form-control" type="date" name="stop" value="<?= $stopDate ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>

                </div>

                <div class="card-footer">

                </div>

            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Detail Booking To Order
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="display">
                            <thead>

                                <th>
                                    No Model
                                </th>
                                <th>Buyer Order</th>
                                <th>Order Placement Date</th>
                                <th>Qty Order</th>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Booking -->



</div>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    function calculateTarget(key) {
        var percentage = document.getElementById('percentage-' + key).value;
        var target100 = document.getElementById('target-100-' + key).value;
        var calculatedTarget = (target100 * (percentage / 100)).toFixed(2) + " (" + percentage + "%)";
        document.getElementById('calculated-target-' + key).value = calculatedTarget;
    }

    function initCalculations() {
        var keys = <?= json_encode(array_keys($planning)) ?>;
        keys.forEach(function(key) {
            calculateTarget(key);
        });
    }

    window.onload = initCalculations;
    document.getElementById('cancelBookingBtn').addEventListener('click', cancelBooking);
</script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>