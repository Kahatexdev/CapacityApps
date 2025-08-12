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
                            <form action="<?= base_url($role . '/exportTimter') ?>" method="post" ?>
                                <input type="hidden" class="form-control" name="area" value="<?= $dataFilter['area'] ?>">
                                <input type="hidden" class="form-control" name="jarum" value="<?= $dataFilter['jarum'] ?>">
                                <input type="hidden" class="form-control" name="pdk" value="<?= $dataFilter['pdk'] ?>">
                                <input type="hidden" class="form-control" name="awal" value="<?= $dataFilter['awal'] ?>">
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Seam</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Buyer</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">No Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Jarum</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">No Model</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">In</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Style</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Color</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Smv</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Delivery</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Target</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Jml Mc</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">No MC</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">A</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">B</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">C</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">PA</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">Total</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">Produksi</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">Qty Delivery</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">Total Produksi</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">Sisa Produksi</th>
                                    </tr>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($uniqueData as $key => $id) :
                                        if (count($id['no_mesin']) > 0) {
                                            $rows = count($id['no_mesin']);
                                        } else {
                                            $rows = 1;
                                        }
                                        // dd($rows);
                                        $smv = $id['smv'];
                                        if (!empty($smv)) {
                                            $target = 86400 / floatval($smv) * 0.8 / 24;
                                        } else {
                                            $target = 0;
                                        }
                                        // Ambil countjlMc untuk ukuran dan model tertentu
                                        $countjlMc = 0;
                                        foreach ($jlMC as $jl) {
                                            if ($jl['mastermodel'] == $id['mastermodel'] && $jl['size'] == $id['size']) {
                                                $countjlMc = $jl['jl_mc'];
                                                break; // Hentikan loop setelah menemukan count
                                            }
                                        }

                                        // Jika tidak ada countjlMc, set ke 1 untuk menampilkan setidaknya satu baris
                                        $countjlMc = $countjlMc ?: 0;

                                    ?>
                                        <tr>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['seam']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['kd_buyer_order']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['no_order']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['machinetypeid']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['mastermodel']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['inisial']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['size']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['color']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['smv']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $id['delivery']; ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= number_format($target, 0); ?></td>
                                            <td class="text-sm" style="text-align: center;" rowspan="<?= $rows ?>"><?= $countjlMc; ?></td>
                                            <?php
                                            if (empty($id['no_mesin'])) {
                                            ?>
                                                <td class="text-sm" style="text-align: center;" colspan="11"></td>
                                                <td class="text-sm"><?= floor($id['ttl_dz'] / 24); ?></td>
                                                <td class="text-sm"><?= ($id['ttl_dz'] % 24); ?></td>
                                                <td class="text-sm"><?= floor($id['qty'] / 24) ?></td>
                                                <td class="text-sm"><?= ($id['qty'] % 24) ?></td>
                                                <td class="text-sm"><?= floor($id['qty_prod'] / 24) ?></td>
                                                <td class="text-sm"><?= ($id['qty_prod'] % 24) ?></td>
                                                <td class="text-sm"><?= floor($id['sisa'] / 24) ?></td>
                                                <td class="text-sm"><?= ($id['sisa'] % 24) ?></td>
                                        </tr>
                                        <?php
                                            } else {

                                                $loopIndex = 0;
                                                foreach ($id['no_mesin'] as $noMc => $row) {
                                                    // Inisialisasi variabel 
                                                    $shift_a = $shift_b = $shift_c = $pa = 0;
                                                    $pcs_a = $pcs_b = $pcs_c = $pcs_pa = 0;

                                                    // Menggunakan null coalescing untuk mengatur nilai default 0 jika null
                                                    $no_mesin = $row['no_mesin'] ?? 0;
                                                    // Hitung dz
                                                    $shift_a = $row['shift_a'] ?? 0;
                                                    $shift_b = $row['shift_b'] ?? 0;
                                                    $shift_c = $row['shift_c'] ?? 0;
                                                    $pa = $row['pa'] ?? 0;

                                                    // Hitung pcs
                                                    $pcs_a = $shift_a % 24;
                                                    $pcs_b = $shift_b % 24;
                                                    $pcs_c = $shift_c % 24;
                                                    $pcs_pa = $pa % 24;

                                                    // Hitung total dz & pcs
                                                    $total_dz = $row['total_shift'];
                                                    $total_pcs = $row['total_shift'] % 24;

                                        ?>
                                            <td class="text-sm"><?= $noMc ?></td>
                                            <td class="text-sm"><?= floor($shift_a / 24); ?></td>
                                            <td class="text-sm"><?= $pcs_a; ?></td>
                                            <td class="text-sm"><?= floor($shift_b / 24); ?></td>
                                            <td class="text-sm"><?= $pcs_b; ?></td>
                                            <td class="text-sm"><?= floor($shift_c / 24); ?></td>
                                            <td class="text-sm"><?= $pcs_c; ?></td>
                                            <td class="text-sm"><?= floor($pa / 24); ?></td>
                                            <td class="text-sm"><?= $pcs_pa; ?></td>
                                            <td class="text-sm"><?= floor($total_dz / 24); ?></td>
                                            <td class="text-sm"><?= $total_pcs; ?></td>
                                            <?php
                                                    if ($loopIndex == 0) {
                                            ?>
                                                <td class="text-sm"><?= floor($id['ttl_dz'] / 24) ?></td>
                                                <td class="text-sm"><?= ($id['ttl_dz'] % 24) ?></td>
                                                <td class="text-sm"><?= floor($id['qty'] / 24) ?></td>
                                                <td class="text-sm"><?= ($id['qty'] % 24) ?></td>
                                                <td class="text-sm"><?= floor($id['qty_prod'] / 24) ?></td>
                                                <td class="text-sm"><?= ($id['qty_prod'] % 24) ?></td>
                                                <td class="text-sm"><?= floor($id['sisa'] / 24) ?></td>
                                                <td class="text-sm"><?= ($id['sisa'] % 24) ?></td>
                                            <?php
                                                    }
                                            ?>
                                            </tr>
                                    <?php
                                                    $loopIndex++;
                                                }
                                            }
                                    ?>

                                <?php
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