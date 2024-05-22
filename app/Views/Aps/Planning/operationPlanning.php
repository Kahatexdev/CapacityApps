<?php $this->extend('Aps/layout'); ?>
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
                            Planning Order
                        </h5>
                    </div>

                </div>
                <div class="card-body p-3">
                <?php foreach($planning as $key => $item): ?>
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">No Model</label>
                                <input class="form-control" type="text" value="<?= $item['model'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Delivery</label>
                                <input class="form-control" type="text" value="<?= $item['delivery'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Qty</label>
                                <input class="form-control" type="text" value="<?= $item['qty'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Remaining Qty</label>
                                <input class="form-control" type="text" value="<?= $item['sisa'] ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Target 100%</label>
                                <input class="form-control" type="text" value="<?= round(3600/$item['smv'],2) ?>" readonly id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Description</label>
                                <input class="form-control" type="text" value="" id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">OPD</label>
                                <input class="form-control" type="text" value="" id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Delivery</label>
                                <input class="form-control" type="text" value="" id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Booking Qty (Pcs) </label>
                                <input class="form-control" type="text" value="" id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Booking Remaining (Pcs) </label>
                                <input class="form-control" type="text" value="" id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Needle</label>
                                <input class="form-control" type="text" value="" id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Seam</label>
                                <input class="form-control" type="text" value="" id="">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="" class="form-control-label">Lead Time</label>
                                <input class="form-control" type="text" value="" id="">
                            </div>
                        </div>

                    </div>
                <?php endforeach ?>
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
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
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
                                    <tr>
                                       
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Booking -->



</div>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

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

    function calculateRemaining() {
        var availableQuantity = parseInt(document.getElementById("sisa_booking").value);
        var quantityToCancel = parseInt(document.getElementById("qty_cancel").value);

        // Check if quantity to cancel exceeds available quantity
        if (quantityToCancel > availableQuantity) {
            // Show SweetAlert error message
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Quantity to cancel cannot exceed available quantity.',
            });

            // Reset quantity to cancel to available quantity
            document.getElementById("qty_cancel").value = availableQuantity;
            quantityToCancel = availableQuantity; // Reset quantity to cancel
        }

        var remainingQuantity = availableQuantity - quantityToCancel;
        document.getElementById("sisa_booking_remaining").value = remainingQuantity;
    }

    function cancelBooking() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to cancel this booking.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the form
                document.querySelector('form').submit();
            }
        });
    }

    document.getElementById('cancelBookingBtn').addEventListener('click', cancelBooking);
</script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>