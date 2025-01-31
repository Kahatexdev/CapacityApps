<?php $this->extend($role . '/layout'); ?>
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
                            Detail Data Model <?= $noModel ?> Delivery <?= date('d-M-Y', strtotime($delivery)) ?>
                        </h5>
                        <a href="<?= base_url($role . '/semuaOrder/') ?>" class="btn bg-gradient-dark d-inline-flex align-items-center">
                            <i class="fas fa-arrow-circle-left me-2 text-lg opacity-10"></i>
                            Back
                        </a>

                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable" class="display compact striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style Size</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty (Dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Remaining Qty (Dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Seam</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">PU</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                        <th colspan=2 class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dataAps as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['machinetypeid']; ?></td>
                                            <td class="text-sm"><?= $order['size']; ?></td>
                                            <td class="text-sm"><?= date('d-M-y', strtotime($order['delivery'])); ?></td>
                                            <?php
                                            // Jika machinetypeid = 240n, bagi qty dan sisa dengan 12, selain itu bagi dengan 24
                                            $divider = ($order->machinetypeid == '240N') ? 12 : 24;
                                            ?>

                                            <td class="text-xs"><?= number_format(round($order->qty / $divider), 0, ',', '.'); ?> Dz</td>
                                            <td class="text-xs"><?= number_format(round($order->sisa / $divider), 0, ',', '.'); ?> Dz</td>

                                            <td class="text-sm"><?= $order['seam']; ?></td>
                                            <td class="text-sm"><?= $order['production_unit']; ?></td>
                                            <td class="text-sm"><?= $order['factory']; ?></td>
                                            <td class="text-sm">
                                                <button type="button" class="btn btn-success btn-sm import-btn" data-toggle="modal" data-target="#EditModal" data-id="<?= $order['idapsperstyle']; ?>" data-no-model="<?= $order['mastermodel']; ?>" data-delivery="<?= $order['delivery']; ?>" data-jarum="<?= $order['machinetypeid']; ?>" data-style="<?= $order['size']; ?>" data-qty="<?= $order['qty']; ?>" data-sisa="<?= $order['sisa']; ?>" data-seam="<?= $order['seam']; ?>" data-factory="<?= $order['factory']; ?>" data-production_unit="<?= $order['production_unit']; ?>">
                                                    Edit
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#ModalEdit" data-id="<?= $order['idapsperstyle']; ?>" data-no-model="<?= $order['mastermodel']; ?>" data-delivery="<?= $order['delivery']; ?>">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Total</th>
                                        <th></th> <!-- This will hold the total for Qty (Dz) -->
                                        <th></th> <!-- Optionally, any total for Remaining Qty -->
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th colspan="2"></th> <!-- Empty cells for the action columns -->
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="#" class="btn btn-danger btn-delete-all" Data-bs-toggle="modal" data-bs-target="ModalDeleteAll" data-no-model="<?= $noModel ?>">Delete All</a>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
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
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="" class="col-form-label">ID</label>
                                        <input type="text" class="form-control" name="idapsperstyle" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">No Model</label>
                                        <input type="text" class="form-control" name="no_model" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Style</label>
                                        <input type="text" class="form-control" name="style">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Delivery</label>
                                        <input type="date" name="delivery" id="" class="form-control">
                                    </div>
                                    <div class=" form-group">
                                        <label for="" class="col-form-label">Quantity</label>
                                        <input type="number" name="qty" id="" class="form-control">
                                    </div>
                                    <div class=" form-group">
                                        <label for="" class="col-form-label">Sisa</label>
                                        <input type="text" name="sisa" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Seam</label>
                                        <input type="text" name="seam" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">SMV</label>
                                        <input type="text" name="smv" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Production Unit</label>
                                        <input type="text" name="production_unit" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Areal</label>
                                        <input type="text" name="factory" id="" class="form-control">
                                    </div>

                                </div>
                            </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Ubah</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade  bd-example-modal-lg" id="ModalDelete" tabindex="-1" role="dialog" aria-labelledby="modaldelete" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Hapus Data Style</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post">
                            <input type="hidden" class="form-control" name="no_model">
                            <input type="hidden" class="form-control" name="delivery">
                            <input type="text" name="idapsperstyle" id="" hidden value="">
                            Apakah anda yakin ingin menghapus Data Style?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Hapus</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade  bd-example-modal-lg" id="ModalDeleteAll" tabindex="-1" role="dialog" aria-labelledby="modaldeleteall" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Hapus Semua Style ?</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post">
                            <input type="text" name="no_model" id="" hidden value="<?= $noModel ?>">
                            Apakah anda yakin menghapus semua style di Model <?= $noModel ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Hapus</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-2">
        <div class="col-lg-12">
            <div class="card z-index-2">
                <div class="card-header">

                    <h6 class="card-title"> Progress chart active order</h6>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <h6 class="card-title">PDK</h6>
                        </div>

                        <div class="col-lg-10">
                            <h6 class="card-title">Progress</h6>
                        </div>
                    </div>

                    <div id="progress-container">
                        <?php foreach ($progress as $key) : ?>
                            <div class="row">
                                <div class="col-lg-2">
                                    <h6 class="card-title"><?= $key['mastermodel'] ?></h6>
                                </div>
                                <div class="col-lg-8">
                                    <div class="progress-wrapper">
                                        <div class="progress-info">
                                            <div class="progress-percentage">
                                                <span class="text-sm font-weight-bold " id="<?= $key['mastermodel'] ?>-progressText"> <?= $key['persen'] ?>% (<?= $key['remain'] ?> dz / <?= $key['target'] ?> dz)</span>
                                            </div>
                                        </div>
                                        <!-- Tambahkan ID ke elemen progress bar -->
                                        <div class="progress">
                                            <div id="<?= $key['mastermodel'] ?>-progress-bar" class="progress-bar bg-info" role="progressbar" aria-valuenow="<?= $key['persen'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $key['persen'] ?>%; height: 10px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>


                </div>
            </div>

        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "pageLength": 35,
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api();

                    // Calculate the total of the 4th column (Qty in dozens) - index 3
                    var totalQty = api.column(3, {
                        page: 'current'
                    }).data().reduce(function(a, b) {
                        return parseInt(a) + parseInt(b);
                    }, 0);

                    // Calculate the total of the 5th column (Remaining Qty in dozens) - index 4
                    var totalRemainingQty = api.column(4, {
                        page: 'current'
                    }).data().reduce(function(a, b) {
                        return parseInt(a) + parseInt(b);
                    }, 0);

                    var totalqty = numberWithDots(totalQty) + " Dz";
                    var totalsisa = numberWithDots(totalRemainingQty) + " Dz";

                    // Update the footer cell for the total Qty
                    $(api.column(3).footer()).html(totalqty);

                    // Update the footer cell for the total Remaining Qty
                    $(api.column(4).footer()).html(totalsisa);
                }
            });

            function numberWithDots(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }


            $('.import-btn').click(function() {
                var apsperstyle = $(this).data('id');
                var noModel = $(this).data('no-model');
                var delivery = $(this).data('delivery');
                var jarum = $(this).data('jarum');
                var style = $(this).data('style');
                var qty = $(this).data('qty');
                var sisa = $(this).data('sisa');
                var seam = $(this).data('seam');
                var smv = $(this).data('smv');
                var production_unit = $(this).data('production_unit');
                var factory = $(this).data('factory');

                var formattedDelivery = new Date(delivery).toISOString().split('T')[0];

                $('#ModalEdit').find('form').attr('action', '<?= base_url($role . '/updatedetailorder/') ?>' + apsperstyle);
                $('#ModalEdit').find('input[name="idapsperstyle"]').val(apsperstyle);
                $('#ModalEdit').find('input[name="style"]').val(style);
                $('#ModalEdit').find('input[name="no_model"]').val(noModel);
                $('#ModalEdit').find('input[name="delivery"]').val(formattedDelivery);
                $('#ModalEdit').find('input[name="qty"]').val(qty);
                $('#ModalEdit').find('input[name="sisa"]').val(sisa);
                $('#ModalEdit').find('input[name="seam"]').val(seam);
                $('#ModalEdit').find('input[name="smv"]').val(smv);
                $('#ModalEdit').find('input[name="production_unit"]').val(production_unit);
                $('#ModalEdit').find('input[name="factory"]').val(factory);

                $('#ModalEdit').modal('show'); // Show the modal
            });
            $('.delete-btn').click(function() {
                var noModel = $(this).data('no-model');
                var delivery = $(this).data('delivery');
                var apsperstyle = $(this).data('id');
                var formattedDelivery = new Date(delivery).toISOString().split('T')[0];
                $('#ModalDelete').find('form').attr('action', '<?= base_url($role . '/deletedetailstyle/') ?>' + apsperstyle);
                $('#ModalDelete').find('input[name="idapsperstyle"]').val(apsperstyle);
                $('#ModalDelete').find('input[name="no_model"]').val(noModel);
                $('#ModalDelete').find('input[name="delivery"]').val(formattedDelivery);
                $('#ModalDelete').modal('show'); // Show the modal
            });
            $('.btn-delete-all').click(function() {
                var noModel = $(this).data('no-model');
                $('#ModalDeleteAll').find('form').attr('action', '<?= base_url($role . '/deletedetailorder/') ?>' + noModel);
                $('#ModalDeleteAll').find('input[name="idapsperstyle"]').val(noModel);
                $('#ModalDeleteAll').modal('show'); // Show the modal
            });
        });
    </script>
    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script>
        $(document).ready(function() {
            function fetchData() {
                var noModel = $(this).data('no-model');

                $.ajax({
                    url: '<?= base_url($role . '/dataprogress/') ?>' + noModel,
                    type: 'GET',
                    success: function(responseData) {

                        updateProgressBars(responseData);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            function updateProgressBars(progressData) {
                var tes = JSON.parse(progressData);
                tes.forEach(function(item) {
                    var progressBarId = item.mastermodel + '-progress-bar';
                    var progressBar = $('#' + progressBarId);
                    var progressTextId = item.mastermodel + '-progressText';
                    var progressText = $('#' + progressTextId);
                    if (progressBar.length > 0) {
                        progressBar.css('width', item.persen + '%').attr('aria-valuenow', item.persen);
                        progressBar.text(item.persen + '%');
                        progressText.text(item.persen + '% (' + item.remain + ' dz / ' + item.target + ' dz)'); // Mengubah teks ke 'tes'
                    } else {
                        console.error('Progress bar element not found for ID:', progressBarId);
                    }
                });
            }

            setInterval(fetchData, 10000000);
            fetchData();
        });
    </script>
    <?php $this->endSection(); ?>