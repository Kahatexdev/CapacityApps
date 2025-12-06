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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Detail Cancel Booking
                                </h5>
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
        </div>

    </div>


    <div class="row mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="display compact " style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Tgl Booking</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Buyer</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Order</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No PDK</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Booking</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Booking</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Cancel</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $bk) : ?>
                                <tr>
                                    <td class="text-xs"><?= date('d-M-Y', strtotime($bk['tgl_terima_booking'])) ?></td>
                                    <td class="text-xs"><?= $bk['kd_buyer_booking'] ?></td>
                                    <td class="text-xs"><?= $bk['no_order'] ?></td>
                                    <td class="text-xs"><?= $bk['no_booking'] ?></td>
                                    <td class="text-xs"><?= round($bk['qty_booking'] / 24) ?> Dz</td>
                                    <td class="text-xs"><?= round($bk['sisa_booking'] / 24) ?> Dz</td>
                                    <td class="text-xs"><?= round($bk['qty_cancel'] / 24) ?> Dz</td>
                                    <td class="text-xs"><?= $bk['alasan'] ?></td>
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
</script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>