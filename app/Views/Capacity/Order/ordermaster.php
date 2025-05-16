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
                                    Data Booking Berdasarkan Jarum
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalMessage" class="btn btn-success bg-gradient-success shadow text-center border-radius-md">
                                Input Data Order
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
    <div class="modal fade  bd-example-modal-lg" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Input Data Order</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url($role . '/inputOrderManual') ?>" method="post">
                        <div class="form-group">
                            <label for="tgl-bk-form-label">Tanggal Turun Order</label>
                            <input type="date" class="form-control" name="tgl_turun">
                        </div>
                        <div class="form-group">
                            <label for="No Model" class="col-form-label">No Model</label>
                            <input type="text" name="no_model" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="jarum" class="col-form-label">Needle</label>
                            <select class="form-control" id="jarum" name="jarum">
                                <option value="">Choose</option>
                                <?php foreach ($jenisJarum as $jj) : ?>
                                    <option value="<?= $jj['jarum'] ?>"><?= $jj['jarum'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="productType" class="col-form-label">Product Type</label>
                            <select class="form-control" id="productType" name="productType">
                                <option value="">Choose</option>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">

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
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">All Data Order by Needles</p>
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
            <a href="<?= base_url($role . '/orderPerjarumBln/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Data Order By Needle and Month</p>
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
            <a href="<?= base_url($role . '/belumImport/') ?>">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">

                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Order Not Yet Import</p>
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
                                    <p class="text-md mb-0 text-capitalize font-weight-bold">Order Status</p>
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

    </div>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    $('#jarum').change(function() {
        var selectedJarum = $(this).val();
        if (selectedJarum) {
            $.ajax({
                url: '<?= base_url($role . '/getTypebyJarum') ?>', // Ubah dengan URL controller Anda
                type: 'POST',
                data: {
                    jarum: selectedJarum
                },
                dataType: 'json',
                success: function(response) {

                    var productTypeSelect = $('#productType');
                    productTypeSelect.empty();
                    productTypeSelect.append('<option value="">Choose</option>'); // Tambahkan opsi default

                    // Iterasi melalui data yang diterima dan tambahkan ke select option
                    $.each(response, function(index, productType) {
                        productTypeSelect.append('<option value="' + productType + '">' + productType + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        } else {
            $('#productType').empty();
            $('#productType').append('<option value="">Choose</option>');
        }
    });
</script>
<?php $this->endSection(); ?>