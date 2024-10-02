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
                            <form action="<?= base_url($role . '/exportPlanningJlMc/' . $bulan) ?>" method="post" ?>
                                <!-- <a href="" class="btn btn-save bg-gradient-info d-inline-flex align-items-center">
                                    Save</a> -->


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
                            <li>Total Mesin : <?= $summary['totalMc'] ?> Mesin
                                <input type="number" id="globalmc" value="<?= $summary['totalMc'] ?>" hidden>

                            </li>
                            <li>Total Planning : <?= $summary['totalPlanning'] ?> Mesin
                                <input type="number" id="globalplanning" value="<?= $summary['totalPlanning'] ?>" hidden>

                            </li>
                            <li>Persentase : <?= $summary['totalPersen'] ?>%
                                <input type="number" id="globalpersen" value="<?= $summary['totalPersen'] ?>" hidden>

                            </li>
                            <li>Total Output : <?= $summary['OutputTotal'] ?> dz
                                <input type="number" id="globaloutput" value="<?= $summary['OutputTotal'] ?>" hidden>

                            </li>

                        </div>
                        <div class="col-lg-4">
                            <h6> Socks </h6>
                            <li>Total Mesin : <?= $summary['mcSocks'] ?> Mesin
                                <input type="number" id="ttlmcsocks" value="<?= $summary['mcSocks'] ?>" hidden>

                            </li>
                            <li>Total Planning : <?= $summary['planMcSocks'] ?> Mesin
                                <input type="number" id="ttlplanningsocks" value="<?= $summary['planMcSocks'] ?>" hidden>

                            </li>
                            <li>Persentase : <?= $summary['persenSocks'] ?>%
                                <input type="number" id="ttlpersensocks" value="<?= $summary['persenSocks'] ?>" hidden>

                            </li>
                        </div>
                        <div class="col-lg-4">
                            <h6> Gloves </h6>
                            <li>Total Mesin : <?= $summary['mcGloves'] ?> Mesin
                                <input type="number" id="ttlmcgloves" value="<?= $summary['mcGloves'] ?>" hidden>
                            </li>
                            <li>Total Planning : <?= $summary['planMcGloves'] ?> Mesin
                                <input type="number" id="ttlplanninggloves" value="<?= $summary['planMcGloves'] ?>" hidden>

                            </li>
                            <li>Persentase : <?= $summary['persenGloves'] ?>%
                                <input type="number" id="ttlpersengloves" value="<?= $summary['persenGloves'] ?>" hidden>

                            </li>
                        </div>
                    </div>
                    <div class="row text-end">
                        <button class="btn btn-info save" onclick="saveAll()">SAVE</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row" id="card-container">
        <?php
        $no = 1;
        foreach ($data as $area => $jarum): ?>
            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4 mt-2">
                <div class="card equal-height">
                    <div class="card-header" id="area_mc">
                        <div class="row">
                            <h6> <?= $area ?></h6>
                            <input type="text" id="area<?= $no ?>" value="<?= $area ?>" hidden>
                        </div>
                        <div class="row text-bold">
                            <div class="col-lg-4"> Total Mesin: <?= $jarum['totalMesin']; ?>
                                <input type="text" id="ttlmc<?= $no ?>" value="<?= $jarum['totalMesin']; ?>" hidden>
                            </div>
                            <div class="col-lg-4"> Planning Mesin : <?= $jarum['planningMc']; ?>
                                <input type="text" id="planmc<?= $no ?>" value="<?= $jarum['planningMc']; ?>" hidden>

                            </div>
                            <div class="col-lg-4"> Output (dz) : <?= $jarum['outputDz']; ?> dz
                                <input type="text" id="outputdz<?= $no ?>" value=" <?= $jarum['outputDz']; ?> " hidden>

                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table">
                            <table class="table">
                                <thead class="bg-dark">
                                    <th class=" text-white"> Jarum </th>
                                    <th class=" text-white"> Plan Mesin </th>
                                </thead>
                                <tbody>
                                    <?php
                                    $row = 1;
                                    foreach ($jarum as $jr): ?>
                                        <?php if (!is_array($jr)) continue; ?>
                                        <tr id="detail_area">
                                            <td><?= $jr['jr'] ?>
                                                <input type="text" id="jarum<?= $row ?>" value="<?= $jr['jr'] ?>" hidden>

                                            </td>
                                            <td><?= $jr['kebutuhanMesin'] ?>
                                                <input type="text" id="kebmesin<?= $row ?>" value="<?= $jr['kebutuhanMesin'] ?>" hidden>

                                                <input type="text" id="areaDetail" value="<?= $area ?>" hidden>
                                            </td>
                                        </tr>
                                    <?php
                                        $row++;
                                    endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            $no++;
        endforeach; ?>
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

        // Debugging global
        console.log("Global Data:", global);

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

        // Debugging areaPlan
        console.log("Area Plan:", areaPlan);

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

                if (result.status === 'success') {
                    // Tampilkan SweetAlert untuk pesan sukses
                    Swal.fire({
                        title: 'Sukses!',
                        text: result.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Redirect setelah SweetAlert ditutup
                        window.location.href = 'http://localhost:8080/planning/viewPlan/' + document.querySelector("#judulPlan").value;
                    });
                } else {
                    // Tampilkan SweetAlert untuk pesan error
                    Swal.fire({
                        title: 'Gagal!',
                        text: result.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Tampilkan SweetAlert jika ada error dalam proses fetch
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyimpan data!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });

    }
</script>
<?php $this->endSection(); ?>