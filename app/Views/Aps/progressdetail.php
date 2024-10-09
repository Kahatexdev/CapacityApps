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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Progress Detail</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $model ?>
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



    <div class="row my-3">
        <div class="card">
            <div class="col-lg-12">
                <div class="card-header">

                    <h5>
                        Order status <?= $model ?>
                    </h5>
                </div>
                <div class="card-body">

                    <div id="progress-container">
                        <?php foreach ($perjarum as $jarum => $key): ?>
                            <div class="row mt-3" style="padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
                                <div class="col-lg-2">
                                    <h6 class="font-weight-bold" style="color: #333;"><?= $key['jarum'] ?></h6>
                                </div>
                                <div class="col-lg-8">
                                    <div class="progress-wrapper">
                                        <div class="progress-info">
                                            <span class="text-sm font-weight-bold" style="color: #555;">
                                                <?= $key['percentage'] ?>% (<?= $key['target'] - $key['remain'] ?> dz / <?= $key['target'] ?> dz)
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div id="<?= $key['mastermodel'] ?>-progress-bar"
                                                class="progress-bar <?php if ($key['percentage'] < 100): ?>
                                                        bg-gradient-info
                                                    <?php elseif ($key['percentage'] == 100): ?>
                                                        bg-gradient-success
                                                    <?php else: ?>
                                                        bg-gradient-danger
                                                    <?php endif; ?>"
                                                role="progressbar"
                                                style="width: <?= $key['percentage'] ?>%; height: 8px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $key['jarum'] ?>" aria-expanded="false">
                                        Details
                                    </button>
                                </div>
                            </div>

                            <!-- Section for collapsible details -->
                            <div class="collapse" id="collapse-<?= $key['jarum'] ?>" style="padding-left: 20px;">
                                <?php foreach ($key['detail'] as $deliveryDate => $row): ?>
                                    <div class="row mt-2">
                                        <div class="col-lg-2">
                                            <h6 class="text-muted"> <?= $deliveryDate ?></h6>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="progress-wrapper">
                                                <div class="progress-info">
                                                    <span class="text-sm font-weight-bold" style="color: #777;">
                                                        <?= $row['percentage'] ?>% (<?= $row['target'] - $row['remain'] ?> dz / <?= $row['target'] ?> dz)
                                                    </span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div id="<?= $row['mastermodel'] ?>-progress-bar"
                                                        class="progress-bar <?php if ($row['percentage'] < 100): ?>
                                                        bg-gradient-info
                                                    <?php elseif ($row['percentage'] == 100): ?>
                                                        bg-gradient-success
                                                    <?php else: ?>
                                                        bg-gradient-danger
                                                    <?php endif; ?>"
                                                        role="progressbar"
                                                        style="width: <?= $row['percentage'] ?>%; height: 6px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>


                </div>

            </div>
        </div>
    </div>

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

</script>
<?php $this->endSection(); ?>