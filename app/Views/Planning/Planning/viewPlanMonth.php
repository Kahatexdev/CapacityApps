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
                                    Planning Jalan Mesin <?= $title ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-8 text-end">
                            <form action="<?= base_url($role . '/exportPlanningJlMc/' . $title) ?>" method="post" ?>
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
    <div class="row" id="card-container">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">

                <div class="card-body">
                    <div class="row" id="global">
                        <div class="col-lg-4">
                            <h6>Global</h6>
                            <input type="text" id="judulPlan" value="<?= $title ?>" hidden>
                            <li>Total Mesin : <?= $summary['total_mc'] ?> Mesin
                            </li>
                            <li>Total Planning : <?= $summary['planning_mc'] ?> Mesin
                            </li>
                            <li>Persentase : <?= round(($summary['planning_mc'] / $summary['total_mc']) * 100) ?>%
                            </li>
                            <li>Total Output : <?= $summary['total_output'] ?> dz
                            </li>

                        </div>
                        <div class="col-lg-4">
                            <h6> Socks </h6>
                            <li>Total Mesin : <?= $summary['mc_socks'] ?> Mesin
                            </li>
                            <li>Total Planning : <?= $summary['plan_mc_socks'] ?> Mesin
                            </li>
                            <li>Persentase : <?= round(($summary['plan_mc_socks'] / $summary['mc_socks']) * 100) ?>%
                            </li>
                        </div>
                        <div class="col-lg-4">
                            <h6> Gloves </h6>
                            <li>Total Mesin : <?= $summary['mc_gloves'] ?> Mesin
                            </li>
                            <li>Total Planning : <?= $summary['plan_mc_gloves'] ?> Mesin
                            </li>
                            <li>Persentase : <?= round(($summary['plan_mc_gloves'] / $summary['mc_gloves']) * 100) ?>%
                            </li>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row" id="card-container">
        <?php foreach ($data as $area => $detailArea): ?>
            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4 mt-2">
                <div class="card equal-height">
                    <div class="card-header" id="area_mc">
                        <div class="row">
                            <h6><?= $area ?></h6>
                        </div>
                        <div class="row text-bold">
                            <div class="col-lg-4"> Total Mesin: <?= $detailArea['totalMesin']; ?> </div>
                            <div class="col-lg-4"> Planning Mesin: <?= $detailArea['planningMc']; ?> </div>
                            <div class="col-lg-4"> Output (dz): <?= $detailArea['outputDz']; ?> dz </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table">
                            <table class="table">
                                <thead class="bg-dark">
                                    <tr>
                                        <th class="text-white"> Jarum </th>
                                        <th class="text-white"> Plan Mesin </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detailArea['jarum'] as $jarumDetail): ?>
                                        <tr id="detail_area">
                                            <td><?= $jarumDetail['jarum'] ?></td>
                                            <td><?= $jarumDetail['planning_mc'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-dark text-sm">
                                <tr class="text-center">
                                    <th class="text-white" rowspan="2">STATUS ORDER</th>
                                    <?php foreach ($statusOrder as $month => $data): ?>
                                        <?php if (is_array($data)): ?>
                                            <th class="text-white" colspan="2"><?= $month ?></th>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <th class="text-white" colspan="2">TOTAL</th>
                                </tr>
                                <tr>
                                    <?php foreach ($statusOrder as $month => $data): ?>
                                        <?php if (is_array($data)): ?>
                                            <th class="text-white text-sm">QTY ORDER</th>
                                            <th class="text-white text-sm">SISA ORDER</th>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <th class="text-white text-sm">QTY ORDER</th>
                                    <th class="text-white text-sm">SISA ORDER</th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-sm">
                                <tr>
                                    <td>KAOSKAKI</td>
                                    <?php foreach ($statusOrder as $month => $data): ?>
                                        <?php if (is_array($data)): ?>
                                            <td><?= number_format($data['socks']['qty'],) ?></td>
                                            <td><?= number_format($data['socks']['sisa'],) ?></td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <td><?= number_format($statusOrder['totalOrderSocks'],) ?></td>
                                    <td><?= number_format($statusOrder['totalSisaSocks'],) ?></td>
                                </tr>

                                <!-- SARUNG TANGAN row -->
                                <tr>
                                    <td>SARUNG TANGAN</td>
                                    <?php foreach ($statusOrder as $month => $data): ?>
                                        <?php if (is_array($data)): ?>
                                            <td><?= number_format($data['gloves']['qty'],) ?></td>
                                            <td><?= number_format($data['gloves']['sisa'],) ?></td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <td><?= number_format($statusOrder['totalOrderGloves'],) ?></td>
                                    <td><?= number_format($statusOrder['totalSisaGloves'],) ?></td>
                                </tr>

                                <!-- TOTAL row -->
                                <tr class="text-bold">
                                    <td>TOTAL</td>
                                    <?php foreach ($statusOrder as $month => $data): ?>
                                        <?php if (is_array($data)): ?>
                                            <td><?= number_format($data['qty'],) ?></td>
                                            <td><?= number_format($data['sisa'],) ?></td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <td><?= number_format($statusOrder['grandTotalOrder'],) ?></td>
                                    <td><?= number_format($statusOrder['grandTotalSisa'],) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let cards = document.querySelectorAll('.equal-height');
        let maxHeight = 0;

        // Loop untuk cek tinggi tertinggi
        cards.forEach(function(card) {
            let cardHeight = card.offsetHeight;
            if (cardHeight > maxHeight) {
                maxHeight = cardHeight;
            }
        });

        // Set tinggi semua card ke maxHeight
        cards.forEach(function(card) {
            card.style.height = maxHeight + 'px';
        });
    });

    function saveAll() {
        // GLOBAL
        let global = {
            judulPlan: document.querySelector("#judulPlan") ? document.querySelector("#judulPlan").value : null,
            globalMc: document.querySelector("#globalmc") ? document.querySelector("#globalmc").value : null,
            globalPlan: document.querySelector("#globalplanning") ? document.querySelector("#globalplanning").value : null,
            globalOutput: document.querySelector("#globaloutput") ? document.querySelector("#globaloutput").value : null,
            ttlMcSocks: document.querySelector("#ttlmcsocks") ? document.querySelector("#ttlmcsocks").value : null,
            ttlPlanSocks: document.querySelector("#ttlplanningsocks") ? document.querySelector("#ttlplanningsocks").value : null,
            ttlPersenSocks: document.querySelector("#ttlpersensocks") ? document.querySelector("#ttlpersensocks").value : null,
            ttlMcGloves: document.querySelector("#ttlmcgloves") ? document.querySelector("#ttlmcgloves").value : null,
            ttlPlanGloves: document.querySelector("#ttlplanninggloves") ? document.querySelector("#ttlplanninggloves").value : null,
            ttlPersenGloves: document.querySelector("#ttlpersengloves") ? document.querySelector("#ttlpersengloves").value : null
        };

        // AREA
        let areaPlan = [];
        document.querySelectorAll("[id^='area']").forEach((areaElement, index) => {
            let areaEl = document.querySelector(`#area${index + 1}`)
            let ttlMc = document.querySelector(`#ttlmc${index + 1}`);
            let planMc = document.querySelector(`#planmc${index + 1}`);
            let outputDz = document.querySelector(`#outputdz${index + 1}`);

            // Check if elements exist before accessing textContent
            areaEl = areaEl ? areaEl.value : null;
            ttlMc = ttlMc ? ttlMc.value : null;
            planMc = planMc ? planMc.value : null;
            outputDz = outputDz ? outputDz.value : null;

            // Add to areaPlan only if ttlMc, planMc, and outputDz are not null
            if (ttlMc && planMc && outputDz) {
                areaPlan.push({
                    area: areaEl, // Trim to clean up unwanted spaces
                    ttlMc: ttlMc,
                    planMc: planMc,
                    outputDz: outputDz
                });
            }
        });

        // DETAIL
        let detailPlan = [];
        document.querySelectorAll("[id^='detail_area']").forEach((detailRow, index) => {
            let jarum = detailRow.querySelector("[id^='jarum']");
            let kebMesin = detailRow.querySelector("[id^='kebmesin']");
            let areaDetail = detailRow.querySelector("[id^='areaDetail']");

            // Check if elements exist before accessing textContent
            jarum = jarum ? jarum.value : null;
            kebMesin = kebMesin ? kebMesin.value : null;
            areaDetail = areaDetail ? areaDetail.value : null;

            // Add to detailPlan only if jarum and kebMesin are not null
            if (jarum && kebMesin) {
                detailPlan.push({
                    jarum: jarum,
                    kebMesin: kebMesin,
                    areaDetail: areaDetail
                });
            }
        });

        // Create final data object
        let data = {
            global: global,
            area: areaPlan,
            detail: detailPlan
        };

        // Debugging to check data structure
        console.log(data);

        // Send data using fetch
        fetch('http://localhost:8080/planning/saveMonthlyMc', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                console.log('Sukses:', result);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
</script>
<?php $this->endSection(); ?>