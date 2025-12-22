<?php $this->extend('Sudo/layout'); ?>
<?php $this->section('content'); ?>
<style>
    .upload-area:hover {
        background-color: #eef5ff;
        border-color: #0d6efd !important;
        transition: 0.2s;
    }
</style>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Overall Equipment Effectiveness
                                </h5>
                            </div>
                        </div>
                        <div>

                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalImport">
                                <i class="fas fa-file-import me-1"></i> Import Downtime
                            </button>
                            <a href="<?= base_url($role . '/oee/download-template') ?>" class="btn btn-success">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>

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


        </div>

    </div>
    <div class="modal fade" id="modalImport" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Upload File Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="" enctype="multipart/form-data" action="<?= base_url($role . '/oee/importdowntime') ?>" method="post">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="excelFile" class="form-label fw-semibold">
                                <i class="fas fa-file-excel me-1 text-success"></i>
                                Pilih File Excel
                            </label>

                            <!-- Dropzone Style -->
                            <div class="p-4 border border-2 rounded-3 text-center upload-area bg-light"
                                style="cursor: pointer;"
                                onclick="document.getElementById('excelFile').click()">

                                <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-dark"></i>

                                <p class="mb-1 fw-semibold">Klik untuk memilih file</p>
                                <p class="text-muted small mb-0">Format yang didukung: .xlsx, .xls, .csv</p>
                            </div>

                            <!-- Hidden File Input -->
                            <input type="file" class="form-control" id="excelFile" name="file"
                                accept=".xlsx, .xls, .csv" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>

                        <button type="submit" class="btn btn-dark" id="btnImport">
                            <i class="fas fa-upload me-1"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="display compact " style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Username</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Role</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Area</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Assign</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Action</th>


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


<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
<script type="text/javascript">
    $("#formImportOrder").on("submit", function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "<?= base_url($role . '/oee/importdowntime') ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",

            beforeSend: function() {
                $("#btnImport")
                    .prop("disabled", true)
                    .html(`<span class="spinner-border spinner-border-sm"></span> Importing...`);
            },

            success: function(res) {
                $("#btnImport").prop("disabled", false).html("Import");

                if (res.status === true) {
                    Swal.fire({
                        icon: "success",
                        title: "Import berhasil!",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $("#modalImport").modal("hide");
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: "Gagal Import",
                        text: res.message ?? "Import gagal"
                    });
                }
            },

            error: function(xhr) {
                $("#btnImport").prop("disabled", false).html("Import");

                let msg = "Terjadi kesalahan server.";

                if (xhr.responseJSON) {
                    msg = xhr.responseJSON.message ??
                        xhr.responseJSON.error ??
                        msg;
                }

                console.error(xhr);

                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: msg
                });
            }
        });
    });
</script>

<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>