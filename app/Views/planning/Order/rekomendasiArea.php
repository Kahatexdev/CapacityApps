<?php $this->extend($role . '/Order/detailModelJarum'); ?>
<?php $this->section('rekomendasi'); ?>

<?php foreach ($rekomendasi as $jarum => $value) ?>
<?= $jarum ?>

<?php $this->endSection(); ?>
