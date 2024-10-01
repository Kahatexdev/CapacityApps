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
                            <li id="globalmc">Total Mesin : <?= $summary['totalMc'] ?> Mesin</li>
                            <li id="globalplanning">Total Planning : <?= $summary['totalPlanning'] ?> Mesin </li>
                            <li id="globalpersen">Persentase : <?= $summary['totalPersen'] ?>% </li>
                            <li id="globaloutput">Total Output : <?= $summary['OutputTotal'] ?> dz </li>

                        </div>
                        <div class="col-lg-4">
                            <h6> Socks </h6>
                            <li id="ttlmcsocks">Total Mesin : <?= $summary['mcSocks'] ?> Mesin</li>
                            <li id="ttlplanningsocks">Total Planning : <?= $summary['planMcSocks'] ?> Mesin </li>
                            <li id="ttlpersensocks">Persentase : <?= $summary['persenSocks'] ?>% </li>
                        </div>
                        <div class="col-lg-4">
                            <h6> Gloves </h6>
                            <li id="ttlmcgloves">Total Mesin : <?= $summary['mcGloves'] ?> Mesin</li>
                            <li id="ttlplanninggloves">Total Planning : <?= $summary['planMcGloves'] ?> Mesin </li>
                            <li id="ttlpersengloves">Persentase : <?= $summary['persenGloves'] ?>% </li>
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
                            <h6 id="area<?= $no ?>"> <?= $area ?></h6>
                        </div>
                        <div class="row text-bold">
                            <div class="col-lg-4" id="ttlmc<?= $no ?>"> Total Mesin: <?= $jarum['totalMesin']; ?></div>
                            <div class="col-lg-4" id="planmc<?= $no ?>"> Planning Mesin : <?= $jarum['planningMc']; ?></div>
                            <div class="col-lg-4" id="outputdz<?= $no ?>"> Output (dz) : <?= $jarum['outputDz']; ?> dz</div>
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
                                            <td id="jarum<?= $row ?>"><?= $jr['jr'] ?></td>
                                            <td id="kebmesin<?= $row ?>"><?= $jr['kebutuhanMesin'] ?></td>
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
            globalMc: document.querySelector("#globalmc") ? document.querySelector("#globalmc").textContent : null,
            globalPlan: document.querySelector("#globalplanning") ? document.querySelector("#globalplanning").textContent : null,
            globalOutput: document.querySelector("#globaloutput") ? document.querySelector("#globaloutput").textContent : null,
            ttlMcSocks: document.querySelector("#ttlmcsocks") ? document.querySelector("#ttlmcsocks").textContent : null,
            ttlPlanSocks: document.querySelector("#ttlplanningsocks") ? document.querySelector("#ttlplanningsocks").textContent : null,
            ttlPersenSocks: document.querySelector("#ttlpersensocks") ? document.querySelector("#ttlpersensocks").textContent : null,
            ttlMcGloves: document.querySelector("#ttlmcgloves") ? document.querySelector("#ttlmcgloves").textContent : null,
            ttlPlanGloves: document.querySelector("#ttlplanninggloves") ? document.querySelector("#ttlplanninggloves").textContent : null,
            ttlPersenGloves: document.querySelector("#ttlpersengloves") ? document.querySelector("#ttlpersengloves").textContent : null
        };

        // AREA
        let areaPlan = [];
        document.querySelectorAll("[id^='area']").forEach((areaElement, index) => {
            areaPlan.push({
                area: areaElement.textContent, // Ambil konten area
                ttlMc: document.querySelector(`#ttlmc${index + 1}`) ? document.querySelector(`#ttlmc${index + 1}`).textContent : null,
                planMc: document.querySelector(`#planmc${index + 1}`) ? document.querySelector(`#planmc${index + 1}`).textContent : null,
                outputDz: document.querySelector(`#outputdz${index + 1}`) ? document.querySelector(`#outputdz${index + 1}`).textContent : null
            });
        });

        // DETAIL
        let detailPlan = [];
        document.querySelectorAll("[id^='detail_area']").forEach((detailRow, index) => {
            let jarum = detailRow.querySelector("[id^='jarum']");
            let kebMesin = detailRow.querySelector("[id^='kebmesin']");
            detailPlan.push({
                area: document.querySelector(`#area${Math.floor(index / 3) + 1}`) ? document.querySelector(`#area${Math.floor(index / 3) + 1}`).textContent : null, // Mengambil area sesuai indeks
                jarum: jarum ? jarum.textContent : null,
                kebMesin: kebMesin ? kebMesin.textContent : null
            });
        });

        // Membuat wadah 'data' untuk menyimpan global, areaPlan, dan detailPlan
        let data = {
            global: global,
            area: areaPlan,
            detail: detailPlan
        };

        // Debugging untuk memastikan struktur data sudah benar
        console.log(data);

        // Kirim data menggunakan fetch
        fetch('PlanningController/savePlanning', {
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