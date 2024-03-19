<?php $this->extend('Capacity/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <h5>
                        Detail Booking
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Tanggal Terima Booking</label>
                                <input class="form-control" type="text" value="<?= $booking['tgl_terima_booking'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Kode Buyer</label>
                                <input class="form-control" type="text" value="<?= $booking['kd_buyer_booking'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Product Type</label>
                                <input class="form-control" type="text" value="<?= $booking['product_type'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">No Order</label>
                                <input class="form-control" type="text" value="<?= $booking['no_order'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">No Booking</label>
                                <input class="form-control" type="text" value="<?= $booking['no_booking'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Deskripsi</label>
                                <input class="form-control" type="text" value="<?= $booking['desc'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">OPD</label>
                                <input class="form-control" type="text" value="<?= $booking['opd'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Delivery</label>
                                <input class="form-control" type="text" value="<?= $booking['delivery'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Qty Booking</label>
                                <input class="form-control" type="text" value="<?= $booking['qty_booking'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Sisa Booking</label>
                                <input class="form-control" type="text" value="<?= $booking['sisa_booking'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Needle</label>
                                <input class="form-control" type="text" value="<?= $booking['needle'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Seam</label>
                                <input class="form-control" type="text" value="<?= $booking['seam'] ?>" readonly id="">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">

                            <a href="" class="btn btn-info">Booking to Booking</a>
                            <a href="" class="btn btn-info">Booking to Order</a>
                            <a href="" class="btn btn-success">Edit Booking</a>
                        </div>
                        <div class="col-lg-4">
                            <a href="" class="btn btn-warning">Cancel Booking</a>
                            <a href="" class="btn btn-danger">Delete Booking</a>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <?php $this->endSection(); ?>