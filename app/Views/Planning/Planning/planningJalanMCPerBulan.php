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
                            <form action="<?= base_url($role . '/exportPlanningJlMc/' . $bulan) ?>" method="post" ?>
                                <!-- <a href="" class="btn btn-save bg-gradient-info d-inline-flex align-items-center">
                                    Save</a> -->
                                <button type="submit" class="btn btn-info"><i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Report Excel</button>

                                <a href="<?= base_url($role . '/jalanmesin') ?>" class="btn bg-gradient-dark">
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
            <form action="">
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
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" colspan="<?= count($jarum); ?>" style="text-align: center;">Rincian Planning Jalan MC</th>
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
                                                $row = 1;
                                                foreach ($kebutuhanMesin[$i] as $area => $jarums):
                                                    $planMcArea = 0;
                                                    $outputDz = 0;
                                                    foreach ($jarum as $jrm) {
                                                        $planMcJrm = $jarums[$jrm['jarum']] ?? 0; // Planning MC untuk jenis jarum tertentu
                                                        $planMcArea += $planMcJrm;
                                                        // Menambahkan nilai ke total per jarum
                                                        if (!isset($totalPlanMcJrm[$jrm['jarum']])) {
                                                            $totalPlanMcJrm[$jrm['jarum']] = 0;
                                                        }
                                                        $totalPlanMcJrm[$jrm['jarum']] += $planMcJrm;
                                                    }
                                                    // Ambil nilai outputDz dari $output berdasarkan $i dan $area
                                                    $outputDz = isset($output[$i][$area]) ? $output[$i][$area] : 0;

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
                                                        <td class="text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;" id="totalPlanArea<?= $row; ?>"><?= $planMcArea; ?></td>
                                                        <?php
                                                        $no = 1; // Deklarasi variabel sebelum loop
                                                        foreach ($jarum as $jrm): ?>
                                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">
                                                                <input type="number" class="form-control plan_mc<?= $row; ?>" id="plan_mc<?= $no; ?>"
                                                                    style="text-align: center; font-size: 0.7rem; width: 65px;"
                                                                    value="<?= $jarums[$jrm['jarum']] ?? 0; ?>"
                                                                    data-jarum="<?= $jrm['jarum']; ?>" onchange="updateTotals(<?= $row; ?>)">
                                                            </td>
                                                        <?php
                                                            $no++; // Increment variabel $no
                                                        endforeach; ?>
                                                        <td class=" text-uppercase text-dark text-xxs font-weight opacity-7 ps-2" style="text-align: center;"><?= number_format($outputDz, 0); ?>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    $row++;
                                                endforeach; ?>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total MC Sock</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $totalMcSocks; ?></th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $planMcSocks; ?></th>
                                                    <?php
                                                    $no = 1;
                                                    foreach ($jarum as $jrm): ?>
                                                        <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" id="totalPlan<?= $no ?>"><?= $totalPlanMcJrm[$jrm['jarum']] ?? 0; ?></td>
                                                    <?php
                                                        $no++;
                                                    endforeach; ?>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= number_format($outputDzSocks, 0); ?></th>
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
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= number_format($outputDzGloves, 0); ?></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $totalMcAll; ?></th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= $planMcAll; ?></th>
                                                    <?php foreach ($jarum as $jrm): ?>
                                                        <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"></td>
                                                    <?php endforeach; ?>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;"><?= number_format($totalOutputDz, 0); ?></th>
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
<canvas id="myCanvas" width="200" height="100"></canvas>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvas = document.getElementById('myCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            // Gambar sesuatu di canvas jika perlu
            ctx.fillStyle = 'blue';
            ctx.fillRect(0, 0, 200, 100);
        } else {
            console.error("Canvas not found");
        }
    });

    function updateTotals(row) {
        // Menghitung total untuk area (baris)
        let totalArea = 0;
        const areaInputs = document.querySelectorAll('#plan_mc' + row); // Gunakan id

        areaInputs.forEach(input => {
            const value = parseInt(input.value) || 0; // Ambil nilai input atau 0 jika kosong
            totalArea += value; // Tambahkan ke total area
        });

        // Tampilkan total area di elemen yang sesuai
        document.getElementById('totalPlanArea' + row).textContent = totalArea;

        // Menghitung total untuk setiap jenis jarum (kolom)
        const jarumCount = <?= count($jarum) ?>; // Ganti dengan jumlah jenis jarum yang sesuai
        for (let no = 1; no <= jarumCount; no++) {
            let totalPlan = 0;
            const columnInputs = document.querySelectorAll('.plan_mc' + no); // Gunakan class

            columnInputs.forEach(input => {
                const value = parseInt(input.value) || 0; // Ambil nilai input atau 0 jika kosong
                totalPlan += value; // Tambahkan ke total plan
            });

            // Tampilkan total plan di elemen yang sesuai
            document.getElementById('totalPlan' + no).textContent = totalPlan;
        }
    }
</script>
<?php $this->endSection(); ?>