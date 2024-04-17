<?php $this->extend('Capacity/layout'); ?>
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
                                <label for="" class="form-control-label">Booking Receipt Date</label>
                                <input class="form-control" type="text" value="<?= $booking['tgl_terima_booking'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Buyer Code</label>
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
                                <label for="" class="form-control-label">Order Number</label>
                                <input class="form-control" type="text" value="<?= $booking['no_order'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Booking Number</label>
                                <input class="form-control" type="text" value="<?= $booking['no_booking'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Description</label>
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
                                <label for="" class="form-control-label">Booking Qty</label>
                                <input class="form-control" type="text" value="<?= $booking['qty_booking'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Booking Left</label>
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
                            <a href="" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#ModalBooking">Booking to Booking</a>
                            <a href="#" class="btn btn-info order-btn" data-bs-toggle="modal" data-bs-target="#exampleModalMessage">Booking to Order</a>
                            <a href="" class="btn btn-success" Data-bs-toggle="modal" data-bs-target="#ModalEdit">Edit Booking</a>
                            <a href="" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ModalCancel">Cancel Booking</a>
                            <a href="" class="btn btn-danger" Data-bs-toggle="modal" data-bs-target="#ModalDelete">Delete Booking</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Detail Booking To Order
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="display">
                            <thead>

                                <th>
                                    No Model
                                </th>
                                <th>Buyer Order</th>
                                <th>Order Placement Date</th>
                                <th>Qty Order</th>
                            </thead>
                            <tbody>
                                <?php foreach ($childOrder as $order) : ?>
                                    <tr>
                                        <td><?= $order['no_model'] ?></td>
                                        <td><?= $order['kd_buyer_order'] ?></td>
                                        <td><?= date_format(new DateTime($order['created_at']), 'd-F-Y') ?></td>
                                        <td><?= $order['qtyOrder'] ?> Pcs</td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Detail Booking To Booking
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable1" class="display">
                            <thead>

                                <th>
                                    Booking Receipt Date
                                </th>
                                <th>Buyer Booking</th>
                                <th>No Booking</th>
                                <th>Qty Booking</th>
                                <th>Desc</th>
                                <th>Needle</th>
                                <th>Seam</th>
                            </thead>
                            <tbody>
                                <?php foreach ($childBooking as $data) : ?>
                                    <tr>
                                        <td><?= date_format(new DateTime($data['tgl_terima_booking']), 'd-F-Y') ?></td>
                                        <td><?= $data['kd_buyer_booking'] ?></td>
                                        <td><?= $data['no_booking'] ?></td>
                                        <td><?= $data['qty_booking'] ?> </td>
                                        <td><?= $data['desc'] ?> </td>
                                        <td><?= $data['needle'] ?> </td>
                                        <td><?= $data['seam'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade  bd-example-modal-lg" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Convert Booking into Order</h5>
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
                                    <label for="col-lg-6 col-sm-12">Order Receipt Date</label>
                                    <input type="date" class="form-control" name="tgl_turun">
                                </div>
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">No Booking</label>
                                    <input type="text" class="form-control" name="no_booking" value="<?= $booking['no_booking']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">Description</label>
                                    <input type="text" class="form-control" name="deskripsi" oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">No Model</label>
                                    <input type="text" name="no_model" class="form-control" oninput="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">Beginning Remaining Booking</label>
                                    <input type="text" name="sisa_booking" class="form-control" value="<?= $booking['sisa_booking']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">Qty Order Place</label>
                                    <input type="text" name="turun_order" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="col-lg-6 col-sm-12">Ending Remaining Booking</label>
                                    <input type="text" name="sisa_booking_akhir" class="form-control">
                                </div>
                            </div>
                        </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-primary">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- modal edit -->
    <div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Booking</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('capacity/updatebooking/' . $booking['id_booking']) ?>" method="post">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="tgl-bk" class="col-form-label">Order Receipt Date</label>
                                    <input type="date" class="form-control" name="tgl_booking" value="<?= $booking['tgl_terima_booking'] ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="buyer" class="col-form-label">Buyer Code </label>
                                    <input type="text" name="buyer" id="" class="form-control" value="<?= $booking['kd_buyer_booking']; ?>" disabled>
                                </div>
                                <div class=" form-group">
                                    <label for="no_order" class="col-form-label">Order Number</label>
                                    <input type="text" name="no_order" id="" class="form-control" value="<?= $booking['no_order']; ?>" disabled>
                                </div>
                                <div class=" form-group">
                                    <label for="productType" class="col-form-label">Product Type</label>
                                    <input type="text" name="desc" id="" class="form-control" value="<?= $booking['product_type']; ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="no_pdk" class="col-form-label">Booking Number</label>
                                    <input type="text" name="no_booking" id="" class="form-control" value="<?= $booking['no_booking']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="desc" class="col-form-label">Description</label>
                                    <input type="text" name="desc" id="" class="form-control" value="<?= $booking['desc']; ?>" oninput="this.value = this.value.toUpperCase()">
                                </div>

                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="seam" class="col-form-label">Seam</label>
                                    <input type="text" name="seam" id="" class="form-control" value="<?= $booking['seam']; ?>" oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="form-group">
                                    <label for="opd" class="col-form-label">OPD</label>
                                    <input type="date" name="opd" id="opd" class="form-control" value="<?= $booking['opd']; ?>" onchange="hitungJumlahHari()">
                                </div>
                                <div class=" form-group">
                                    <label for="shipment" class="col-form-label">Shipment</label>
                                    <input type="date" name="delivery" id="shipment" value="<?= $booking['delivery']; ?>" class="form-control" onchange="hitungJumlahHari()">
                                </div>
                                <div class=" form-group">
                                    <label for="Lead" class="col-form-label">LeadTime</label>
                                    <input type="text" readonly name="lead" id="lead" value="<?= $booking['lead_time']; ?>" class=" form-control">
                                </div>
                                <div class="form-group">
                                    <label for="qty" class="col-form-label">QTY Booking (pcs)</label>
                                    <input type="number" name="qty" id="" class="form-control" value="<?= $booking['qty_booking']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="qty" class="col-form-label">Sisa Booking (pcs)</label>
                                    <input type="number" name="sisa" id="" class="form-control" value="<?= $booking['sisa_booking']; ?>">
                                </div>
                            </div>
                        </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-info">Edit</button>
                </div>
                </form>
            </div>
        </div>
    </div>


    <!-- modal delete -->
    <div class="modal fade  bd-example-modal-lg" id="ModalDelete" tabindex="-1" role="dialog" aria-labelledby="modaldelete" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Data Booking</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('capacity/deletebooking/' . $booking['id_booking']) ?>" method="post">
                        <input type="text" name="jarum" id="" hidden value="<?= $booking['needle'] ?>">
                        Are you sure to Delete Data Booking?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-danger">Delete</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade  bd-example-modal-lg" id="ModalCancel" tabindex="-1" role="dialog" aria-labelledby="modalCancel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cancel Booking</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('capacity/cancelbooking/' . $booking['id_booking']) ?>" method="post">
                        <input type="text" name="jarum" id="" hidden value="<?= $booking['needle'] ?>">
                        Are You Sure Want to Cancel Booking?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-danger">Cancel</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Booking -->
<div class="modal fade  bd-example-modal-lg" id="ModalBooking" tabindex="-1" role="dialog" aria-labelledby="modalbooking" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Split Booking</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('capacity/pecahbooking/' . $booking['id_booking']) ?>" method="post">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="tgl-bk" class="col-form-label">Booking Date</label>
                                <input type="date" class="form-control" name="tgl_booking">
                            </div>
                            <div class="form-group">
                                <label for="buyer" class="col-form-label">Buyer Code</label>
                                <input type="text" name="buyer" id="" class="form-control" oninput="this.value = this.value.toUpperCase()">
                            </div>
                            <div class="form-group">
                                <label for="jarum" class="col-form-label">Needle</label>
                                <select class="form-control" id="jarum" name="jarum">
                                    <option>Choose</option>
                                    <?php foreach ($jenisJarum as $jj) : ?>
                                        <option><?= $jj['jarum'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class=" form-group">
                                <label for="productType" class="col-form-label">Product Type</label>
                                <select class="form-control" id="productType" name="productType">
                                    <option>Choose</option>
                                    <?php foreach ($product as $pr) : ?>
                                        <option><?= $pr['product_type'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class=" form-group">
                                <label for="no_order" class="col-form-label">No Order:</label>
                                <input type="text" name="no_order" id="" class="form-control" oninput="this.value = this.value.toUpperCase()">
                            </div>

                            <div class="form-group">
                                <label for="no_pdk" class="col-form-label">No Booking:</label>
                                <input type="text" name="no_booking" id="" class="form-control" oninput="this.value = this.value.toUpperCase()">
                            </div>
                            <div class="form-group">
                                <label for="desc" class="col-form-label">Description:</label>
                                <input type="text" name="desc" id="" class="form-control" oninput="this.value = this.value.toUpperCase()">
                            </div>

                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="seam" class="col-form-label">Seam:</label>
                                <input type="text" name="seam" id="" class="form-control" oninput="this.value = this.value.toUpperCase()">
                            </div>
                            <div class="form-group">
                                <label for="opd" class="col-form-label">OPD:</label>
                                <input type="date" name="opd" id="opd1" class="form-control" onchange="hitungJumlahHari()">
                            </div>
                            <div class=" form-group">
                                <label for="shipment" class="col-form-label">Shipment:</label>
                                <input type="date" name="delivery" id="shipment1" class="form-control" onchange="hitungJumlahHari()">
                            </div>
                            <div class=" form-group">
                                <label for="Lead" class="col-form-label">LeadTime</label>
                                <input type="text" readonly name="lead" id="lead1" class=" form-control">
                            </div>
                            <div class="form-group">
                                <label for="qty" class="col-form-label">QTY Booking (pcs):</label>
                                <input type="number" name="qty" id="" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="qty" class="col-form-label">Booking Left (pcs):</label>
                                <input type="number" name="sisa" id="" class="form-control">
                            </div>
                        </div>
                    </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn bg-gradient-info">Edit</button>
            </div>
            </form>
        </div>
    </div>
</div>


</div>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
        $('#dataTable1').DataTable();
    });
</script>
<script>
    function hitungJumlahHari() {
        var opdString = document.getElementById("opd").value
        var shipmentString = document.getElementById("shipment").value
        var opdString1 = document.getElementById("opd1").value
        var shipmentString1 = document.getElementById("shipment1").value

        var opd = new Date(opdString)
        var shipment = new Date(shipmentString)
        var opd1 = new Date(opdString1)
        var shipment1 = new Date(shipmentString1)

        var timeDiff = shipment.getTime() - opd.getTime()
        var timeDiff1 = shipment1.getTime() - opd1.getTime()
        var leanTime = Math.floor(timeDiff / (1000 * 60 * 60 * 24))
        var leanTime1 = Math.floor(timeDiff1 / (1000 * 60 * 60 * 24))
        var leadTime = leanTime - 7;
        var leadTime1 = leanTime1 - 7;

        if (leadTime <= 14) {
            document.getElementById("lead").value = "invalid Lead Time"
        } else {
            document.getElementById("lead").value = leadTime

        }
        if (leadTime1 <= 14) {
            document.getElementById("lead1").value = "invalid Lead Time"
        } else {
            document.getElementById("lead1").value = leadTime1

        }

    }
</script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>