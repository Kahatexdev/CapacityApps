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
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Data Produksi <?= $area ?> <?= $bulan ?>
                        </h5>
                        <a href="<?= base_url($role . '/dataproduksi/') ?>" class="btn bg-gradient-info"> Kembali</a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable" class="display  striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tgl Produksi</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">PDK</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Produksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produksi as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['tgl_produksi']; ?></td>
                                            <td class="text-sm"><?= $order['mastermodel']; ?></td>
                                            <td class="text-sm"><?= $order['size']; ?></td>
                                            <td class="text-sm"><?= $order['delivery']; ?></td>
                                            <td class="text-sm"><?= $order['qty_produksi']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>



                </div>


            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "order": [
                        [0, "desc"]
                    ]
                });
            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>