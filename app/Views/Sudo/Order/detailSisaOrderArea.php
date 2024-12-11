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
                                    Sisa Produksi <?= $area ?> Bulan <?= date('F', strtotime($bulan)) ?> - <?= date('Y', strtotime($bulan)) ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <form method="post" action="<?= base_url(session()->get('role') . '/sisaOrderArea/' . $area); ?>">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <input type="hidden" value="<?= $area ?>" name="area">
                                        <div class="form-group">
                                            <select class="form-control" id="planSelect" name="month">
                                                <option value="">Pilih Bulan</option>
                                                <?php foreach ($months as $month): ?>
                                                    <option value="<?= $month; ?>"><?= $month; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <select class="form-control" id="planSelect" name="year">
                                                <option value="">Pilih Tahun</option>
                                                <?php foreach (array_keys($years) as $year): ?>
                                                    <option value="<?= $year; ?>"><?= $year; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-info">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                        </div>
                        <div class="col-4 text-end">
                            <form action="<?= base_url($role . '/excelSisaOrderArea/' . $area) ?>" method="post" ?>
                                <input type="hidden" class="form-control" name="buyer" value="<?= $area ?>">
                                <input type="hidden" class="form-control" name="month" value="<?= $bulan; ?>">
                                <button type="submit" class="btn btn-info"><i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Report Excel</button>

                                <a href="<?= base_url($role . '/sisaOrderArea') ?>" class="btn bg-gradient-dark">
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
                                            $rowsJarum = 0;
                                            foreach ($id as $jarum => $rowJarum) {
                                                $rowsJarum = count($rowJarum);
                                                if ($rowsJarum > 1) {
                                                    $rowsModel += $rowsJarum - 1;
                                                }
                                                $rowsArea = 0;
                                                foreach ($rowJarum as $area => $rowArea) {
                                                    for ($i = 1; $i <= $maxWeek; $i++) {
                                                        if (isset($rowArea[$i])) {
                                                            $rowsArea = count($rowArea[$i]);
                                                            $rowDelivery = count($rowArea[$i]);
                                                            if ($rowDelivery > 1) {
                                                                $rowsModel += $rowDelivery - 1;
                                                                $rowsJarum += $rowDelivery - 1;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        ?>
                                            <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;" rowspan="<?= $rowsModel ?>"><?= $noModel ?></td>
                                            <?php foreach ($id as $jarum => $id2) { ?>
                                                <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;" rowspan="<?= $rowsJarum ?>"><?= $jarum ?></td>
                                                <?php foreach ($id2 as $area => $id3) { ?>
                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;" rowspan="<?= $rowsArea ?>"><?= $area ?></td>
                                                    <?php for ($i = 1; $i <= $maxWeek; $i++) {
                                                        if (isset($id3[$i])) {
                                                            $numRows = count($id3[$i]);
                                                            $numRows2 = 1;
                                                            foreach ($id3[$i] as $index => $data) {
                                                                $parsedData = json_decode($data, true);
                                                                if ($parsedData) {
                                                                    // Menampilkan data yang sudah di-parse
                                                    ?>
                                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $parsedData['del'] ?? '-' ?></td>
                                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $parsedData['qty'] ?? 0 ?></td>
                                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $parsedData['prod'] ?? 0 ?></td>
                                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $parsedData['sisa'] ?? 0 ?></td>
                                                                    <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $parsedData['jlMc'] ?? 0 ?></td>
                                                            <?php
                                                                } else {
                                                                    // Jika week tidak ditemukan
                                                                    echo str_repeat("<td class='text-uppercase text-dark text-xxs font-weight opacity-7 ps-2' style='text-align: center;'>-</td>", 5);
                                                                }
                                                                $colsEnd = 5 * ($maxWeek - $i);
                                                                $colsStart = 5 * ($i - 1);
                                                                if ($numRows > 1 && $numRows2 < $numRows) {
                                                                    echo "<td colspan=$colsEnd></td>";
                                                                    echo "</tr>";
                                                                    echo "<td colspan=$colsStart></td>";
                                                                }
                                                                $numRows2++;
                                                            }
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