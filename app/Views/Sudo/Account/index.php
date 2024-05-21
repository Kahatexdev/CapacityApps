<?php $this->extend('Sudo/layout'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">sudo System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data User
                                </h5>
                            </div>
                        </div>
                        <div>

                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalMessage" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md d-inline-flex align-items-center">
                                <i class="fas fa-plus-circle me-2 text-lg opacity-10" style="margin-right: 0.5rem;"></i> <span class="ms-1">Tambah User</span>
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
                            <h5 class="modal-title" id="exampleModalLabel">Input Data User</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= base_url('sudo/addaccount') ?>" method="post">
                                <div class="row">
                                    <div class="col-lg-12 col-sm-12">

                                        <div class="form-group">
                                            <label for="buyer" class="col-form-label">Username:</label>
                                            <input type="text" name="username" id="" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="no_order" class="col-form-label">Password:</label>
                                            <input type="password" name="password" id="" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="productType" class="col-form-label">Role</label>
                                            <select class="form-control" id="role" name="role">
                                                <option>Choose</option>
                                                <?php foreach ($userdata as $ar) : ?>
                                                    <option><?= $ar['role'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Save</button>
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
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Username</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Role</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Area</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Assign</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Action</th>


                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userdata as $user) : ?>
                                <tr>
                                    <td class="text-xs"><?= $user['username'] ?></td>
                                    <td class="text-xs"><?= $user['role'] ?></td>
                                    <td class="text-xs"><?= $user['area_names'] ?></td>
                                    <td class="text-xs"> <?php if ($user['role'] == 'aps') : ?>
                                            <!-- If qty is null, set action to Import -->
                                            <button class="btn bg-gradient-info btn-sm text-xxs assign-button" data-bs-toggle="modal" data-bs-target="#assignArea" data-id="<?= $user['id_user'] ?>">Assign</button>

                                        <?php else : ?>
                                            <!-- If qty is not null, set action to Details -->
                                        <?php endif; ?>
                                    </td>


                                    <td class=" text-xs">

                                        <button class="btn bg-gradient-danger btn-sm text-xxs edit-btn" data-bs-toggle="modal" data-bs-target="#edit-btn" data-id="<?= $user['id_user'] ?> " data-usn="<?= $user['username'] ?>" data-pass="<?= $user['password'] ?>" data-area="<?= $user['area_names'] ?>">Edit</button>
                                        <button class=" btn bg-gradient-danger btn-sm text-xxs delete-btn" data-bs-toggle="modal" data-bs-target="#delete-btn" data-id="<?= $user['id_user'] ?> ">Delete</button>

                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <div class=" modal fade bd-example-modal-lg" id="assignArea" tabindex="-1" role="dialog" aria-labelledby="assignArea" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assign Area</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('sudo/assignarea/') ?>" method="post">
                        <input type="text" name="iduser" id="iduser" hidden value="">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">

                                <div class="form-group">
                                    <label for="area"> Area</label>
                                </div>
                                <?php foreach ($area as $ar) : ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="<?= $ar['id'] ?>" id="fcustomCheck1" name="areaList[]">
                                        <label class="custom-control-label" for="customCheck1"><?= $ar['name'] ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade  bd-example-modal-lg" id="ModalDelete" tabindex="-1" role="dialog" aria-labelledby="modaldelete" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Account</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <input type="hidden" class="form-control" name="no_model">
                        <input type="hidden" class="form-control" name="delivery">
                        <input type="hidden" class="form-control" name="jarum">
                        <input type="text" name="idapsperstyle" id="" hidden value="">
                        Apakah anda yakin ingin menghapus Account?
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

    $('.assign-button').click(function() {
        var id = $(this).data('id');

        $('#assignArea').find('input[name="iduser"]').val(id);

        $('#assignArea').modal('show'); // Show the modal
    });

    $('.delete-btn').click(function() {
        var id = $(this).data('id');
        $('#ModalDelete').find('form').attr('action', '<?= base_url('sudo/deleteaccount/') ?>' + id);

        $('#ModalDelete').modal('show'); // Show the modal
    });
    $('.edit-btn').click(function() {
        var id = $(this).data('id');
        var username = $(this).data('usn');
        var pass = $(this).data('pass');
        var area = $(this).data('area');
        $('#ModalEdit').find('form').attr('action', '<?= base_url('sudo/editaccount/') ?>' + id);

        $('#ModalEdit').modal('show'); // Show the modal
    });
</script>

<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>