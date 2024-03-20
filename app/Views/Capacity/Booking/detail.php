<?php $this->extend('Capacity/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Detail Booking
                        </h5>
                        <a href="<?= base_url('capacity/databooking/' . $jarum['needle']) ?>" class="btn bg-gradient-info"> Kembali</a>
                    </div>

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
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Lead Time</label>
                                <input class="form-control" type="text" value="<?= $booking['lead_time'] ?>" readonly id="">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="" class="btn btn-info">Booking to Booking</a>
                            <a href="#" class="btn btn-info order-btn" data-bs-toggle="modal" data-bs-target="#exampleModalMessage">Booking to Order</a>
                            <a href="" class="btn btn-success">Edit Booking</a>
                            <a href="" class="btn btn-warning">Cancel Booking</a>
                            <a href="" class="btn btn-danger">Delete Booking</a>
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

                <div class="modal fade  bd-example-modal-lg" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
                    <div class="modal-dialog  modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Ambil Booking Jadi Order</h5>
                                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url("capacity/inputOrder"); ?>" method="post">
                                    <input type="text" name="id_booking" value="<?= $booking['id_booking']; ?>" hidden>
                                    <input type="text" name="jarum" value="<?= $booking['needle']; ?>" hidden>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="col-lg-6 col-sm-12">Tanggal Turun Order</label>
                                                <input type="date" class="form-control" name="tgl_turun">
                                            </div>
                                            <div class="form-group">
                                                <label for="col-lg-6 col-sm-12">No Booking</label>
                                                <input type="text" class="form-control" name="no_booking" value="<?= $booking['no_booking']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="col-lg-6 col-sm-12">Deskripsi</label>
                                                <input type="text" class="form-control" name="deskripsi">
                                            </div>
                                            <div class="form-group">
                                                <label for="col-lg-6 col-sm-12">No Model</label>
                                                <input type="text" name="no_model" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="col-lg-6 col-sm-12">Sisa Booking Awal</label>
                                                <input type="text" name="sisa_booking" class="form-control" value="<?= $booking['sisa_booking']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="col-lg-6 col-sm-12">Turun Order</label>
                                                <input type="text" name="turun_order" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="col-lg-6 col-sm-12">Sisa Booking Akhir</label>
                                                <input type="text" name="sisa_booking_akhir" class="form-control">
                                            </div>
                                        </div>
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

        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>