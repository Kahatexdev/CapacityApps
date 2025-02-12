<?php $this->extend('User/layout'); ?>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Status Bahan Baku
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
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="display compact " style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">No Model</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Buyer</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Delivery Awal</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Delivery Akhir</th>

                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr>

                                <td class="text-xs">



                                    <a href="<?= base_url($role . '/detailBB/idorder/nomodel'); ?>" <button type="button" class="btn btn-info btn-sm details-btn">
                                        Details
                                        </button>
                                    </a>

                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>
<div class="row my-3">

</div>


</div>


</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>

<?php $this->endSection(); ?>