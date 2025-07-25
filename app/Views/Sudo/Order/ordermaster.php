<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
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
                                    Data Order
                                </h5>
                            </div>
                        </div>

                        <div class="col-4 text-end">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalMessage" class="btn btn-success bg-gradient-success shadow text-center border-radius-md">
                                Import Inisial
                            </button>
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- modal -->
    <div class="modal fade  bd-example-modal-lg" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Inisial</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body align-items-center">
                    <div class="row align-items-center">
                        <div id="drop-area" class="border rounded d-flex justify-content-center align-item-center mx-3" style="height:200px; width: 95%; cursor:pointer;">
                            <div class="text-center mt-5">
                                <i class="ni ni-cloud-upload-96" style="font-size: 48px;"></i>
                                <p class="mt-3" style="font-size: 28px;">Upload file here</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-9 pl-0">
                            <form action="<?= base_url($role . '/importinisial') ?>" id="modalForm" method="POST" enctype="multipart/form-data">
                                <input type="text" class="form-control" name="id_model" hidden>
                                <input type="text" class="form-control" name="no_model" hidden>
                                <input type="file" id="fileInput" name="excel_file" multiple accept=".xls , .xlsx" class="form-control ">
                        </div>
                        <div class="col-3 pl-0">
                            <form>
                                <!-- Other form inputs go here -->
                                <button type="submit" class="btn btn-info btn-block" onclick="this.disabled=true; this.form.submit();">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/blmAdaArea/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Order Not Yet Planned</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </a>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/semuaOrder/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">All Data Order</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/orderPerjarum/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Order Data Per Needle</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </a>

        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/orderPerArea/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Order Data Per Area</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </a>

        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/orderPerbulan/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Data Order Perbulan</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </a>

        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/statusOrder/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Status Order</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </a>

        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/pengajuanspk2/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">SPK 2</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </a>

        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/sisaOrder/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Sisa Order Buyer</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </a>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/sisaOrderArea/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Sisa Order Area</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </a>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/startStopMcByPdk') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Start - Stop Mc</p>
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>