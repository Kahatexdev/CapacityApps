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
                            Detail Stock Cylinder
                        </h5>
                        <div>
                            <a href="<?= base_url('capacity/mesinperarea') ?>" class="btn bg-gradient-info"> Back</a>
                            <button type="button" class="btn btn-add bg-gradient-success" data-toggle="modal" data-target="#modalTambah">Input Data Mesin</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable" class="display compact striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Production Unit</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Type Machine</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle Detail</th>
                                        <th colspan=2 class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tampildata as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['needle']; ?></td>
                                            <td class="text-sm"><?= $order['production_unit']; ?></td>
                                            <td class="text-sm"><?= $order['type_machine']; ?></td>
                                            <td class="text-sm"><?= $order['qty'];?> Unit</td>
                                            <td class="text-sm"><?= $order['needle_detail']; ?></td>
                                            <td class="text-sm">
                                                <button type="button" class="btn btn-success btn-sm edit-btn" data-toggle="modal" data-target="#EditModal" data-id="<?= $order['id']; ?>" data-needle="<?= $order['needle']; ?>" data-pu="<?= $order['production_unit']; ?>" data-type="<?= $order['type_machine']; ?>" data-qty="<?= $order['qty']; ?>" data-nd="<?= $order['needle_detail']; ?>" >
                                                    Edit
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#ModalDelete" data-id="<?= $order['id']; ?>" data-needle="<?= $order['needle']; ?>">
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

                    </div>

                </div>


            </div>
        </div>

        <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambah" aria-hidden="true">
            <div class="modal-dialog"   role=" document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Data Mesin</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?=base_url('capacity/addcylinder')?>" method="post">
                            <div class="row">
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">Needle</label>
                                        <input type="text" class="form-control" name="needle" oninput="this.value = this.value.toUpperCase()">
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label">Production Unit</label><br>
                                        <input type="radio" id="cijerah" name="production_unit" value="Cijerah">
                                        <label for="cijerah">Cijerah</label><br>
                                        <input type="radio" id="majalaya" name="production_unit" value="Majalaya">
                                        <label for="majalaya">Majalaya</label><br>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">

                                    <div class="form-group">
                                        <label for="type_machine" class="col-form-label">Type Machine</label>
                                        <select name="type_machine" class="form-control">
                                            <option value="" selected disabled>Please choose...</option>
                                            <option value="THS DOUBLE">THS DOUBLE</option>
                                            <option value="CYLINDER 306">CYLINDER 306</option>
                                            <option value="CYLINDER 308/318">CYLINDER 308/318</option>
                                            <option value="CYLINDER ROSSO">CYLINDER ROSSO</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Qty</label>
                                        <input type="text" name="qty" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label">Needle Detail</label><br>
                                        <input type="radio" id="needle_empty" name="needle_detail" value="Empty">
                                        <label for="needle_empty">Empty</label><br>
                                        <input type="radio" id="needle_filled" name="needle_detail" value="Filled">
                                        <label for="needle_filled">Filled</label><br>
                                    </div>
                                </div>
                            </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Tambah Data</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
            <div class="modal-dialog"   role=" document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Data Cylinder</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?=base_url('capacity/editycylinder')?>" method="post">
                        <div class="row">
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">Needle</label>
                                        <input type="text" class="form-control" name="needle" oninput="this.value = this.value.toUpperCase()">
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label">Production Unit</label><br>
                                        <input type="radio" id="cijerah" name="production_unit" value="Cijerah">
                                        <label for="cijerah">Cijerah</label><br>
                                        <input type="radio" id="majalaya" name="production_unit" value="Majalaya">
                                        <label for="majalaya">Majalaya</label><br>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">

                                    <div class="form-group">
                                        <label for="type_machine" class="col-form-label">Type Machine</label>
                                        <select name="type_machine" class="form-control">
                                            <option value="" selected disabled>Please choose...</option>
                                            <option value="THS DOUBLE">THS DOUBLE</option>
                                            <option value="CYLINDER 306">CYLINDER 306</option>
                                            <option value="CYLINDER 308/318">CYLINDER 308/318</option>
                                            <option value="CYLINDER ROSSO">CYLINDER ROSSO</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Qty</label>
                                        <input type="text" name="qty" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label">Needle Detail</label><br>
                                        <input type="radio" id="needle_empty" name="needle_detail" value="Empty">
                                        <label for="needle_empty">Empty</label><br>
                                        <input type="radio" id="needle_filled" name="needle_detail" value="Filled">
                                        <label for="needle_filled">Filled</label><br>
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
                        <h5 class="modal-title" id="exampleModalLabel">Delete Cylinder Data</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post">
                            <input type="text" name="id" id="" hidden value="">
                            Are You Sure?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Delete</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- <script>
            function valildasi() {
                let qty = parseInt(document.getElementById("qty").value);
                let sisa = parseInt(document.getElementById("sisa").value);

                if (sisa > qty) {
                    alert("Qty tidak boleh melebihi sisa!");
                    document.getElementById("sisa").value = qty; // Reset nilai qty menjadi nilai sisa
                }
            }
        </script> -->
        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable();

                $('.btn-add').click(function() {
                    var form = $('#modalTambah').find('form');
                
                    $('#modalTambah').modal('show'); // Show the modal
                });

                $('.edit-btn').click(function() {
                    var id = $(this).data('id');
                    var needle = $(this).data('needle');
                    var pu = $(this).data('pu');
                    var type = $(this).data('type');
                    var qty = $(this).data('qty');
                    var nd = $(this).data('nd');

                    // Set form action
                    $('#ModalEdit').find('form').attr('action', '<?= base_url('capacity/editcylinder/') ?>' + id);

                    // Set input values
                    $('#ModalEdit').find('input[name="needle"]').val(needle);
                    $('#ModalEdit').find('input[name="qty"]').val(qty);

                    // Set radio button selection
                    $('#ModalEdit').find('input[name="production_unit"][value="' + pu + '"]').prop('checked', true);
                    $('#ModalEdit').find('input[name="needle_detail"][value="' + nd + '"]').prop('checked', true);

                    // Set dropdown selection
                    $('#ModalEdit').find('select[name="type_machine"]').val(type);

                    $('#ModalEdit').modal('show'); // Show the modal
                });



                $('.delete-btn').click(function() {
                    var id = $(this).data('id');
                    $('#ModalDelete').find('form').attr('action', '<?= base_url('capacity/deletecylinder/') ?>' + id);
                    $('#ModalDelete').find('input[name="id"]').val(id);
                    
                    $('#ModalDelete').modal('show'); // Show the modal
                });

            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>