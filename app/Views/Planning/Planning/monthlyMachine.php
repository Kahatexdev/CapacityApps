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
                    <div class="row">
                        <div class="col-lg-4">
                            <h6>Global</h6>
                            <li>Total Mesin : <?= $summary['totalMc'] ?> Mesin</li>
                            <li>Total Planning : <?= $summary['totalPlanning'] ?> Mesin </li>
                            <li>Persentase : <?= $summary['totalPersen'] ?>% </li>
                            <li>Total Output : <?= $summary['OutputTotal'] ?> dz </li>

                        </div>
                        <div class="col-lg-4">
                            <h6> Socks </h6>
                            <li>Total Mesin : <?= $summary['mcSocks'] ?> Mesin</li>
                            <li>Total Planning : <?= $summary['planMcSocks'] ?> Mesin </li>
                            <li>Persentase : <?= $summary['persenSocks'] ?>% </li>
                        </div>
                        <div class="col-lg-4">
                            <h6> Gloves </h6>
                            <li>Total Mesin : <?= $summary['mcGloves'] ?> Mesin</li>
                            <li>Total Planning : <?= $summary['planMcGloves'] ?> Mesin </li>
                            <li>Persentase : <?= $summary['persenGloves'] ?>% </li>
                        </div>
                    </div>
                    <div class="row text-end">
                        <form action="<?= base_url($role . '/saveMonthlyMc') ?>" method="post">
                            <input type="text" name="judul" value="<?= $title ?>" hidden>
                            <input type="text" name="totalMc" value="<?= $summary['totalMc'] ?>" hidden>
                            <input type="text" name="totalPlanning" value="<?= $summary['totalPlanning'] ?>" hidden>
                            <input type="text" name="totalPersen" value="<?= $summary['totalPersen'] ?>" hidden>
                            <input type="text" name="OutputTotal" value="<?= $summary['OutputTotal'] ?>" hidden>

                            <input type="text" name="mcSocks" value="<?= $summary['mcSocks'] ?>" hidden>
                            <input type="text" name="planMcSocks" value="<?= $summary['planMcSocks'] ?>" hidden>
                            <input type="text" name="persenSocks" value="<?= $summary['persenSocks'] ?>" hidden>

                            <input type="text" name="mcGloves" value="<?= $summary['mcGloves'] ?>" hidden>
                            <input type="text" name="planMcGloves" value="<?= $summary['planMcGloves'] ?>" hidden>
                            <input type="text" name="persenGloves" value="<?= $summary['persenGloves'] ?>" hidden>
                            <?php foreach ($data as $tempat => $jarum): ?>
                                <input type="text" name="area[]" value="<?= $tempat ?>" hidden>

                                <input type="text" name="totalMesin[]" value="<?= $jarum['totalMesin']; ?>" hidden>
                                <input type="text" name="planningMc[]" value="<?= $jarum['planningMc']; ?>" hidden>
                                <input type="text" name="outputDz[]" value="<?= $jarum['outputDz']; ?>" hidden>
                                <?php foreach ($jarum as $jr): ?>
                                    <?php if (!is_array($jr)) continue; ?>
                                    <input type="text" name="jarum[]" value="<?= $jr['jr']  ?>" hidden>
                                    <input type="text" name="kebutuhanMesin[]" value="<?= $jr['kebutuhanMesin']  ?>" hidden>
                                <?php endforeach ?>

                            <?php endforeach ?>

                            <button type="submit" class="btn btn-info btn-block"> Save </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row" id="card-container">
        <?php foreach ($data as $area => $jarum): ?>
            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4 mt-2">
                <div class="card equal-height">
                    <div class="card-header">
                        <div class="row">
                            <h6> <?= $area ?></h6>
                        </div>
                        <div class="row text-bold">
                            <div class="col-lg-4"> Total Mesin: <?= $jarum['totalMesin']; ?></div>
                            <div class="col-lg-4"> Planning Mesin : <?= $jarum['planningMc']; ?></div>
                            <div class="col-lg-4"> Output (dz) : <?= $jarum['outputDz']; ?> dz</div>
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
                                    <?php foreach ($jarum as $jr): ?>
                                        <?php if (!is_array($jr)) continue; ?>
                                        <tr>
                                            <td><?= $jr['jr'] ?></td>
                                            <td><?= $jr['kebutuhanMesin'] ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
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
</script>
<?php $this->endSection(); ?>