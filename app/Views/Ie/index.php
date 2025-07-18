<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Industrial Engineering</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data SMV
                                </h5>
                            </div>
                        </div>
                        <div>

                            <div>
                                <a href="<?= $role . '/updatesmv' ?>" class="btn btn-info">Import Data</a>
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



            <div class="row mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="display compact" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Model</th>
                                        <th>Size</th>
                                        <th>SMV</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>



                        </div>
                    </div>

                </div>
            </div>


            <div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-ModalDelete="ModalEdit" aria-hidden="true">
                <div class="modal-dialog  modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Data SMV</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= base_url('sudo/addaccount') ?>" method="post">
                                <div class="row">
                                    <div class="col-lg-12 col-sm-12">

                                        <div class="form-group">
                                            <label for="buyer" class="col-form-label">Model:</label>
                                            <input type="text" name="id" id="" class="form-control" hidden>
                                            <input type="text" name="model" id="" class="form-control" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="no_order" class="col-form-label">Size:</label>
                                            <input type="text" name="size" id="" class="form-control" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="no_order" class="col-form-label">SMV:</label>
                                            <input type="text" name="smvold" id="" class="form-control" hidden>
                                            <input type="text" name="smv" id="" class="form-control" required>
                                        </div>

                                    </div>

                                </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Save</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade bd-example-modal-lg" id="ModalHistory" tabindex="-1" role="dialog" aria-labelledby="ModalHistoryLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">History Data SMV</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="no_order" class="col-form-label">Size:</label>
                                        <input type="text" name="size" id="" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_order" class="col-form-label">Current SMV:</label>
                                        <input type="text" name="currentsmv" id="" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>SMV</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data from AJAX will be appended here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Save</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>

</div>

</div>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= site_url($role . '/getServerSide') ?>", // ganti sesuai route kamu
                type: "POST"
            },
            columns: [{
                    data: 'mastermodel'
                },
                {
                    data: 'size'
                },
                {
                    data: 'smv',
                    render: function(data) {
                        return data + ' second';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `
                        <button class="btn btn-success btn-sm text-xxs edit-btn" data-bs-toggle="modal" data-bs-target="#edit-btn" data-id="${data.idapsperstyle}" data-model="${data.mastermodel}" data-size="${data.size}" data-smv="${data.smv}">Edit</button>
                        <button class="btn btn-info btn-sm text-xxs history-btn" data-bs-toggle="modal" data-bs-target="#edit-btn" data-id="${data.idapsperstyle}" data-model="${data.mastermodel}" data-size="${data.size}" data-smv="${data.smv}">History</button>
                    `;
                    }
                }
            ]
        });
    });
</script>
<script>
    function hitungJumlahHari() {
        var opdString = document.getElementById("opd").value
        var shipmentString = document.getElementById("shipment").value

        var opd = new Date(opdString)
        var shipment = new Date(shipmentString)

        var timeDiff = shipment.getTime() - opd.getTime()
        var leanTime = Math.floor(timeDiff / (1000 * 60 * 60 * 24))
        var leadTime = leanTime - 7;

        if (leadTime <= 14) {
            document.getElementById("lead").value = "Invalid Lead Time"
            document.getElementById("lead").classList.add("is-invalid")
            document.getElementById("lead").classList.add("text-danger")
        } else {
            document.getElementById("lead").value = leadTime
            document.getElementById("lead").classList.remove("is-invalid")
            document.getElementById("lead").classList.remove("text-danger")
        }

    }
</script>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable();

        // Trigger import modal when import button is clicked
        $('.import-btn').click(function() {
            $('#importModal').modal('show');
        });
    });


    // Untuk tombol Edit
    $('#example').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var size = $(this).data('size');
        var model = $(this).data('model');
        var smv = $(this).data('smv');

        $('#ModalEdit').find('form').attr('action', '<?= base_url('ie/inputsmv') ?>');
        $('#ModalEdit').find('input[name="id"]').val(id);
        $('#ModalEdit').find('input[name="model"]').val(model);
        $('#ModalEdit').find('input[name="size"]').val(size);
        $('#ModalEdit').find('input[name="smv"]').val(smv);
        $('#ModalEdit').find('input[name="smvold"]').val(smv);
        $('#ModalEdit').modal('show');
    });

    // Untuk tombol History
    $('#example').on('click', '.history-btn', function() {
        var id = $(this).data('id');
        var size = $(this).data('size');
        var smv = $(this).data('smv');

        $('#ModalHistory tbody').empty();

        $.ajax({
            type: 'post',
            url: '<?= base_url('ie/gethistory') ?>',
            data: {
                size: size
            },
            success: function(response) {
                var history = JSON.parse(response);
                for (const item of history) {
                    var row = `
                    <tr>
                        <td>${item.created_at}</td>
                        <td>${item.smv_old}</td>
                    </tr>
                `;
                    $('#ModalHistory tbody').append(row);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
            }
        });

        $('#ModalHistory').find('form').attr('action', '<?= base_url('ie/inputsmv') ?>');
        $('#ModalHistory').find('input[name="id"]').val(id);
        $('#ModalHistory').find('input[name="size"]').val(size);
        $('#ModalHistory').find('input[name="currentsmv"]').val(smv);
        $('#ModalHistory').modal('show');
    });
</script>

<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>