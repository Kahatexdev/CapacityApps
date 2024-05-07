<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
<?php $this->extend('Planning/layout'); ?>
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
                        List Data Machine Choosen by Needle <strong style="color: orange;"><?= $jarum ?></strong>
                    </h5>
                    <h5>
                        Title <strong style="color: orange;"><?= $judul ?></strong>
                    </h5>
                    <h5>
                        Machine Requirement <strong style="color: orange;"><?= $mesin ?></strong>
                    </h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable" class="display compact striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Brand</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Machine On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datamc as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['area']; ?></td>
                                            <td class="text-sm"><?= $order['jarum']; ?></td>
                                            <td class="text-sm"><?= $order['brand']; ?></td>
                                            <td class="text-sm"><?= $order['mc_nyala']; ?> Mc</td>
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
                $('#dataTable').DataTable();
            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>