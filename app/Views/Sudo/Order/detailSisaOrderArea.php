<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Sisa Produksi <?= $area ?> Bulan <?= date('F', strtotime($bulan)) ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <form action="<?= base_url($role . '/excelSisaOrderArea/' . $area) ?>" method="post" ?>
                                <input type="hidden" class="form-control" name="buyer" value="<?= $area ?>">
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
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="example" class="table table-border" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">NO MODEL</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">NEEDLE</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">AREA</th>
                                        <?php for ($i = 1; $i <= $maxWeek; $i++) { ?>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="5">WEEK <?= $i ?></th>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <?php for ($i = 1; $i <= $maxWeek; $i++) { ?>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">DEL</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">QTY</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">PROD</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">SISA</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">JLN MC</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php
                                        $rowsModel = 0;
                                        foreach ($allData as $noModel => $id) {
                                            $rowsModel = count($id);
                                            foreach ($id as $jarum => $rowJarum) {
                                                $rowsArea = count($rowJarum);
                                                if ($rowsArea > 1) {
                                                    $rowsModel += $rowsArea - 1;
                                                }
                                            }
                                        ?>
                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;" rowspan="<?= $rowsModel ?>"><?= $noModel ?></td>
                                            <?php foreach ($id as $jarum => $id2) {
                                                $rowsJarum = count($id2); ?>
                                                <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;" rowspan="<?= $rowsJarum ?>"><?= $jarum ?></td>
                                                <?php foreach ($id2 as $area => $id3) { ?>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $area ?></td>
                                                    <?php for ($i = 1; $i <= $maxWeek; $i++) {
                                                        // Mengecek apakah week ada di data
                                                        if (isset($id3[$i])) {
                                                            // Ambil data per week
                                                            $del = $id3[$i]['del'];
                                                            $qty = $id3[$i]['qty'] ?? 0;
                                                            $prod = $id3[$i]['prod'] ?? 0;
                                                            $sisa = $id3[$i]['sisa'] ?? 0;
                                                            $jlMc = $id3[$i]['jlMc'] ?? 0;

                                                            // Menampilkan data dari week yang ditemukan
                                                    ?>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $del ?></td>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $qty ?></td>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $prod ?></td>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $sisa ?></td>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"> <?= $jlMc ?></td>
                                                        <?php
                                                        } else {
                                                            // Jika data week tidak ditemukan, tampilkan kolom kosong
                                                        ?>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"></td>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"></td>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"></td>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"></td>
                                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"></td>
                                                    <?php
                                                        }
                                                    }
                                                    ?>

                                    </tr>
                        <?php
                                                    // sinii

                                                }
                                            }
                                        } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total</td>
                                        <?php for ($i = 1; $i <= $maxWeek; $i++) { ?>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"></td>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= isset($totalPerWeek[$i]) ? $totalPerWeek[$i] : 0 ?></td>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= isset($totalProdPerWeek[$i]) ? $totalProdPerWeek[$i] : 0 ?></td>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= isset($totalSisaPerWeek[$i]) ? $totalSisaPerWeek[$i] : 0 ?></td>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= isset($totalJlMcPerWeek[$i]) ? $totalJlMcPerWeek[$i] : 0 ?></td>
                                        <?php } ?>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-1">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <h5 class="font-weight-bolder mb-0">
                            Sisa Produksi Perjarum
                        </h5>
                        <div class="table-responsive">
                            <table id="example" class="table table-border" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">NEEDLE</th>
                                        <!-- untuk menampilkan banyak week -->
                                        <?php for ($i = 1; $i <= $maxWeek; $i++) { ?>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="4">WEEK <?= $i ?></th>
                                        <?php } ?>                                        
                                    </tr>
                                    <tr>
                                        <?php for ($i = 1; $i <= $maxWeek; $i++) { ?>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">QTY</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">PROD</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">SISA</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">JLN MC</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php foreach ($allDataJrm as $jarum => $idJrm) { ?>
                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $jarum ?></td>
                                            <?php
                                            // Inisialisasi looping week dari 1 hingga maksimal week ($maxWeek)
                                            for ($i = 1; $i <= $maxWeek; $i++) {
                                                // Mengecek apakah week ada di data
                                                if (isset($idJrm[$i])) {
                                                    // Ambil data per week
                                                    $qtyJrm = $idJrm[$i]['qtyJrm'] ?? 0;
                                                    $prodJrm = $idJrm[$i]['prodJrm'] ?? 0;
                                                    $sisaJrm = $idJrm[$i]['sisaJrm'] ?? 0;
                                                    $jlMcJrm = $idJrm[$i]['jlMcJrm'] ?? 0;

                                                    // Menampilkan data dari week yang ditemukan
                                            ?>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $qtyJrm ?></td>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $prodJrm ?></td>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $sisaJrm ?></td>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $jlMcJrm ?></td>
                                                <?php
                                                } else {
                                                    // Jika data week tidak ditemukan, tampilkan kolom kosong
                                                ?>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"></td>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"></td>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"></td>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"></td>
                                            <?php
                                                }
                                            }
                                            ?>
                                    </tr>

                                <?php  } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total</td>
                                        <?php for ($i = 1; $i <= $maxWeek; $i++) { ?>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= isset($totalPerWeekJrm[$i]) ? $totalPerWeekJrm[$i] : 0 ?></td>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= isset($totalProdPerWeekJrm[$i]) ? $totalProdPerWeekJrm[$i] : 0 ?></td>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= isset($totalSisaPerWeekJrm[$i]) ? $totalSisaPerWeekJrm[$i] : 0 ?></td>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= isset($totalJlMcPerWeekJrm[$i]) ? $totalJlMcPerWeekJrm[$i] : 0 ?></td>
                                        <?php } ?>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>