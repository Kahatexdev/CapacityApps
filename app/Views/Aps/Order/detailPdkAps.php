<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
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
                    <div class="row justify-content-between align-items-center mb-3">
                        <div class="col-auto">
                            <h5>
                                Detail Data Model <?= esc($noModel) ?>
                            </h5>
                        </div>
                        <div class="col-auto">
                            <form action="<?= base_url($role . '/exportDataOrderArea') ?>" method="post">
                                <input type="hidden" value="<?= $area ?>" name="area">
                                <input type="text" value="<?= $noModel ?>" name="searchModel" hidden>
                                <button type="submit" class="btn bg-gradient-success"> <i class="fas fa-file-excel"></i> Export</button>
                                <a href="<?= base_url($role . '/dataorderperarea/' . $area) ?>" class="btn bg-gradient-info">Kembali</a>
                            </form>
                        </div>
                    </div>



                </div>

                <div class="card-body">
                    <?php foreach ($order as $style => $val): ?>
                        <div class="row mt-3">
                            <div class="d-flex justify-content-between align-item-center">
                                <h5> <span class='badge  badge-pill badge-lg bg-gradient-info'> <?= $style ?> </span></h5>
                                <h5> <span class='badge  badge-pill badge-lg bg-gradient-info'>Qty Order <?= round($val['totalQty'] / 24) ?> dz</span></h5>

                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">JO</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Buyer</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">No Order</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Delivery</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Qty</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Sisa</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Product Type</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">SMV</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Area</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($val as $key => $list): ?>
                                            <?php if (is_array($list)): ?>
                                                <?php
                                                $machine = strtoupper($list['machinetypeid'] ?? '');
                                                $pembagi = in_array($machine, ['240', '240-PL']) ? 12 : 24;
                                                $qty = $list['qty'] ?? 0;
                                                $sisa = $list['sisa'] ?? 0;
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($list['mastermodel'] ?? '-') ?>/<?= $key + 1 ?> <?= htmlspecialchars($machine) ?></td>
                                                    <td><?= htmlspecialchars($list['buyer'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($list['no_order'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($list['delivery'] ?? '-') ?></td>
                                                    <td><?= round($qty / $pembagi) ?> dz</td>
                                                    <td><?= round($sisa / $pembagi) ?> dz</td>
                                                    <td><?= htmlspecialchars($list['product_type'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($list['smv'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($list['factory'] ?? '-') ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <hr>
                            </div>
                        </div>
                    <?php endforeach ?>
                    <div class=" card-footer">
                        <div>
                            <br>

                        </div>
                    </div>



                </div>

                <div class="card-body">
                    <div class="row mt-3">
                        <div class="d-flex justify-content-between align-item-center">
                            <h5> <span class='badge  badge-pill badge-lg bg-gradient-info'>History Revisi</span></h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Tanggal Revisi</th>
                                        <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historyRev as $key): ?>
                                        <tr>
                                            <td><?= $key['tanggal_rev'] ?></td>
                                            <td><?= $key['keterangan'] ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <hr>
                        </div>
                    </div>
                    <div class=" card-footer">
                        <div>
                            <br>

                        </div>
                    </div>
                </div>

            </div>
        </div>




        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable();





            });
            document.getElementById('selectAll').addEventListener('click', function(e) {
                var checkboxes = document.querySelectorAll('.delivery-checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = e.target.checked;
                });
            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>