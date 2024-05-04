<?php $this->extend('Planning/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Booking
                                </h5>
                            </div>
                        </div>
                        <div class="col-6 text-end">

                            <a href="<?= base_url('capacity/cancelBooking') ?>" class="btn btn-sm btn-warning bg-gradient-warning shadow text-center border-radius-md">
                                Summary Cancel Booking
                            </a>
                            <a href="<?= base_url('capacity/turunOrder') ?>" class="btn btn-sm btn-success bg-gradient-success shadow text-center border-radius-md">
                                Summary Placed Order
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
        <?php foreach ($TotalMesin as $jr) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url('planning/databookingbulan/' . $jr['jarum']) ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">

                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $jr['jarum'] ?></p>
                                        <!-- <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Machine : //$jr['total'] </p> -->
                                        <h5 class="font-weight-bolder mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <?php if (stripos($jr['jarum'], '10g') !== false || stripos($jr['jarum'], '13G') !== false) : ?>
                                            <i class="fas fa-mitten text-lg opacity-10" aria-hidden="true"></i>
                                        <?php elseif (stripos($jr['jarum'], '240N') !== false) : ?>
                                            <i class="fab fa-redhat text-lg opacity-10" aria-hidden="true"></i>
                                        <?php elseif (stripos($jr['jarum'], 'POM') !== false) : ?>
                                            <i class="fas fa-atom text-lg opacity-10" aria-hidden="true"></i>
                                        <?php else : ?>
                                            <i class="fas fa-socks text-lg opacity-10" aria-hidden="true"></i>
                                        <?php endif; ?>
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