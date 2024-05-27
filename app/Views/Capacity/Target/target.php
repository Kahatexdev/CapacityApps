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
                                    Data Target Jarum <?= $jarum ?>
                                </h5>
                                <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#ModalAdd">
                                    <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Tambah Target
                                </button>

                            </div>
                        </div>
                        <div>
                            
                        </div>
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
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Product Type</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Target</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" colspan="2" style="text-align: center;">Operation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($product as $order) : ?>
                                <tr>
                                <td class="text-s"><?= $order['product_type']; ?></td>
                                <td class="text-s"><?= $order['keterangan']; ?></td>
                                <td class="text-s"><?= $order['jarum']; ?></td>
                                <td class="text-s"><?= $order['konversi']; ?> Dz</td>
                                <td class="text-s">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="<?= $order['id_product_type']; ?>" data-product="<?= $order['product_type']; ?>" data-keterangan="<?= $order['keterangan']; ?>" data-jarum="<?= $order['jarum']; ?>" data-target="<?= $order['konversi']; ?>">
                                        Edit
                                    </button>   
                                </td>
                                <td class="text-s">
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#ModalDelete" data-id="<?= $order['id_product_type']; ?>" data-product="<?= $order['product_type']; ?>" data-jarum="<?= $order['jarum']; ?>">
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

    <!-- modal -->
    <div class="modal fade  bd-example-modal-lg" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Data Target</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url('capacity/edittarget'); ?>" method="post">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <input type="hidden" class="form-control" name="id" readonly>

                                    <div class="form-group">
                                        <label for="" class="col-form-label">Product Type</label>
                                        <input type="text" class="form-control" name="producttype">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Keterangan</label>
                                        <input type="text" class="form-control" name="keterangan">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Jarum</label>
                                        <input type="text" class="form-control" name="jarum">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Target Dz</label>
                                        <input type="number" class="form-control" name="target" step="0.01" min="0" value="0.00">
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

        <div class="modal fade  bd-example-modal-lg" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="modalAdd" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Data Target</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url('capacity/addtarget'); ?>" method="post">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Product Type</label>
                                        <input type="text" class="form-control" name="producttype">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Keterangan</label>
                                        <input type="text" class="form-control" name="keterangan">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Jarum</label>
                                        <input type="text" class="form-control" name="jarum" value ="<?= $jarum ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Target Dz</label>
                                        <input type="number" class="form-control" name="target" step="0.01" min="0" value="0.00">
                                    </div>
                                </div>
                            </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Add</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ModalDelete" tabindex="-1" aria-labelledby="ModalDeleteLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalDeleteLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <form id="deleteForm" method="post" action="<?= site_url('capacity/deletetarget'); ?>">
                    <input type="hidden" name="id" id="deleteId">
                    <input type="hidden" name="jarum" id="jarumId">
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
                </div>
            </div>
        </div>


    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
    // Trigger edit modal when edit button is clicked
        $('.btn-primary').click(function() {
            var id = $(this).data('id');
            var product = $(this).data('product');
            var keterangan = $(this).data('keterangan');
            var jarum = $(this).data('jarum');
            var konversi = $(this).data('target');

            $('#ModalEdit').find('input[name="id"]').val(id);
            $('#ModalEdit').find('input[name="producttype"]').val(product); // Fixed input name
            $('#ModalEdit').find('input[name="keterangan"]').val(keterangan);
            $('#ModalEdit').find('input[name="jarum"]').val(jarum);
            $('#ModalEdit').find('input[name="target"]').val(konversi);

            $('#ModalEdit').modal('show'); // Show the modal
        });

        $('#example').DataTable({
            "order": [],
            "pageLength": 25
        });

        $('#ModalDelete').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id'); // Extract info from data-* attributes
        var product = button.data('product');
        var jarum = button.data('jarum');

        var modal = $(this);
        modal.find('.modal-body').html('Are you sure you want to delete the item with ID ' + id + ', Product: ' + product + ', Jarum: ' + jarum + '?');
        $('#deleteId').val(id);
        $('#jarumId').val(jarum);
        });

        $('#confirmDeleteBtn').click(function() {
        // Submit the delete form
        $('#deleteForm').submit();
        });
    });

    </script>
    <?php $this->endSection(); ?>