<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-4">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $title ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-8 text-end">
                            <form action="<?= base_url($role . '/exportSummaryPerTod') ?>" method="post" ?>
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
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Running (Days)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Buyer</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">No Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Jarum</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">No Model</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Style</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Color</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Qty Shipment (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total Shipment (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Prod (Bruto)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Prod (Netto)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Prod (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Shipment (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">% Prod</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Bs Setting (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Qty + Pck (dz)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $prevSize = null;
                                    $sisa_ship_prev = [];

                                    foreach ($uniqueData as $key => $id) :
                                        $today = date('Y-m-d'); // Get current date in yyyy-mm-dd format
                                        $delivery_date = $id['delivery']; // Assuming $id['delivery'] is also in yyyy-mm-dd format

                                        // Calculate remaining days
                                        $sisa_hari = (strtotime($delivery_date) - strtotime($today)) / (60 * 60 * 24);

                                        // Group key
                                        $group_key = $id['machinetypeid'] . '_' . $id['mastermodel'] . '_' . $id['size'];

                                        // Initialize sisa_ship_prev for new groups
                                        if (!isset($sisa_ship_prev[$group_key])) {
                                            $sisa_ship_prev[$group_key] = null;
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['area'] : ''; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['seam'] : ''; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= $delivery_date; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= $sisa_hari; ?> days</td>
                                            <?php
                                            foreach ($total_prod as $sm) {
                                                if ($sm['mastermodel'] == $id['mastermodel'] && $sm['size'] == $id['size']) {
                                            ?>
                                                    <td class="text-sm" style="text-align: center;"><?= $sm['start_mc']; ?></td>
                                            <?php
                                                    break;
                                                }
                                            }
                                            ?>
                                            <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['running'] : ''; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['buyer'] : ''; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['no_order'] : ''; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= $id['machinetypeid']; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= $id['mastermodel']; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['size'] : ''; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['color'] : ''; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= number_format($id['qty_deliv'] / 24, 2); ?></td>
                                            <?php
                                            // Resetting the variable for each iteration
                                            $production_found = false;
                                            $shipping_found = false;

                                            foreach ($total_ship as $row) {
                                                if ($row['mastermodel'] == $id['mastermodel'] && $row['size'] == $id['size']) {
                                            ?>
                                                    <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($row['ttl_ship'] / 24, 2) : ''; ?></td>
                                                <?php
                                                    $shipping_found = true;
                                                    break;
                                                }
                                            }

                                            // Jika tidak ditemukan pengiriman, tampilkan baris kosong
                                            if (!$shipping_found) {
                                                ?>
                                                <td class="text-sm" style="text-align: center;">0.00</td>
                                                <?php
                                            }

                                            foreach ($total_prod as $pr) {
                                                if ($pr['mastermodel'] == $id['mastermodel'] && $pr['size'] == $id['size']) {
                                                    $bruto = $pr['qty_produksi'] ?? 0;
                                                    $bs_st = $pr['bs_prod'] ?? 0;
                                                    $netto = $bruto - $bs_st ?? 0;
                                                    $production_found = true;

                                                    //sisa per inisial
                                                    $sisa = $row['ttl_ship'] - $netto ?? 0;
                                                    if ($sisa > 0) {
                                                        $sisa;
                                                    } else {
                                                        $sisa = 0;
                                                    }

                                                    // Initialize sisa_ship for the first calculation
                                                    if ($sisa_ship_prev[$group_key] === null) {
                                                        $sisa_ship = $id['qty_deliv'] - $netto;
                                                    } else {
                                                        // Calculate sisa for each shipment based on previous sisa_ship
                                                        if ($sisa_ship_prev[$group_key] < 0) {
                                                            $sisa_ship = $id['qty_deliv'] + $sisa_ship_prev[$group_key];
                                                        } else {
                                                            $sisa_ship = $id['qty_deliv'];
                                                        }
                                                    }

                                                    // Calculate percentage
                                                    $persentase = ($row['ttl_ship'] != 0) ? ($netto / $row['ttl_ship']) * 100 : 0;

                                                    // Update sisa_ship_prev for the next iteration
                                                    $sisa_ship_prev[$group_key] = $sisa_ship;
                                                ?>
                                                    <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($bruto / 24, 2) : ''; ?></td>
                                                    <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($netto / 24, 2) : ''; ?></td>
                                                    <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($sisa / 24, 2) : ''; ?></td>
                                                    <td class="text-sm" style="text-align: center;"><?= ($sisa_ship > 0 ? number_format($sisa_ship / 24, 2) : '0.00'); ?></td>
                                                    <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($persentase, 2) . '%' : ''; ?></td>
                                                    <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($bs_st, 2) : ''; ?></td>
                                                    <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($pr['plus_packing'], 2) : ''; ?></td>
                                                <?php
                                                    break;
                                                }
                                            }
                                            // Jika tidak ditemukan produksi, tampilkan baris kosong
                                            if (!$production_found) {
                                                ?>
                                                <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? '0.00' : '' ?></td>
                                                <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? '0.00' : '' ?></td>
                                                <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? '0.00' : '' ?></td>
                                                <td class="text-sm" style="text-align: center;">0.00</td>
                                                <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? '0.00%' : '' ?></td>
                                                <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? '0.00' : '' ?></td>
                                                <td class="text-sm" style="text-align: center;"><?= ($id['mastermodel'] . $id['size'] != $prevSize) ? '0.00' : '' ?></td>
                                            <?php
                                            }
                                            ?>
                                        </tr>
                                    <?php
                                        $prevSize = $id['mastermodel'] . $id['size'];
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