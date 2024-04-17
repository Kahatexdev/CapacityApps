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
                            Detail Data Area <?= $area ?>
                        </h5>
                        <div>
                            <a href="<?= base_url('capacity/mesinperarea') ?>" class="btn bg-gradient-info"> Kembali</a>
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Mesin</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Brand</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Mesin Jalan</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Mesin Mati</th>
                                        <th colspan=2 class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tampildata as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['area']; ?></td>
                                            <td class="text-sm"><?= $order['jarum']; ?></td>
                                            <td class="text-sm"><?= $order['total_mc']; ?></td>
                                            <td class="text-sm"><?= $order['brand']; ?></td>
                                            <td class="text-sm"><?= $order['mesin_jalan']; ?></td>
                                            <td class="text-sm"><?= $order['total_mc'] - $order['mesin_jalan']; ?></td>
                                            <td class="text-sm">
                                                <button type="button" class="btn btn-success btn-sm edit-btn" data-toggle="modal" data-target="#EditModal" data-id="<?= $order['id_data_mesin']; ?>" data-area="<?= $order['area']; ?>" data-total="<?= $order['total_mc']; ?>" data-jarum="<?= $order['jarum']; ?>" data-mc-jalan="<?= $order['mesin_jalan']; ?>" data-brand="<?= $order['brand']; ?>">
                                                    Edit
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#ModalDelete" data-id="<?= $order['id_data_mesin']; ?>">
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
        <div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
            <div class="modal-dialog   role=" document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Data Machine</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">Area</label>
                                        <input type="text" class="form-control" name="area" readonly>
                                        <input type="hidden" name="id">
                                    </div>
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">Needle</label>
                                        <input type="text" class="form-control" name="jarum">
                                    </div>
                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Brand</label>
                                        <input type="text" name="brand" class="form-control">
                                    </div>

                                </div>
                                <div class="col-lg-6 col-sm-12">

                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Total Machine</label>
                                        <input type="text" name="total_mc" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Mesin Runnig</label>
                                        <input type="text" name="mesin_jalan" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Mesin Not Running</label>
                                        <input type="text" name="mesin_mati" class="form-control">
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

        <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambah" aria-hidden="true">
            <div class="modal-dialog   role=" document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Data Mesin</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">Area</label>
                                        <input type="text" class="form-control" name="area" value=<?= $area ?>>
                                        <input type="hidden" name="id">
                                    </div>
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">Needle</label>
                                        <input type="text" class="form-control" name="jarum">
                                    </div>
                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Brand</label>
                                        <input type="text" name="brand" class="form-control">
                                    </div>

                                </div>
                                <div class="col-lg-6 col-sm-12">

                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Total Machine</label>
                                        <input type="text" name="total_mc" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Machine Running</label>
                                        <input type="text" name="mesin_jalan" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Machine Not Running</label>
                                        <input type="text" name="mesin_mati" class="form-control">
                                    </div>


                                </div>
                            </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Add Data</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade  bd-example-modal-lg" id="ModalDelete" tabindex="-1" role="dialog" aria-labelledby="modaldelete" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Machine in Area <?= $area ?></h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post">
                            <input type="text" name="id_data_mesin" id="" hidden value="">
                            Are you sure you want to delete ?
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
                    $('#modalTambah').find('form').attr('action', '<?= base_url('capacity/tambahmesinperarea/') ?>');

                    $('#modalTambah').modal('show'); // Show the modal
                });

                $('.edit-btn').click(function() {
                    var id_data_mesin = $(this).data('id');
                    var area = $(this).data('area');
                    var jarum = $(this).data('jarum');
                    var total_mc = $(this).data('total');
                    var brand = $(this).data('brand');
                    var mesin_jalan = $(this).data('mc-jalan');
                    var mesin_mati = total_mc - mesin_jalan;

                    $('#ModalEdit').find('form').attr('action', '<?= base_url('capacity/updatemesinperjarum/') ?>' + id_data_mesin);
                    $('#ModalEdit').find('input[name="id"]').val(id_data_mesin);
                    $('#ModalEdit').find('input[name="area"]').val(area);
                    $('#ModalEdit').find('input[name="jarum"]').val(jarum);
                    $('#ModalEdit').find('input[name="total_mc"]').val(total_mc);
                    $('#ModalEdit').find('input[name="brand"]').val(brand);
                    $('#ModalEdit').find('input[name="mesin_jalan"]').val(mesin_jalan);
                    $('#ModalEdit').find('input[name="mesin_mati"]').val(mesin_mati);

                    $('#ModalEdit').modal('show'); // Show the modal
                });
                $('.delete-btn').click(function() {
                    var id = $(this).data('id');
                    $('#ModalDelete').find('form').attr('action', '<?= base_url('capacity/deletemesinareal/') ?>' + id);
                    $('#ModalDelete').find('input[name="id_data_mesin"]').val(id);
                    $('#ModalDelete').modal('show'); // Show the modal
                });

            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>