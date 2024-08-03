<?php $this->extend($role . '/Mesin/detailMesinArea'); ?>
<?php $this->section('bstabel'); ?>

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
    <div class="col">
        <div class="card">
            <div class="card-header">

            </div>
            <div class="card-body">

            </div>
        </div>
    </div>
</div>


<?php $this->endSection(); ?>