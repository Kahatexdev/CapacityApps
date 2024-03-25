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
                            Data Produksi <?= $area ?>
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
                                    <?php foreach ($produksi as $order) : ?>
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
                                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#ModalEdit" data-id="<?= $order['idapsperstyle']; ?>" data-no-model="<?= $order['mastermodel']; ?>" data-delivery="<?= $order['delivery']; ?>">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>



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
                    var noModel = $(this).data('no-model');
                    var delivery = $(this).data('delivery');
                    var apsperstyle = $(this).data('id');
                    var formattedDelivery = new Date(delivery).toISOString().split('T')[0];
                    $('#ModalDelete').find('form').attr('action', '<?= base_url('capacity/deletedetailstyle/') ?>' + apsperstyle);
                    $('#ModalDelete').find('input[name="idapsperstyle"]').val(apsperstyle);
                    $('#ModalDelete').find('input[name="no_model"]').val(noModel);
                    $('#ModalDelete').find('input[name="delivery"]').val(formattedDelivery);
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