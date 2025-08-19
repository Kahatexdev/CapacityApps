<?php $this->extend($role . '/layout'); ?>
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
                            <button type="button" class="btn btn-sm btn-success bg-gradient-success shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#exportDataOrder"><i class="fas fa-file-export text-lg opacity-10" aria-hidden="true"></i> Excel</button>


                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="exportDataOrder" tabindex="-1" role="dialog" aria-labelledby="exportDataOrder" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Export Data Order</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="exportDataOrderForm" action="<?= base_url($role . '/exportDataBooking'); ?>" method="POST">
                    <div class="modal-body align-items-center">
                        <div class="form-group">
                            <label for="buyer" class="col-form-label">Buyer</label>
                            <select class="form-control" id="buyer" name="buyer">
                                <option></option>
                                <?php foreach ($buyer as $buy) : ?>
                                    <option><?= $buy['kd_buyer_order'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jarum" class="col-form-label">Jarum</label>
                            <select class="form-control" id="jarum" name="jarum">
                                <option></option>
                                <option value="13">13</option>
                                <option value="84">84</option>
                                <option value="92">92</option>
                                <option value="96">96</option>
                                <option value="106">106</option>
                                <option value="108">108</option>
                                <option value="116">116</option>
                                <option value="120">120</option>
                                <option value="124">124</option>
                                <option value="126">126</option>
                                <option value="144">144</option>
                                <option value="168">168</option>
                                <option value="240">240</option>
                                <option value="POM-POM">POM-POM</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tgl_turun_order" class="col-form-label">Tgl Booking Dari</label>
                            <input type="date" class="form-control" name="tgl_booking">
                        </div>
                        <div class="form-group">
                            <label for="tgl_turun_order" class="col-form-label">Tgl Booking Sampai</label>
                            <input type="date" class="form-control" name="tgl_booking_akhir">
                        </div>
                        <div class="form-group">
                            <label for="awal" class="col-form-label">Delivery Dari</label>
                            <input type="date" class="form-control" name="awal">
                        </div>
                        <div class="form-group">
                            <label for="akhir" class="col-form-label">Delivery Sampai</label>
                            <input type="date" class="form-control" name="akhir">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Generate</button>
                    </div>
                </form>
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
                <a href="<?= base_url($role . '/databookingbulan/' . $jr['jarum']) ?>">
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