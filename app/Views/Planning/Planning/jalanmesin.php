<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid pu-4">

    <div class="row">
        <div class="card">


            <div class="card-header">
                <h4>Running MC recomendation PerMonth</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <?php foreach ($bulan as $bl) : ?>
            <div class="col-lg-3 my-3">
                <a href="<?= base_url($role . '/jalanmesin/' . str_replace(' ', '-', $bl)) ?>">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between">
                            <h5>
                                <?= $bl ?>
                            </h5>
                            <h2>

                                <i class="ni ni-calendar-grid-58"></i>
                            </h2>
                        </div>

                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>


<?php $this->endSection(); ?>