<?php $this->extend('Planning/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Machine Detail Production Unit Cijerah and Majalaya
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Mesin</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Brand</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Mesin Mati</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Mesin Jalan</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Production Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tampildata as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['area']; ?></td>
                                            <td class="text-sm"><?= $order['jarum']; ?></td>
                                            <td class="text-sm"><?= $order['total_mc']; ?></td>
                                            <td class="text-sm"><?= $order['brand']; ?></td>
                                            <td class="text-sm"><?= $order['mesin_mati']; ?></td>
                                            <td class="text-sm"><?= $order['mesin_jalan']; ?></td>
                                            <td class="text-sm"><?= $order['pu']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "pageLength": 100, // Set default number of rows per page to 100
                    "footerCallback": function(row, data, start, end, display) {
                        var api = this.api();

                        var totalMesin = api.column(2, {
                            page: 'current'
                        }).data().reduce(function(a, b) {
                            return parseInt(a) + parseInt(b);
                        }, 0);

                        // Calculate the total of the 5th column (Remaining Qty in dozens) - index 4
                        var mesinJalan = api.column(4, {
                            page: 'current'
                        }).data().reduce(function(a, b) {
                            return parseInt(a) + parseInt(b);
                        }, 0);

                        var percentage = (mesinJalan / totalMesin) * 100;

                        // Format totalMesin and mesinJalan with " Mc" suffix and dots for thousands
                        var totalMesinFormatted = numberWithDots(totalMesin) + " Mc";
                        var mesinJalanFormatted = numberWithDots(mesinJalan) + " Mc";

                        // Update the footer cell for the total Qty
                        $(api.column(2).footer()).html(totalMesinFormatted);

                        // Update the footer cell for the total Mesin Jalan
                        $(api.column(4).footer()).html(mesinJalanFormatted);

                        // Update the footer cell for the percentage
                        $(api.column(6).footer()).html(percentage.toFixed(2) + '%');
                    },
                    "order": []
                });
            });

            // Function to add dots for thousands
            function numberWithDots(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>