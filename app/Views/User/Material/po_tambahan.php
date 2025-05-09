<?php $this->extend('User/layout'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                    <div class="d-flex justify-content-between">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                List PO Tambahan <?= $area ?>
                            </h5>
                        </div>
                        <div>
                            <button class="btn btn-info">
                                <a href="<?= base_url($role . '/form-potambahan/' . $area) ?>" class="fa fa-list text-white" style="text-decoration: none;"> List</a>
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="display compact " style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal PO(+)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Item Type</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode Warna</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Color</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty PO(+)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src=" <?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    // SweetAlert untuk menampilkan pesan sukses/gagal
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil flashdata dari server dan pastikan nilainya berupa string
        const successMessage = "<?= htmlspecialchars(is_string(session()->getFlashdata('success')) ? session()->getFlashdata('success') : '') ?>";
        const errorMessage = "<?= htmlspecialchars(is_string(session()->getFlashdata('error')) ? session()->getFlashdata('error') : '') ?>";

        // Tampilkan SweetAlert jika ada pesan sukses
        if (successMessage && successMessage.trim() !== "") {
            Swal.fire({
                title: "Berhasil!",
                text: successMessage,
                icon: "success",
                confirmButtonText: "OK"
            });
        }

        // Tampilkan SweetAlert jika ada pesan gagal
        if (errorMessage && errorMessage.trim() !== "") {
            Swal.fire({
                title: "Gagal!",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "OK"
            });
        }
    });
</script>
<script type="text/javascript">
</script>
<?php $this->endSection(); ?>