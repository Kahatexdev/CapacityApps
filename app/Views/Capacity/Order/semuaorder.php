<?php $this->extend('Capacity/layout'); ?>
<?php $this->section('content'); ?>
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
                                    Data Semua Order
                                </h5>
                            </div>
                        </div>
                        <div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                <table id="example" class="display compact" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Created At</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Buyer</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Order</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Product Type</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Desc</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Seam</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Leadtime</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Order</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Order</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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

    <!-- modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Input Aps Report</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body align-items-center">
                    <div class="row align-items-center">
                        <div id="drop-area" class="border rounded d-flex justify-content-center align-item-center mx-3" style="height:200px; width: 95%; cursor:pointer;">
                            <div class="text-center mt-5">
                                <i class="ni ni-cloud-upload-96" style="font-size: 48px;">

                                </i>
                                <p class="mt-3" style="font-size: 28px;">
                                    Upload file here
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-9 pl-0">

                            <form action="<?= base_url('capacity/importModel') ?>" id="modalForm" method="POST" enctype="multipart/form-data">
                                <input type="text" class="form-control" name="id_model" hidden>
                                <input type="text" class="form-control" name="no_model" hidden>
                                <input type="file" id="fileInput" name="excel_file" multiple accept=".xls , .xlsx" class="form-control ">
                        </div>
                        <div class="col-3 pl-0">
                            <form>
                                <!-- Other form inputs go here -->
                                <button type="submit" class="btn btn-info btn-block" onclick="this.disabled=true; this.form.submit();">Simpan</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Trigger import modal when import button is clicked
            $('.import-btn').click(function() {
                console.log("a");
                var idModel = $(this).data('id');
                var noModel = $(this).data('no-model');

                $('#importModal').find('input[name="id_model"]').val(idModel);
                $('#importModal').find('input[name="no_model"]').val(noModel);

                $('#importModal').modal('show'); // Show the modal
            });
            
            $('#example').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '<?= base_url('capacity/tampilPerdelivery') ?>',
                    type: 'POST'
                },
                "columns": [
                    { 
                        "data": "created_at",
                        "render": function(data, type, row) {
                            if (data) {
                                // Parse the date and format it as d-M-Y
                                var date = new Date(data);
                                var day = ('0' + date.getDate()).slice(-2);
                                var month = date.toLocaleString('default', { month: 'short' });
                                var year = date.getFullYear();
                                return `${day}-${month}-${year}`;
                            }
                            return data;
                        }
                    },
                    { "data": "kd_buyer_order" },
                    { "data": "no_model" },
                    { "data": "no_order" },
                    { "data": "machinetypeid" },
                    { "data": "product_type" },
                    { "data": "description" },
                    { "data": "seam" },
                    { "data": "leadtime" },
                    { 
                        "data": "qty",
                        "render": function(data, type, row) {
                            if (data) {
                                return parseFloat(data).toLocaleString('en-US', { minimumFractionDigits: 0 });
                            }
                            return data;
                        }
                    },
                    { 
                        "data": "sisa",
                        "render": function(data, type, row) {
                            if (data) {
                                return parseFloat(data).toLocaleString('en-US', { minimumFractionDigits: 0 });
                            }
                            return data;
                        }
                    },
                    { 
                        "data": "delivery",
                        "render": function(data, type, row) {
                            if (data) {
                                // Parse the date and format it as d-M-Y
                                var date = new Date(data);
                                var day = ('0' + date.getDate()).slice(-2);
                                var month = date.toLocaleString('default', { month: 'short' });
                                var year = date.getFullYear();
                                return `${day}-${month}-${year}`;
                            }
                            return data;
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            if (row.qty === null) {
                                return `<button type="button" class="btn import-btn btn-success text-xs" data-toggle="modal" data-target="#importModal" data-id="${row.id_model}" data-no-model="${row.no_model}">
                                            Import
                                        </button>`;
                            } else {
                                return `<a href="<?= base_url('capacity/detailmodel') ?>/${row.no_model}/${row.delivery}">
                                            <button type="button" class="btn btn-info btn-sm details-btn">Details</button>
                                        </a>`;
                            }
                        }
                    }
                ],
                "order": []
            });
        });
    </script>
    <?php $this->endSection(); ?>