<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
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
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-10">
                            <div class="numbers">
                                <h4 class="font-weight-bolder mb-0">Sales Position</h4>
                            </div>
                        </div>
                        <div class="col-2">
                            <a href="<?= base_url($role . '/generatesales') ?>" class="btn btn-sm bg-gradient-success shadow text-center border-radius-md">Generate Excel</a>
                        </div>
                    </div>
                </div>

                <div class=" card-body">
                    <h5 class="font-weight-bolder mb-0">Actual Export</h5>
                    <form id="formId" action="<?= base_url($role . '/updateQtyExport') ?>" method="POST">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="row">
                                    <?php
                                    // Mendapatkan bulan dan tahun saat ini
                                    $currentMonth = date('n'); // Bulan dalam format angka (1-12)
                                    $currentYear = date('Y'); // Tahun dalam format 4 digit
                                    for ($i = 0; $i < 2; $i++) {
                                        $month = ($currentMonth + $i) % 12; // Menggunakan modulo 12 untuk menghindari overflow
                                        $year = $currentYear;
                                        // Jika bulan 0 (Januari), kita set ke 12 dan tambah tahun
                                        if ($month == 0) {
                                            $month = 12;
                                            $year += 1; // Tambah tahun jika bulan melintasi Desember
                                        }

                                        // Menyimpan nama bulan
                                        $monthName = date('F', mktime(0, 0, 0, $month, 1)); // Mendapatkan nama bulan

                                        // Format bulan dan tahun
                                        $thisMonth = $monthName . '-' . $year;
                                    ?>
                                        <div class="form-group col-6">
                                            <label for=" jarum" class="form-control-label">Qty Actual Export <?= $thisMonth ?></label>
                                            <input class="form-control" type="text" id="qty_export<?= $i ?>" name="qty_export[]">
                                            <input class="form-control" hidden type="text" id="month<?= $i ?>" name="month[]" value="<?= $thisMonth ?>">
                                        </div>
                                    <?php

                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-sm bg-gradient-info w-100">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('formId').onsubmit = function() {
        // Mendapatkan semua input qty_export
        var inputs = document.getElementsByClassName('qty_export');

        for (var i = 0; i < inputs.length; i++) {
            var input = inputs[i].value;
            // Memeriksa apakah input adalah angka dan tidak kosong
            if (isNaN(input) || input === "") {
                alert("Harap masukkan angka yang valid di semua kolom Qty Actual Export.");
                return false; // Mencegah pengiriman form
            }
        }
    };
</script>

<!-- Skrip JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?php $this->endSection(); ?>