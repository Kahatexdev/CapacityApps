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
                            <form action="<?= base_url('capacity/exportSummaryPerTod') ?>" method="post" ?>
                                <input type="hidden" class="form-control" name="buyer" value="<?= $dataFilter['buyer'] ?>">
                                <input type="hidden" class="form-control" name="area" value="<?= $dataFilter['area'] ?>">
                                <input type="hidden" class="form-control" name="jarum" value="<?= $dataFilter['jarum'] ?>">
                                <input type="hidden" class="form-control" name="pdk" value="<?= $dataFilter['pdk'] ?>">
                                <button type="submit" class="btn btn-info"><i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Report Excel</button>
                                
                                <a href="<?= base_url($role . '/dataproduksi') ?>" class="btn bg-gradient-dark">
                                    <i class="fas fa-arrow-circle-left me-2 text-lg opacity-10"></i>
                                Back</a>
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Area</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Seam</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Delivery Shipment</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Hari</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Jalan Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Buyer</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">No Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Jarum</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">No Model</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Style</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Qty Shipment</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total Shipment</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Prod (Bruto)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Prod (Netto)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Prod</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Shipment</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">% Prod</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Bs Setting</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Running MC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $prevSize = null;
                                    foreach ($uniqueData as $key => $id) : 
                                        $today = date('Y-m-d'); // Get current date in yyyy-mm-dd format
                                        $delivery_date = $id['delivery']; // Assuming $id['delivery'] is also in yyyy-mm-dd format
                                
                                        // Calculate remaining days
                                        $sisa_hari = (strtotime($delivery_date) - strtotime($today)) / (60 * 60 * 24);
                                        ?>                                            
                                        <tr>
                                            <td class="text-sm"><?= ($id['size'] != $prevSize) ? $id['area'] : ''; ?></td>
                                            <td class="text-sm"><?= ($id['size'] != $prevSize) ? $id['seam'] : ''; ?></td>
                                            <td class="text-sm"><?= $delivery_date; ?></td>
                                            <td class="text-sm"><?= $sisa_hari; ?> days</td>
                                            <td class="text-sm"><?= $id['running']; ?> days</td>
                                            <td class="text-sm"><?= ($id['size'] != $prevSize) ? $id['buyer'] : ''; ?></td>
                                            <td class="text-sm"><?= ($id['size'] != $prevSize) ? $id['no_order'] : ''; ?></td>
                                            <td class="text-sm"><?= $id['machinetypeid']; ?></td>
                                            <td class="text-sm"><?= $id['mastermodel']; ?></td>
                                            <td class="text-sm"><?= $id['size']; ?></td>
                                            <td class="text-sm"><?= number_format($id['qty_deliv']/24, 2); ?> dz</td>
                                            <?php 
                                                if ($id['size'] != $prevSize) {
                                                    $total_ship_found = false;
                                                    foreach ($total_ship as $row) {
                                                        if ($row['mastermodel'] == $id['mastermodel'] && $row['size'] == $id['size']) {
                                                            $total_ship_found = true;
                                                            ?>
                                                            <td class="text-sm"><?= number_format($row['ttl_ship']/24, 2); ?> dz</td>
                                                            <?php
                                                            break;
                                                        }
                                                    }
                                                    if (!$total_ship_found) {
                                                        ?>
                                                        <td class="text-sm">0 dz</td> <!-- Or handle no match scenario -->
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <td class="text-sm"></td>
                                                    <?php
                                                }
                                            ?>
                                            <td class="text-sm"><?= ($id['size'] != $prevSize) ? number_format($id['bruto']/24, 2) . ' dz' : ''; ?></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"></td>
                                            <td class="text-sm"><?= $id['ttl_jlmc']; ?> mc</td>
                                        </tr>
                                    <?php 
                                    $prevSize = $id['size'];
                                    endforeach; ?>
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