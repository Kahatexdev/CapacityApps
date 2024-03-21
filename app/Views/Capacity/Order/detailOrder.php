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
                            Detail Data Model <?= $noModel ?> Delivery <?= date('d-M-Y', strtotime($delivery)) ?>
                        </h5>
                        <a href="<?= base_url('capacity/semuaOrder/') ?>" class="btn bg-gradient-info"> Kembali</a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable" class="display compact striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Seam</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Factory</th>
                                        <th colspan=2 class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dataAps as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['machinetypeid']; ?></td>
                                            <td class="text-sm"><?= $order['size']; ?></td>
                                            <td class="text-sm"><?= $order['delivery']; ?></td>
                                            <td class="text-sm"><?= $order['qty']; ?></td>
                                            <td class="text-sm"><?= $order['sisa']; ?></td>
                                            <td class="text-sm"><?= $order['seam']; ?></td>
                                            <td class="text-sm"><?= $order['factory']; ?></td>
                                            <td class="text-sm">
                                                <button type="button" class="btn btn-success btn-sm import-btn" data-toggle="modal" data-target="#EditModal" data-id="<?= $order['idapsperstyle']; ?>" data-no-model="<?= $order['mastermodel']; ?>" data-delivery="<?= $order['delivery']; ?>" data-jarum="<?= $order['machinetypeid']; ?>" data-style="<?= $order['size']; ?>" data-qty="<?= $order['qty']; ?>" data-sisa="<?= $order['sisa']; ?>" data-seam="<?= $order['seam']; ?>" data-factory="<?= $order['factory']; ?>">
                                                    Edit
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#ModalEdit" data-id="<?= $order['idapsperstyle']; ?>">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="#" class="btn btn-danger btn-delete-all" Data-bs-toggle="modal" data-bs-target="ModalDeleteAll">Delete All</a>
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
                                        <label for="tgl-bk" class="col-form-label">No Model</label>
                                        <input type="text" class="form-control" name="no_model" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">Style</label>
                                        <input type="text" class="form-control" name="style">
                                    </div>
                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Delivery</label>
                                        <input type="date" name="delivery" id="" class="form-control">
                                    </div>
                                    <div class=" form-group">
                                        <label for="no_order" class="col-form-label">Quantity</label>
                                        <input type="number" name="qty" id="" class="form-control">
                                    </div>
                                    <div class=" form-group">
                                        <label for="productType" class="col-form-label">Sisa</label>
                                        <input type="text" name="sisa" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="no_pdk" class="col-form-label">Seam</label>
                                        <input type="text" name="seam" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="desc" class="col-form-label">Areal</label>
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
                                    <input type="text" name="idapsperstyle" id="" hidden value="">
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
        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable();

                $('.import-btn').click(function() {
                    var apsperstyle = $(this).data('id');
                    var noModel = $(this).data('no-model');
                    var delivery = $(this).data('delivery');
                    var jarum = $(this).data('jarum');
                    var style = $(this).data('style');
                    var qty = $(this).data('qty');
                    var sisa = $(this).data('sisa');
                    var seam = $(this).data('seam');
                    var factory = $(this).data('factory');

            var formattedDelivery = new Date(delivery).toISOString().split('T')[0];
            
            $('#ModalEdit').find('form').attr('action', '<?= base_url('capacity/updatedetailorder/') ?>' + apsperstyle);
            $('#ModalEdit').find('input[name="style"]').val(style);
            $('#ModalEdit').find('input[name="no_model"]').val(noModel);
            $('#ModalEdit').find('input[name="delivery"]').val(formattedDelivery);
            $('#ModalEdit').find('input[name="qty"]').val(qty);
            $('#ModalEdit').find('input[name="sisa"]').val(sisa);
            $('#ModalEdit').find('input[name="seam"]').val(seam);
            $('#ModalEdit').find('input[name="factory"]').val(factory);
            
            $('#ModalEdit').modal('show'); // Show the modal
        });
        $('.delete-btn').click(function() {
            var apsperstyle = $(this).data('id');
            $('#ModalDelete').find('form').attr('action', '<?= base_url('capacity/deletedetailstyle/') ?>' + apsperstyle);
            $('#ModalDelete').find('input[name="idapsperstyle"]').val(apsperstyle);
            $('#ModalDelete').modal('show'); // Show the modal
        });
        $('.btn-delete-all').click(function() {
            var noModel = $(this).data('no-model');
            $('#ModalDeleteAll').find('form').attr('action', '<?= base_url('capacity/deletedetailorder/') ?>' + noModel);
            $('#ModalDeleteAll').find('input[name="idapsperstyle"]').val(noModel);
            $('#ModalDeleteAll').modal('show'); // Show the modal
        });
        });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>