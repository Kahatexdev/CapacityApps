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
                            Pick Data for Planning by Needle, Title <?= $jarum; ?> 
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Machine Total</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Brand</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Machine Running</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">PU</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Input Machine</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datamc as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['area']; ?></td>
                                            <td class="text-sm"><?= $order['jarum']; ?></td>
                                            <td class="text-sm"><?= $order['total_mc']; ?> Mc</td>
                                            <td class="text-sm"><?= $order['brand']; ?></td>
                                            <td class="text-sm"><?= $order['mesin_jalan']; ?> Mc</td>
                                            <td class="text-sm"><?= $order['pu']; ?></td>
                                            <td class="text-sm">
                                                <input type="number" class="input-machine" value="<?= $order['mesin_jalan']; ?>" data-order-id="<?= $order['id']; ?>" min="0" style="width: 80px;">
                                            </td>
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
            var table = $('#dataTable').DataTable({
                "order": []
            });
            
            $('#dataTable tbody').on('input', '.input-machine', function() {
                var row = table.row($(this).closest('tr'));
                var machineTotal = parseInt(row.data()[2].replace(/\D/g, '')); // Extract numeric value from "Machine Total" column
                var inputMachine = parseInt($(this).val().trim());

                // Check if inputMachine exceeds machineTotal
                if (inputMachine > machineTotal) {
                    // Display SweetAlert
                    Swal.fire({
                        icon: 'warning',
                        title: 'Input Machine exceeds Machine Total',
                        text: 'Setting Input Machine to Machine Total.',
                    });

                    // Set input value to machineTotal
                    $(this).val(machineTotal);
                }
            });

        });
        </script>




        <?php $this->endSection(); ?>