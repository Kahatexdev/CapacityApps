<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
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
                    </div>
                    <div>
                        <a href="<?= base_url($role . '/semuaOrder/') ?>" class="btn bg-gradient-info"> Kembali</a>
                        <button type="button" class="btn bg-gradient-warning btn-assign" data-toggle="modal" data-target="#ModalAssign"> Arahkan Ke Areal</button>
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
                                        <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
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
                                                <button type="button" class="btn btn-warning btn-sm import-btn" data-toggle="modal" data-target="#EditModal" data-id="<?= $order['idapsperstyle']; ?>" data-no-model="<?= $order['mastermodel']; ?>" data-delivery="<?= $order['delivery']; ?>" data-jarum="<?= $order['machinetypeid']; ?>" data-style="<?= $order['size']; ?>" data-qty="<?= $order['qty']; ?>" data-sisa="<?= $order['sisa']; ?>" data-seam="<?= $order['seam']; ?>" data-factory="<?= $order['factory']; ?>">
                                                    Edit
                                                </button>
                                            </td>
                                            <!-- <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#ModalEdit" data-id="<?= $order['idapsperstyle']; ?>"data-no-model="<?= $order['mastermodel']; ?>" data-delivery="<?= $order['delivery']; ?>">
                                                    Delete
                                                </button>
                                            </td> -->
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card-footer">
                        <div>
                            <br>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success btn-sm btn-assignall" data-toggle="modal" data-target="#ModalAssignAll" data-id="<?= $order['idapsperstyle']; ?>" data-no-model="<?= $order['mastermodel']; ?>" data-delivery="<?= $order['delivery']; ?>">
                                    Arahkan Seluruh Model ke
                                </button>
                            </div>
                        </div>
                    </div>



                </div>


            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" id="ModalAssign" tabindex="-1" role="dialog" aria-labelledby="ModalAssign" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Arahkan PDK</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/assignareal') ?>" method="post">
                            <input type="text" name="no_model" id="" hidden value="<?= $noModel ?>">
                            <div id="confirmationMessage"></div>
                            <div class="form-group">
                                <label for="selectMachineType">Pilih Tipe Mesin:</label>
                                <select class="form-control" id="selectMachineType" name="jarum">
                                    <?php
                                    $uniqueMachineTypeIds = array_unique(array_column($dataAps, 'machinetypeid'));
                                    foreach ($uniqueMachineTypeIds as $machinetypeid) :
                                    ?>
                                        <option value="<?= $machinetypeid; ?>"><?= $machinetypeid; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Pilih Area:</label>
                                <select class="form-control" id="selectArea" name="area">
                                    <?php
                                    $uniqueAreas = array_unique(array_column($dataMc, 'area'));
                                    foreach ($uniqueAreas as $area) :
                                    ?>
                                        <option value="<?= $area; ?>"><?= $area; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Ya</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" id="ModalAssignAll" tabindex="-1" role="dialog" aria-labelledby="ModalAssignAll" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Arahkan Model <?= $noModel ?> dan seluruh Delivery ke Areal</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/assignarealall') ?>" method="post">
                            <input type="text" name="no_model" id="" hidden value="<?= $noModel ?>">
                            <div id="confirmationMessage"></div>
                            <div class="form-group">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Pilih Area:</label>
                                <select class="form-control" id="selectArea" name="area">
                                    <?php
                                    $uniqueAreas = array_unique(array_column($dataMc, 'area'));
                                    foreach ($uniqueAreas as $area) :
                                    ?>
                                        <option value="<?= $area; ?>"><?= $area; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Ya</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable();
                $('.btn-assign').click(function() {
                    var noModel = $(this).data('no-model');
                    $('#ModalAssign').modal('show'); // Show the modal
                    var selectedMachineTypeId = document.getElementById("machinetypeid").value;
                    var selectedArea = document.getElementById("area").value;
                    document.getElementById('confirmationMessage').innerHTML = "Apakah anda yakin mengarahkan PDK " + noModel + " dengan jarum " + selectedMachineTypeId + " ke " + selectedArea;
                });
                $('.btn-assignall').click(function() {
                    var noModel = $(this).data('no-model');
                    $('#ModalAssignAll').modal('show'); // Show the modal
                    var selectedMachineTypeId = document.getElementById("machinetypeid").value;
                    var selectedArea = document.getElementById("area").value;
                    document.getElementById('confirmationMessage').innerHTML = "Apakah anda yakin mengarahkan PDK " + noModel + " dengan jarum " + selectedMachineTypeId + " ke " + selectedArea;
                });
            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>