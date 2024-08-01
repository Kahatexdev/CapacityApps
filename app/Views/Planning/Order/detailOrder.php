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
                        <div>
                            <button type="button" class="btn bg-gradient-success btn-recomend" data-model=" <?= $noModel ?>" data-delivery=" <?= $delivery ?>" data-toggle="modal" data-target="#ModalRecomend"> Area Recomendation</button>
                            <button type="button" class="btn bg-gradient-warning btn-assign" data-toggle="modal" data-target="#ModalAssign"> Arahkan Ke Areal</button>
                            <a href="<?= base_url($role . '/blmAdaArea/') ?>" class="btn bg-gradient-info"> Kembali</a>
                        </div>
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
                                            <td class="text-sm"><?= ceil($order['qty'] / 24); ?> dz</td>
                                            <td class="text-sm"><?= ceil($order['sisa'] / 24); ?> dz</td>
                                            <td class="text-sm"><?= $order['seam']; ?></td>
                                            <td class="text-sm"><?= $order['factory']; ?></td>
                                            <td class="text-sm">
                                                <button type="button" class="btn btn-warning btn-sm split-btn" data-toggle="modal" data-target="#splitModal" data-id="<?= $order['idapsperstyle']; ?>" data-no-model="<?= $order['mastermodel']; ?>" data-delivery="<?= $order['delivery']; ?>" data-jarum="<?= $order['machinetypeid']; ?>" data-style="<?= $order['size']; ?>" data-qty="<?= $order['qty']; ?>" data-sisa="<?= $order['sisa']; ?>" data-seam="<?= $order['seam']; ?>" data-factory="<?= $order['factory']; ?>" data-smv=" <?= $order['smv']; ?> " data-order=" <?= $order['no_order']; ?> " data-country=" <?= $order['country']; ?> ">
                                                    Split
                                                </button>
                                                <button type=" button" class="btn btn-info btn-sm edit-btn" data-toggle="modal" data-target="#ModalEdit" data-id="<?= $order['idapsperstyle']; ?>" data-area="<?= $order['factory']; ?>" data-pdk="<?= $order['mastermodel']; ?>" data-deliv="<?= $order['delivery']; ?>">
                                                    Edit Area
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class=" card-footer">
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
        <div class="modal fade bd-example-modal-lg" id="splitModal" tabindex="-1" role="dialog" aria-labelledby="splitModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Split Area</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/splitarea') ?>" method="post">
                            <input type="text" name="noModel" id="noModel" hidden value="">
                            <input type="text" name="idaps" id="idaps" hidden value="">
                            <input type="text" name="smv" id="smv" hidden value="">
                            <input type="text" name="delivery" id="delivery" hidden value="">
                            <input type="text" name="seam" id="seam" hidden value="">
                            <input type="text" name="jarum" id="jarum" hidden value="">
                            <input type="text" name="style" id="style" hidden value="">
                            <input type="text" name="order" id="order" hidden value="">
                            <input type="text" name="country" id="country" hidden value="">
                            <div id="confirmationMessage"></div>
                            <div class="form-group">
                                <label for="selectArea">Area 1:</label>
                                <select class="form-control" id="selectArea" name="area1">
                                    <?php
                                    $uniqueAreas = array_unique(array_column($dataMc, 'area'));
                                    foreach ($uniqueAreas as $area) :
                                    ?>
                                        <option value="<?= $area; ?>"><?= $area; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="qty">Qty Area 1:</label>
                                <input type="number" name="qty1" class="form-control" id="">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Area 2:</label>
                                <select class="form-control" id="selectArea" name="area2">
                                    <?php
                                    $uniqueAreas = array_unique(array_column($dataMc, 'area'));
                                    foreach ($uniqueAreas as $area) :
                                    ?>
                                        <option value="<?= $area; ?>"><?= $area; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="qty">Qty Area 2:</label>
                                <input type="number" name="qty2" class="form-control" id="">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Simpan</button>
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
        <div class="modal fade bd-example-modal-lg" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit <Area:d></Area:d>
                        </h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/editarea') ?>" method="post">
                            <input type="text" name="id" id="" hidden value="">
                            <input type="text" name="pdk" id="" hidden value="">
                            <input type="text" name="deliv" id="" hidden value="">
                            <div id="confirmationMessage"></div>
                            <div class="form-group">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Pilih Area:</label>
                                <select class="form-control" id="selectArea" name="area">
                                    <option value="">-- Choose --</option>

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
        <div class="modal fade bd-example-modal-lg" id="recomendModal" tabindex="-1" role="dialog" aria-labelledby="recomendModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Recomendation Area <Area:d></Area:d>
                        </h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/recomendationarea') ?>" method="post">
                            <input type="text" name="pdk" id="" hidden value="<?= $noModel ?>">
                            <input type="text" name="deliv" id="" hidden value="">

                            <div class="form-group">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Estimated Start Machine:</label>
                                <input type="date" class="form-control" name="start">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Estimated Stop Machine:</label>
                                <input type="date" class="form-control" name="stop">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Sumbit</button>
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
                $('.split-btn').click(function() {
                    var noModel = $(this).data('no-model');
                    var idAps = $(this).data('id');
                    var style = $(this).data('style');
                    var smv = $(this).data('smv');
                    var delivery = $(this).data('delivery');
                    var seam = $(this).data('seam');
                    var jarum = $(this).data('jarum');
                    var order = $(this).data('order');
                    var country = $(this).data('country');


                    $('#splitModal').modal('show'); // Show the modal
                    $('#splitModal').find('input[name="noModel"]').val(noModel);
                    $('#splitModal').find('input[name="idaps"]').val(idAps);
                    $('#splitModal').find('input[name="style"]').val(style);
                    $('#splitModal').find('input[name="delivery"]').val(delivery);
                    $('#splitModal').find('input[name="smv"]').val(smv);
                    $('#splitModal').find('input[name="seam"]').val(seam);
                    $('#splitModal').find('input[name="jarum"]').val(jarum);
                    $('#splitModal').find('input[name="order"]').val(order);
                    $('#splitModal').find('input[name="country"]').val(country);

                    document.getElementById('confirmationMessage').innerHTML = "Apakah anda yakin memecah" + noModel + " dengan jarum " + selectedMachineTypeId + " ke " + selectedArea;
                });
                $('.edit-btn').click(function() {
                    var idAps = $(this).data('id');
                    var area = $(this).data('area');
                    var pdk = $(this).data('pdk');
                    var deliv = $(this).data('deliv');
                    $('#editModal').modal('show'); // Show the modal
                    $('#editModal').find('input[name="area"]').val(area);
                    $('#editModal').find('input[name="id"]').val(idAps);
                    $('#editModal').find('input[name="pdk"]').val(pdk);
                    $('#editModal').find('input[name="deliv"]').val(deliv);
                });
                $('.btn-recomend').click(function() {

                    var pdk = $(this).data('model');
                    var deliv = $(this).data('delivery');
                    $('#recomendModal').modal('show'); // Show the modal


                    $('#recomendModal').find('input[name="deliv"]').val(deliv);
                });
            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>