<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid pu-4">

    <div class="row">
        <div class="card">
            <div class="card-header">
                <div class="row d-flex justify-content-between">
                    <div class="col-lg-6">

                        <h4>Running MC recommendation Per Month</h4>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <select class="form-control" id="planSelect">
                                <option value="">List Plan Jalan Mesin</option>
                                <?php foreach ($plan as $judul) : ?>
                                    <option value="<?= base_url(session()->get('role') . '/viewPlan/' . $judul['judul']); ?>"><?= $judul['judul']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>


                </div>
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
<script>
    document.getElementById('planSelect').onchange = function() {
        var url = this.value;
        if (url) { // Jika option memiliki value (URL)
            window.location.href = url; // Redirect ke URL yang dipilih
        }
    };
</script>

<?php $this->endSection(); ?>