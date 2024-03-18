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
                                    Data Booking Jarum <?= $jarum ?>
                                </h5>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success bg-gradient-success shadow text-center border-radius-md">
                                Import Data Booking
                            </button>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalMessage" class="btn btn-success bg-gradient-info shadow text-center border-radius-md">
                                Input Data Booking
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade  bd-example-modal-lg" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
                <div class="modal-dialog  modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Input Data Booking</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= base_url('capacity/inputbooking') ?>" method="post">
                                <div class="form-group">
                                    <label for="tgl-bk-form-label">Tanggal Booking</label>
                                    <input type="date" class="form-control" name="tgl_booking">
                                </div>
                                <div class="form-group">
                                    <label for="no_order" class="col-form-label">No Order:</label>
                                    <input type="text" name="no_order" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="no_pdk" class="col-form-label">No PDK:</label>
                                    <input type="text" name="no_pdk" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="desc" class="col-form-label">Description:</label>
                                    <input type="text" name="desc" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="seam" class="col-form-label">Seam:</label>
                                    <input type="text" name="seam" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="opd" class="col-form-label">OPD:</label>
                                    <input type="date" name="opd" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="shipment" class="col-form-label">Shipment:</label>
                                    <input type="date" name="shipment" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="qty" class="col-form-label">QTY Booking (pcs):</label>
                                    <input type="number" name="qty" id="" class="form-control">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row mt-3">
        <div class="card">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7">Tgl Booking</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Buyer</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">No Order</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">No PDK</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Desc</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Seam</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">OPD</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Shipment</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Qty Booking</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Sisa Booking</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Action</th>

                            <th class="text-dark opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>