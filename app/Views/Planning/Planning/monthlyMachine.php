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
                            <li id="globalttlmc">Total Mesin : <?= $summary['totalMc'] ?> Mesin</li>
                            <li id="globalttlplanning">Total Planning : <?= $summary['totalPlanning'] ?> Mesin </li>
                            <li id="globalttlpersen">Persentase : <?= $summary['totalPersen'] ?>% </li>
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
                        <button class="btn btn-info" onclick="saveAll()">SAVE</button>
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
        // Buat array untuk menampung semua data
        let ids = [];

        // global
        let globalmcElements = document.querySelectorAll("[id^='globalttlmc']");
        let globalplanElements = document.querySelectorAll("[id^='globalttlplanning']");
        let globalpersenElements = document.querySelectorAll("[id^='globalttlpersen']");
        let globaloutputElements = document.querySelectorAll("[id^='globaloutput']");

        let ttlmcsocksElements = document.querySelectorAll("[id^='ttlmcsocks']");
        let ttlplansocksElements = document.querySelectorAll("[id^='ttlplanningsocks']");
        let ttlpersensocksElements = document.querySelectorAll("[id^='ttlpersensocks']");

        let ttlmcglovesElements = document.querySelectorAll("[id^='ttlmcgloves']");
        let ttlplanglovesElements = document.querySelectorAll("[id^='ttlplanninggloves']");
        let ttlpersenglovesElements = document.querySelectorAll("[id^='ttlpersengloves']");

        // area
        let ttlmcElements = document.querySelectorAll("[id^='ttlmc']");
        let planmcElements = document.querySelectorAll("[id^='planmc']");
        let outputdzElements = document.querySelectorAll("[id^='outputdz']");

        //detail
        let jarumElements = document.querySelectorAll("[id^='jarum']");
        let kebmesinElements = document.querySelectorAll("[id^='kebmesin']");

        // Loop untuk menyimpan data dari setiap elemen ke array
        globalmcElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        globalplanElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        globalpersenElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        globaloutputElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        ttlmcsocksElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        ttlplanglovesElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        ttlpersensocksElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        ttlmcglovesElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        ttlplanglovesElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        ttlpersenglovesElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });


        ttlmcElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        planmcElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        outputdzElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        jarumElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });
        kebmesinElements.forEach(function(element) {
            ids.push({
                id: element.id,
                value: element.innerText
            });
        });

        // Optional: Convert to JSON if needed for form submission or AJAX
        console.log(ids); // Debugging untuk memastikan data sudah benar

        // Kirim data dengan AJAX atau masukkan ke dalam input tersembunyi dalam form
        let form = document.querySelector('form'); // Ambil form yang sudah ada
        let hiddenInput = document.createElement('input'); // Buat input hidden baru
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'ids'; // Nama parameter yang akan dikirim
        hiddenInput.value = JSON.stringify(ids); // Data ID yang akan dikirim
        form.appendChild(hiddenInput); // Tambahkan input hidden ke dalam form

        // Submit form
        form.submit();
    }
</script>
<?php $this->endSection(); ?>