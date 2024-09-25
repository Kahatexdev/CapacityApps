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