<?php $this->extend('Planning/layout'); ?>
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
                        <a href="<?= base_url('planning/databookingbulantampil/' . date('F/Y', strtotime($booking['delivery'])) . '/' . $jarum['needle']) ?>" class="btn bg-gradient-dark">
                            <i class="fas fa-arrow-circle-left text-lg opacity-10" aria-hidden="true" style="margin-right: 0.5rem;"></i> Back
                        </a>
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
                                <label for="" class="form-control-label">Booking Qty (Pcs) </label>
                                <input class="form-control" type="text" value="<?= $booking['qty_booking'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Booking Remaining (Pcs) </label>
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