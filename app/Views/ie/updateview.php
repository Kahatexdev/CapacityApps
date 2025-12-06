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

    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card pb-0">
                    <div class="card-header d-flex justify-content-between">
                        <h5>
                            Import Data Produksi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">

                                <div id="drop-area" class="border rounded d-flex justify-content-center align-item-center mx-3" style="height:200px; width: 100%; cursor:pointer;">
                                    <div class="text-center mt-5">
                                        <i class="fas fa-upload" style="font-size: 48px;"></i>

                                        <p class=" mt-3" style="font-size: 28px;">
                                            Upload file here
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12 pl-0">

                                <form action="<?= base_url($role . '/importupdate') ?>" method="post" enctype="multipart/form-data">
                                    <input type="file" id="fileInput" name="excel_file" multiple accept=".xls , .xlsx" class="form-control mx-3">
                                    <button type="submit" class="btn btn-info btn-block w-100 mx-3"> Simpan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({});
            $('#dataTable0').DataTable({
                "order": [
                    [0, "desc"]
                ]
            });

            // Trigger import modal when import button is clicked
            $('.import-btn').click(function() {
                console.log("a");
                var idModel = $(this).data('id');
                var noModel = $(this).data('no-model');

                $('#importModal').find('input[name="id_model"]').val(idModel);
                $('#importModal').find('input[name="no_model"]').val(noModel);

                $('#importModal').modal('show'); // Show the modal
            });
        });
    </script>
    <?php $this->endSection(); ?>