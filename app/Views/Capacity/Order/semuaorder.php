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
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalMessage" class="btn btn-success bg-gradient-info shadow text-center border-radius-md">
                                Input Data Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade  bd-example-modal-lg" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
                <div class="modal-dialog  modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Input Data Order</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= base_url('capacity/inputOrder') ?>" method="post">
                                <div class="form-group">
                                    <label for="tgl-bk-form-label">Tanggal Turun Order</label>
                                    <input type="date" class="form-control" name="tgl_turun">
                                </div>
                                <div class="form-group">
                                    <label for="No Model" class="col-form-label">No Model</label>
                                    <input type="text" name="no_model" class="form-control">
                                </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row mt-3">
    <div class="card">
        <div class="table-responsive">
            <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Turun PDK</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Buyer</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Product Type</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Desc</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Seam</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Leadtime</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Shipment</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Order</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Order</th>
                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tampildata as $order) : ?>
                        <tr>
                            <td><?= $order->created_at; ?></td>
                            <td><?= $order->kd_buyer_order; ?></td>
                            <td><?= $order->no_model; ?></td>
                            <td><?= $order->product_type; ?></td>
                            <td><?= $order->description; ?></td>
                            <td><?= $order->seam; ?></td>
                            <td><?= $order->leadtime; ?></td>
                            <td><?= $order->delivery; ?></td>
                            <td><?= $order->qty; ?></td>
                            <td><?= $order->sisa; ?></td>
                            <td>
                                <?php if ($order->qty === null) : ?>
                                    <!-- If qty is null, set action to Import -->
                                    <button type="button" class="btn btn-success btn-sm import-btn" data-toggle="modal" data-target="#importModal" 
                                        data-id="<?= $order->id_model; ?>"
                                        data-no-model="<?= $order->no_model; ?>"
                                    >
                                        Import
                                    </button>
                                <?php else : ?>
                                    <!-- If qty is not null, set action to Details -->
                                    <a href="<?= base_url('capacity/detailmodel/' . $order->no_model. '/' .$order->delivery); ?>"
                                    <button type="button" class="btn btn-info btn-sm details-btn">
                                        Details
                                    </button>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Data Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="importModalLabel">Import Data</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="<?= base_url('capacity/importModel') ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>ID Model</label>
                    <input type="text" class="form-control" name="id_model" value="<?=  $order->id_model; ?>">
                </div>
                <div class="form-group">
                    <label>No Model</label>
                    <input type="text" class="form-control" name="no_model" value="<?= $order->no_model; ?>">
                </div>
                <div class="">File Browser</div>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="fileexcel" id="file" required accept=".xls, .xlsx">
                    <label class="custom-file-label" for="file">Pilih APS Order Report</label>
                </div> 
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info" id="importDataBtn">Import</button>
               </div>
            </form> 
        </div>
    </div>
</div>



<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
     $(document).ready(function() {
        $('#dataTable').DataTable();

        // Trigger import modal when import button is clicked
        $('.import-btn').click(function() {
            var idModel = $(this).data('id');
            var noModel = $(this).data('no-model');
            
            $('#importModal').find('input[name="id_model"]').val(idModel);
            $('#importModal').find('input[name="no_model"]').val(noModel);
            
            $('#importModal').modal('show'); // Show the modal
        });
    });
</script>
<?php $this->endSection(); ?>
