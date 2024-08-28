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
            <form action="">
                <div class="col-lg-12" align="right">
                    <a href="" class="btn btn-save bg-gradient-info d-inline-flex align-items-center">
                        Save</a>
                </div>
                <!-- Menampilkan Planning MC Per Week -->
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if (!empty($kebutuhanMesin[$i])): ?>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3>Week-<?= $i ?> (<?= date('d-m', strtotime($monthlyData[$i - 1]['start_date'])) ?> - <?= date('d-m', strtotime($monthlyData[$i - 1]['end_date'])) ?>)</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="example" class="table table-border" style="width:100%">
                                            <thead>
                                                <tr class=" text-center">
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" rowspan="2" style="text-align: center;">Area</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" rowspan="2" style="text-align: center;">Jumlah MC</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" rowspan="2" style="text-align: center;">Planning MC</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" colspan="27" style="text-align: center;">Rincian Planning Jalan MC</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" rowspan="2" style="text-align: center;">Output (dz)</th>
                                                </tr>
                                                <tr>
                                                    <?php foreach ($jarum as $jrm): ?>
                                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $jrm['jarum']; ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $totalMcSocks = 0;
                                                $planMcSocks = 0;
                                                $totalMcGloves = 0;
                                                $planMcGloves = 0;
                                                $totalMcAll = 0;
                                                $planMcAll = 0;
                                                $totalPlanMcJrm = [];
                                                $outputDzSocks = 0;
                                                $outputDzGloves = 0;
                                                $totalOutputDz = 0;
                                                foreach ($kebutuhanMesin[$i] as $area => $jarums):
                                                    $planMcArea = 0;
                                                    $outputDz = 0;
                                                    foreach ($jarum as $jrm) {
                                                        $planMcJrm = $jarums[$jrm['jarum']] ?? 0; // Planning MC untuk jenis jarum tertentu
                                                        $planMcArea += $planMcJrm;
                                                        $dz = $planMcJrm * 14; // Menjumlahkan Planning MC per jarum untuk total area
                                                        $outputDz += $dz; // Menjumlahkan Planning MC per jarum untuk total area
                                                        // Menambahkan nilai ke total per jarum
                                                        if (!isset($totalPlanMcJrm[$jrm['jarum']])) {
                                                            $totalPlanMcJrm[$jrm['jarum']] = 0;
                                                        }
                                                        $totalPlanMcJrm[$jrm['jarum']] += $planMcJrm;
                                                    }
                                                    // Menambahkan total MC per area ke variabel total seluruh area
                                                    if ($area != 'KK8J') {
                                                        $totalMcSocks += $totalMc[$area]['Total'] ?? 0;
                                                        $planMcSocks += $planMcArea ?? 0;
                                                        $outputDzSocks += $outputDz ?? 0;
                                                    } else if ($area == 'KK8J') {
                                                        $totalMcGloves = $totalMc[$area]['Total'] ?? 0;
                                                        $planMcGloves = $planMcArea ?? 0;
                                                        $outputDzGloves += $outputDz ?? 0;
                                                    }
                                                    $totalMcAll = $totalMcSocks + $totalMcGloves;
                                                    $planMcAll = $planMcSocks + $planMcGloves;
                                                    $totalOutputDz = $outputDzSocks + $outputDzGloves;

                                                    // Jika area adalah kk8j, simpan total MC-nya

                                                ?>
                                                    <tr>
                                                        <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $area ?></td>
                                                        <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $totalMc[$area]['Total'] ?? 0; ?></td>
                                                        <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $planMcArea; ?></td>
                                                        <?php foreach ($jarum as $jrm): ?>
                                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><input type="number" class="form-control" style="text-align: center; font-size: 0.7rem; width: 65px;" value="<?= $jarums[$jrm['jarum']] ?? 0; ?>"></td>
                                                        <?php endforeach; ?>
                                                        <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= $outputDz; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total MC Sock</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $totalMcSocks; ?></th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $planMcSocks; ?></th>
                                                    <?php foreach ($jarum as $jrm): ?>
                                                        <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $totalPlanMcJrm[$jrm['jarum']] ?? 0; ?></td>
                                                    <?php endforeach; ?>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $outputDzSocks; ?></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">% Sock</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"></th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= number_format(($totalMcSocks / $planMcSocks) * 100, 2) ?>%</th>
                                                    <?php foreach ($jarum as $jrm): ?>
                                                        <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"></td>
                                                    <?php endforeach; ?>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total MC Gloves</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $totalMcGloves; ?></th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $planMcGloves; ?></th>
                                                    <?php foreach ($jarum as $jrm): ?>
                                                        <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"></td>
                                                    <?php endforeach; ?>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $outputDzGloves; ?></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $totalMcAll; ?></th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $planMcAll; ?></th>
                                                    <?php foreach ($jarum as $jrm): ?>
                                                        <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"></td>
                                                    <?php endforeach; ?>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $totalOutputDz; ?></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">% Total MC</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"></th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= number_format($totalMcAll / $planMcAll * 100, 2); ?>%</th>
                                                    <?php foreach ($jarum as $jrm): ?>
                                                        <td class="text-sm" style="text-align: center;"></td>
                                                    <?php endforeach; ?>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"></th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </form>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>