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
                            Detail Data Area <?= $area ?>
                        </h5>
                        <div>
                            <button type="button" class="btn btn-add bg-gradient-info d-inline-flex align-items-center" data-toggle="modal" data-target="#modalTambah">
                                <i class="fas fa-plus-circle me-2 text-lg opacity-10"></i>
                                Input Data Machine
                            </button>
                           

                            <a href="<?= base_url($role . '/mesinperarea/' . $pu) ?>" class="btn bg-gradient-dark">
                                <i class="fas fa-arrow-circle-left me-2 text-lg opacity-10"></i>
                                Back</a>
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Machine</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Brand</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Machine Running</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Machine Not Running</th>
                                        <th colspan=3 class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tampildata as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['area']; ?></td>
                                            <td class="text-sm"><?= $order['jarum']; ?></td>
                                            <td class="text-sm"><?= $order['total_mc']; ?> Mc</td>
                                            <td class="text-sm"><?= $order['brand']; ?></td>
                                            <td class="text-sm"><?= $order['total_mc'] - $order['mesin_jalan']; ?> Mc</td>
                                            <td class="text-sm"><?= $order['mesin_jalan']; ?> Mc</td>
                                             <td class="text-sm">
                                             
                                                   
                                                <button type="button" class="btn btn-capacity btn-info btn-sm ?>" data-jarum="<?= $order['jarum']; ?>">
                                                    Cek Kapasitas
                                                </button>
                                           
                                            </td>
                                            <td class="text-sm">
                                                <button type="button" class="btn btn-success btn-sm edit-btn" data-toggle="modal" data-target="#EditModal" data-id="<?= $order['id_data_mesin']; ?>" data-area="<?= $order['area']; ?>" data-total="<?= $order['total_mc']; ?>" data-jarum="<?= $order['jarum']; ?>" data-mc-jalan="<?= $order['mesin_jalan']; ?>" data-brand="<?= $order['brand']; ?>" data-pu="<?= $order['pu']; ?>">
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
                                <tfoot>
                                    <th></th>
                                    <th>Total :</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tfoot>
                            </table>

                        </div>
                    </div>

                    <div class="card-footer">

                    </div>

                </div>


            </div>
        </div>
        <div class="row">
        <?= $this->renderSection('capacity'); ?>
        </div>
        <div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
            <div class="modal-dialog"   role="document">
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
                                    <div class="form-group">
                                        <label class="col-form-label">Production Unit</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="production_unit" id="cj_radio" value="CJ">
                                            <label class="form-check-label" for="cj_radio">CJ</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="production_unit" id="mj_radio" value="MJ">
                                            <label class="form-check-label" for="mj_radio">MJ</label>
                                        </div>
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
                                    <div class="form-group">
                                        <label class="col-form-label">Production Unit</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="production_unit" id="cj_radio" value="CJ">
                                            <label class="form-check-label" for="cj_radio">CJ</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="production_unit" id="mj_radio" value="MJ">
                                            <label class="form-check-label" for="mj_radio">MJ</label>
                                        </div>
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
        <div class="modal fade  bd-example-modal-lg" id="ModalCapacity" tabindex="-1" role="dialog" aria-labelledby="modalCapacity" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Input Target</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post">
                       <input type="text" name="jarum" id="jarum" class="form-control">
                       <input type="number" name="target" id="target" class="form-control" placeholder="Masukan target">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Pilih</button>
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
                $('#dataTable').DataTable({
                    "footerCallback": function(row, data, start, end, display) {
                        var api = this.api();

                        var totalMesin = api.column(2, {
                            page: 'current'
                        }).data().reduce(function(a, b) {
                            return parseInt(a) + parseInt(b);
                        }, 0);

                        // Calculate the total of the 5th column (Remaining Qty in dozens) - index 4
                        var mesinJalan = api.column(4, {
                            page: 'current'
                        }).data().reduce(function(a, b) {
                            return parseInt(a) + parseInt(b);
                        }, 0);

                        var mesinMati = totalMesin - mesinJalan;

                        // Format totalMesin and mesinJalan with " Mc" suffix and dots for thousands
                        var totalMesinFormatted = numberWithDots(totalMesin) + " Mc";
                        var mesinJalanFormatted = numberWithDots(mesinJalan) + " Mc";
                        var mesinMatiFormatted = numberWithDots(mesinMati) + " Mc";

                        // Update the footer cell for the total Qty
                        $(api.column(2).footer()).html(totalMesinFormatted);

                        // Update the footer cell for the total Mesin Jalan
                        $(api.column(4).footer()).html(mesinJalanFormatted);

                        // Update the footer cell for the percentage
                        $(api.column(5).footer()).html(mesinMatiFormatted);
                    },
                });

                function numberWithDots(x) {
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }

                $('.btn-add').click(function() {
                    $('#modalTambah').find('form').attr('action', '<?= base_url($role . '/tambahmesinperarea/') ?>');

                    $('#modalTambah').modal('show'); // Show the modal
                });

                $('.edit-btn').click(function() {
                    var id_data_mesin = $(this).data('id');
                    var area = $(this).data('area');
                    var jarum = $(this).data('jarum');
                    var total_mc = $(this).data('total');
                    var brand = $(this).data('brand');
                    var mesin_jalan = $(this).data('mc-jalan');
                    var pu = $(this).data('pu');
                    var mesin_mati = total_mc - mesin_jalan;

                    $('#ModalEdit').find('form').attr('action', '<?= base_url($role . '/updatemesinperjarum/') ?>' + id_data_mesin);
                    $('#ModalEdit').find('input[name="id"]').val(id_data_mesin);
                    $('#ModalEdit').find('input[name="area"]').val(area);
                    $('#ModalEdit').find('input[name="jarum"]').val(jarum);
                    $('#ModalEdit').find('input[name="total_mc"]').val(total_mc);
                    $('#ModalEdit').find('input[name="brand"]').val(brand);
                    $('#ModalEdit').find('input[name="mesin_jalan"]').val(mesin_jalan);
                    $('#ModalEdit').find('input[name="mesin_mati"]').val(mesin_mati);
                    if (pu === "CJ") {
                        $('#cj_radio').prop('checked', true);
                    } else if (pu === "MJ") {
                        $('#mj_radio').prop('checked', true);
                    }

                    $('#ModalEdit').modal('show'); // Show the modal
                });
                $('.delete-btn').click(function() {
                    var id = $(this).data('id');
                    $('#ModalDelete').find('form').attr('action', '<?= base_url($role . '/deletemesinareal/') ?>' + id);
                    $('#ModalDelete').find('input[name="id_data_mesin"]').val(id);
                    $('#ModalDelete').modal('show'); // Show the modal
                });
                $('.btn-capacity').click(function() {
                    var area = $(this).data('area');
                     var jarum = $(this).data('jarum');
                    $('#ModalCapacity').find('form').attr('action', '<?= base_url($role . '/capacityperarea/') ?>' + area);         
                    $('#ModalCapacity').find('input[name="jarum"]').val(jarum);

                    $('#ModalCapacity').modal('show'); // Show the modal
                });

            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>