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

                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalMessage" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md">
                                Input Data Booking
                            </button>
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
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="jarum" hidden value="<?= $jarum ?>">
                                            <label for="tgl-bk" class="col-form-label">Tanggal Booking</label>
                                            <input type="date" class="form-control" name="tgl_booking">
                                        </div>
                                        <div class="form-group">
                                            <label for="buyer" class="col-form-label">Kode Buyer:</label>
                                            <input type="text" name="buyer" id="" class="form-control">
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
                                            <label for="productType" class="col-form-label">Product Type</label>
                                            <select class="form-control" id="productType" name="productType">
                                                <option>Choose</option>
                                                <?php foreach ($product as $pr) : ?>
                                                    <option><?= $pr['product_type'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="seam" class="col-form-label">Seam:</label>
                                            <input type="text" name="seam" id="" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="opd" class="col-form-label">OPD:</label>
                                            <input type="date" name="opd" id="opd" class="form-control" onchange="hitungJumlahHari()">
                                        </div>
                                        <div class=" form-group">
                                            <label for="shipment" class="col-form-label">Shipment:</label>
                                            <input type="date" name="shipment" id="shipment" class="form-control" onchange="hitungJumlahHari()">
                                        </div>
                                        <div class=" form-group">
                                            <label for="Lead" class="col-form-label">LeadTime</label>
                                            <input type="text" readonly name="lead" id="lead" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="qty" class="col-form-label">QTY Booking (pcs):</label>
                                            <input type="number" name="qty" id="" class="form-control">
                                        </div>
                                    </div>
                                </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Simpan</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
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
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Product Type</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Booking</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking as $bk) : ?>
                                <tr>
                                    <td class="text-xs"><?= $bk['tgl_terima_booking'] ?></td>
                                    <td class="text-xs"><?= $bk['kd_buyer_booking'] ?></td>
                                    <td class="text-xs"><?= $bk['no_order'] ?></td>
                                    <td class="text-xs"><?= $bk['no_booking'] ?></td>
                                    <td class="text-xs"><?= $bk['product_type'] ?></td>
                                    <td class="text-xs"><?= $bk['sisa_booking'] ?></td>
                                    <td class="text-xs"><?= $bk['status'] ?></td>
                                    <td class="text-xs"> <a href="<?= base_url('capacity/detailbooking/' . $bk['id_booking']) ?>" class="btn bg-gradient-success btn-sm text-xxs">detail</a> </td>
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