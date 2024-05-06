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
                            Pick Data for Planning by Needle  <strong style="color: orange;"><?= $jarum; ?></strong>
                        </h5>
                    </div>
                    
                    <div>
                        <h6>
                            Machine Requirement is <strong style="color: orange;"><?= $mesin; ?> Machine </strong>
                        </h6>
                    </div>
                    <div>
                        <h6>
                            Status : <?= $status; ?>
                        </h6>
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
                                                <input type="number" class="input-machine" value="<?= $order['mesin_jalan']; ?>" data-order-id="<?= $order['id']; ?>" min="0" style="width: 80px;" inputmode="numeric"> Machine
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-right">Subtotal:</th>
                                        <th class="subtotal-column"></th>
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
            var table = $('#dataTable').DataTable({
                "order": []
            });

            // Calculate and display subtotal in footer
            function calculateSubtotal() {
                var subtotal = 0;
                $('#dataTable tbody tr').each(function() {
                    var machineTotal = parseInt($(this).find('td:eq(2)').text().replace(/\D/g, ''));
                    var inputMachine = parseInt($(this).find('.input-machine').val().trim());
                    subtotal += Math.min(machineTotal, inputMachine);
                });
                $('#dataTable tfoot .subtotal-column').text(subtotal);
            }

            // Event listener for input fields
            $('#dataTable tbody').on('input', '.input-machine', function() {
                var row = table.row($(this).closest('tr'));
                var machineTotal = parseInt(row.data()[2].replace(/\D/g, ''));
                var inputMachine = parseInt($(this).val().trim());

                // Check if inputMachine exceeds machineTotal
                if (inputMachine > machineTotal) {
                    // Display SweetAlert
                    Swal.fire({
                        icon: 'warning',
                        title: 'Input Machine exceeds Machine Total',
                        text: 'Please set the running machines correctly.',
                    });

                    // Set input value to machineTotal
                    $(this).val(machineTotal);
                }

                // Recalculate and update subtotal
                calculateSubtotal();
            });

            // Initial calculation of subtotal
            calculateSubtotal();
        });
        </script>




        <?php $this->endSection(); ?>