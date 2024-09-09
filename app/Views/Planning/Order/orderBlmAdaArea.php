<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Semua Order
                                </h5>
                            </div>
                        </div>
                        <div>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalMessage" class="btn btn-success bg-gradient-info shadow text-center border-radius-md">
                                Input Data Order
                            </button>
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
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="display compact " style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Turun PDK</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Buyer</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Desc</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Seam</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Order</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Order</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tampildata as $order => $key) : ?>
                                <tr>
                                    <td class="text-xs"><?= $key['created_at'] ?></td>
                                    <td class="text-xs"><?= $key['kd_buyer_order'] ?></td>
                                    <td class="text-xs"><?= $key['no_model'] ?></td>
                                    <td class="text-xs"><?= $key['machinetypeid'] ?></td>
                                    <td class="text-xs"><?= $key['description'] ?></td>
                                    <td class="text-xs"><?= $key['seam'] ?></td>
                                    <td class="text-xs"><?= ceil($key['qty'] / 24) ?> dz</td>
                                    <td class="text-xs"><?= ceil($key['sisa'] / 24) ?> dz</td>
                                    <td class="text-xs">

                                        <a href="<?= base_url($role . '/detailPdk/' . $key['no_model'] . '/' . $key['machinetypeid']); ?>" <button type="button" class="btn btn-info btn-sm details-btn">
                                            Details
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
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

    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable({});
        });
    </script>
    <?php $this->endSection(); ?>