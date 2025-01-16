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
                                    Estimasi SPK 2 di Area <?= $area ?>
                                </h5>
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
                        <table id="example" class="display compact " style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">No Model</th>
                                    </th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Inisial</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Presentase Produksi</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">BS Stocklot</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">PO+</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Estimasi SPK 2</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($perStyle as $item): ?>
                                    <tr>
                                        <td class="text-xs"><?= $item['model']; ?></td>
                                        <td class="text-xs"><?= $item['inisial']; ?></td>
                                        <td class="text-xs"><?= $item['size']; ?></td>
                                        <td class="text-xs"><?= $item['jarum']; ?></td>
                                        <td class="text-xs"><?= round($item['qty'] / 24); ?> dz</td>
                                        <td class="text-xs"><?= round($item['sisa'] / 24); ?>dz</td>
                                        <td class="text-xs"><span class="badge bg-info"><?= $item['percentage']; ?>% </span></td>
                                        <td class="text-xs"><?= $item['bs']; ?>pcs</td>
                                        <td class="text-xs"><?= $item['poplus']; ?>pcs</td>
                                        <td class="text-xs"><?= $item['estimasi']; ?>pcs</td>
                                    <?php endforeach ?>
                                    </tr>
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
                $('#example').DataTable({
                    "order": [
                        [0, 'desc'] // Kolom pertama (indeks 0) diurutkan secara descending
                    ]
                });

                // Trigger import modal when import button is clicked

            });
        </script>
        <?php $this->endSection(); ?>