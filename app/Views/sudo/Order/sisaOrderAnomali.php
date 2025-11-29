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

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="display compact" style="width:100%">
                        <thead>
                            <tr class="text-center">
                                <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                                <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty (pcs)</th>
                                <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa (pcs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order as $dt): ?>
                                <tr class="text-center">
                                    <td><?= $dt['mastermodel'] ?></td>
                                    <td><?= $dt['factory'] ?></td>
                                    <td><?= $dt['delivery'] ?></td>
                                    <td><?= $dt['qty'] ?> pcs</td>
                                    <td><?= $dt['sisa'] ?> pcs</td>
                                </tr>
                            <?php endforeach ?>
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


</div>


<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#example').DataTable({
            "processing": true,
            "pageLength": 100,

        })
    })
</script>




<?php $this->endSection(); ?>