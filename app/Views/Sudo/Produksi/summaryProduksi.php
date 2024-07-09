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
                            <?= $title ?>
                        </h5>
                        <div class="col-8 text-end">
                            <form action="<?= base_url($role . '/exportSummaryPerTgl/') ?>" method="post" ?>
                                <input type="hidden" class="form-control" name="buyer" value="<?= $dataFilter['buyer'] ?>">
                                <input type="hidden" class="form-control" name="jarum" value="<?= $dataFilter['jarum'] ?>">
                                <input type="hidden" class="form-control" name="pdk" value="<?= $dataFilter['pdk'] ?>">
                                <button type="submit" class="btn btn-info"><i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Report Excel</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="example" class="table table-border" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Seam</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Delivery</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Hari</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Jalan Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Buyer</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">No Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Jarum</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Style</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">Qty Delivery</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Prod (Bruto)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Prod (Netto)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Prod</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Shipment</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">% Prod</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Running</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($uniqueData as $key => $id) :
                                    ?>
                                        <tr>
                                            <td class="text-sm"><?= $id['seam']; ?></td>
                                            <td class="text-sm"><?= $id['delivery']; ?></td>
                                            <td class="text-sm"> days</td>
                                            <td class="text-sm"><?= $id['running']; ?> days</td>
                                            <td class="text-sm"><?= $id['buyer']; ?></td>
                                            <td class="text-sm"><?= $id['no_order']; ?></td>
                                            <td class="text-sm"><?= $id['machinetypeid']; ?></td>
                                            <td class="text-sm"><?= $id['mastermodel']; ?></td>
                                            <td class="text-sm"><?= $id['size']; ?></td>
                                            <td class="text-sm"><?= number_format($id['qty_deliv'] / 24, 2); ?></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"><?= number_format($id['bruto'] / 24, 2); ?></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"><?= $id['ttl_jlmc']; ?> mc</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>