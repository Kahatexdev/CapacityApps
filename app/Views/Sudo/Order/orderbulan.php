<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Order Per Bulan
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">

                            <a href="<?= base_url($role . '/dataorder') ?>" class="btn btn-sm bg-gradient-dark shadow text-center border-radius-md d-inline-flex align-items-center">
                                <i class="fas fa-arrow-circle-left text-lg opacity-10" aria-hidden="true" style="margin-right: 0.5rem;"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
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

        <?php foreach ($bulan as $jr) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/orderPerMonth/' . $jr['bulan'] . '/' . $jr['tahun']) ?>">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3">
                            <!-- Header Bulan -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="text-sm text-capitalize font-weight-bold mb-0">
                                    <strong><?= $jr['bulan'] ?> <?= $jr['tahun'] ?></strong>
                                </p>

                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="fas fa-calendar-alt text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                            <!-- Info Qty dan Sisa -->
                            <div class="p-3 bg-light rounded-3 mb-3 shadow-sm">
                                <div class="row mb-3">
                                    <div class="col-4 border-end">
                                        <p class="text-xxs mb-0 text-center">Total Qty</p>
                                        <p class="text-xxs mb-0 text-center"><strong><?= number_format($jr['qtyAll']) ?> dz</strong></p>
                                    </div>

                                    <div class="col-4 border-end">
                                        <p class="text-xxs mb-0 text-center">Total Sisa</p>
                                        <p class="text-xxs mb-0 text-center"><strong><?= number_format($jr['sisaAll']) ?> dz</strong></p>
                                    </div>

                                    <div class="col-4 border-end">
                                        <p class="text-xxs mb-0 text-center">Total Act Mc</p>
                                        <p class="text-xxs mb-0 text-center"><strong><?= number_format($jr['actAll']) ?> mc</strong></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Baris 1: By Jarum -->
                            <div class="p-3 bg-light rounded-3 mb-3 shadow-sm">
                                <!-- Header -->
                                <p class="text-xs text-primary fw-bold mb-2">Total TJ</p>

                                <!-- Total Section -->
                                <div class="row mb-3">
                                    <!-- <div class="col-12">
                                        <p class="text-xs text-secondary fw-bold mb-1">Total</p>
                                    </div> -->
                                    <div class="col-4 border-end">
                                        <p class="text-xxs mb-0 text-center">Qty</p>
                                        <p class="text-xxs mb-0 text-center"><strong><?= number_format($jr['qtyTj']) ?> dz</strong></p>
                                    </div>
                                    <div class="col-4 border-end">
                                        <p class="text-xxs mb-0 text-center">Sisa</p>
                                        <p class="text-xxs mb-0 text-center"><strong><?= number_format($jr['sisaTj']) ?> dz</strong></p>
                                    </div>
                                    <div class="col-4">
                                        <p class="text-xxs mb-0 text-center">Actual Mc</p>
                                        <p class="text-xxs mb-0 text-center"><strong><?= number_format($jr['actualTj']) ?> mc</strong></p>
                                    </div>
                                </div>

                                <!-- Opentoe Section -->
                                <div class="row mb-3">
                                    <div class="col-6 border-end">
                                        <p class="text-xs text-primary fw-bold mb-2">Opentoe</p>
                                        <p class="text-xxs mb-0">Qty: <strong><?= number_format($jr['qtyRossoTj']) ?> dz</strong></p>
                                        <p class="text-xxs mb-0">Sisa: <strong><?= number_format($jr['sisaRossoTj']) ?> dz</strong></p>
                                        <p class="text-xxs mb-0">Act Mc: <strong><?= number_format($jr['actualRossoTj']) ?> mc</strong></p>
                                    </div>

                                    <!-- Closedtoe Section -->
                                    <div class="col-6">
                                        <p class="text-xs text-primary fw-bold mb-2">Closedtoe</p>
                                        <p class="text-xxs mb-0">Qty: <strong><?= number_format($jr['qtyAutolinkTj']) ?> dz</strong></p>
                                        <p class="text-xxs mb-0">Sisa: <strong><?= number_format($jr['sisaAutolinkTj']) ?> dz</strong></p>
                                        <p class="text-xxs mb-0">Act Mc: <strong><?= number_format($jr['actualAutolinkTj']) ?> mc</strong></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Baris 2: By Seam -->
                            <div class="p-2 bg-light rounded-3">
                                <p class="text-xs text-success fw-bold mb-2">Total JC</p>

                                <!-- Total Section -->
                                <div class="row mb-3">
                                    <!-- <div class="col-12">
                                        <p class="text-xs text-secondary fw-bold mb-1">Total</p>
                                    </div> -->
                                    <div class="col-4 border-end">
                                        <p class="text-xxs mb-0 text-center">Qty</p>
                                        <p class="text-xxs mb-0 text-center"><strong><?= number_format($jr['qtyJc']) ?> dz</strong></p>
                                    </div>
                                    <div class="col-4 border-end">
                                        <p class="text-xxs mb-0 text-center">Sisa</p>
                                        <p class="text-xxs mb-0 text-center"><strong><?= number_format($jr['sisaJc']) ?> dz</strong></p>
                                    </div>
                                    <div class="col-4">
                                        <p class="text-xxs mb-0 text-center">Actual Mc</p>
                                        <p class="text-xxs mb-0 text-center"><strong><?= number_format($jr['actualJc']) ?> mc</strong></p>
                                    </div>
                                </div>

                                <!-- Opentoe Section -->
                                <div class="row mb-3">
                                    <div class="col-6 border-end">
                                        <p class="text-xs text-success fw-bold mb-2">Opentoe</p>
                                        <p class="text-xxs mb-0">Qty: <strong><?= number_format($jr['qtyRossoJc']) ?> dz</strong></p>
                                        <p class="text-xxs mb-0">Sisa: <strong><?= number_format($jr['sisaRossoJc']) ?> dz</strong></p>
                                        <p class="text-xxs mb-0">Act Mc: <strong><?= number_format($jr['actualRossoJc']) ?> mc</strong></p>
                                    </div>

                                    <!-- Closedtoe Section -->
                                    <div class="col-6">
                                        <p class="text-xs text-success fw-bold mb-2">Closedtoe</p>
                                        <p class="text-xxs mb-0">Qty: <strong><?= number_format($jr['qtyAutolinkJc']) ?> dz</strong></p>
                                        <p class="text-xxs mb-0">Sisa: <strong><?= number_format($jr['sisaAutolinkJc']) ?> dz</strong></p>
                                        <p class="text-xxs mb-0">Act Mc: <strong><?= number_format($jr['actualAutolinkJc']) ?> mc</strong></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>