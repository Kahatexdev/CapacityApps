<?php
// Initialize variables
$total_mc_nyala = 0;
$percentage = 0;
$total_mc = 0;

// Calculate total_mc_nyala and percentage
foreach ($datamc as $order) {
    $total_mc_nyala += $order['mc_nyala'];
    $total_mc += $order['total_mc'];
}
if ($mesin != 0) { // Avoid division by zero error
    $percentage = ($total_mc_nyala / $mesin) * 100;
}
?>
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
                    <div>
                        <h4 class="card-title">Machine Utilization: <?= number_format($percentage, 2); ?>%</h4>
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Machine</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Current Running Machine</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Planning Machine</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Percentage Planning Machine</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datamc as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['area']; ?></td>
                                            <td class="text-sm"><?= $order['jarum']; ?></td>
                                            <td class="text-sm"><?= $order['brand']; ?></td>
                                            <td class="text-sm"><?= $order['total_mc']; ?> Mc</td>
                                            <td class="text-sm"><?= $order['mesin_jalan']; ?> Mc</td>
                                            <td class="text-sm"><strong class="font-weight-bold" style="color: green;"><?= $order['mc_nyala']; ?> Mc</strong></td>
                                            <?php 
                                            $percentage = ($order['mc_nyala'] / $order['total_mc']) * 100; // Calculate percentage
                                            ?>
                                            <td class="text-sm"><strong class="font-weight-bold" style="color: green;"><?= number_format($percentage, 2); ?> %</strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end font-weight-bold">Total:</td>
                                        <td><?= $total_mc; ?> Mc</td>
                                        <td></td>
                                        <td><strong class="font-weight-bold" style="color: green;"><?= $total_mc_nyala; ?> Mc</strong></td>
                                        <td><strong class="font-weight-bold" style="color: green;"><?= number_format(($total_mc_nyala / $total_mc) * 100, 2); ?> %</strong></td>
                                    </tr>
                                </tfoot>
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
