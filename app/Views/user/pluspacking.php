<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>

<div class="card">
    <div class="card-header">
        <div class="row d-flex justify-content-between">
            <div class="col-lg-6">
                <h4 class="header">

                    Input Plus Packing
                </h4>
            </div>
            <div class="col-lg-3">
                <form id="form-plus-packing" method="GET">
                    <div class="form-group">
                        <label for="" class="col-form-label">No Model:</label>
                        <input type="text" name="pdk" id="pdk" class="form-control" placeholder="input No Model">
                        <button type="button" class="btn btn-info form-control" onclick="submitForm()">
                            Submit
                        </button>
                    </div>

                </form>
            </div>
        </div>
        <div class="card-body">
            <?= $this->renderSection('pluspacking'); ?>

        </div>
    </div>
</div>
<script>
    function submitForm() {
        let pdk = document.getElementById('pdk').value;

        if (pdk) {
            // Mengarahkan ke URL yang baru dengan pdk sebagai bagian dari path
            window.location.href = "<?= base_url($role . '/viewModelPlusPacking') ?>" + "/" + encodeURIComponent(pdk);
        } else {
            alert('No Model harus diisi!');
        }
    }

    // Event listener untuk mencegah form submit saat menekan Enter
    document.getElementById('pdk').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Mencegah default behavior, yaitu submit form
        }
    });
</script>
<?php $this->endSection(); ?>