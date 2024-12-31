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
                    <div class="row justify-content-between align-items-center mb-3">
                        <div class="col-auto">
                            <h5>
                                Detail Data Model <?= esc($noModel) ?> Jarum <?= esc($jarum) ?>
                            </h5>
                        </div>
                        <div class="col-auto">

                            <a href="<?= base_url($role . '/dataorder/') ?>" class="btn bg-gradient-info">Kembali</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <h6>
                                Qty Order Perdelivery
                            </h6>
                            <?php foreach ($headerRow as $val): ?>
                                <li><?= date('d M Y', strtotime($val['delivery']))  ?> : <?= round($val['qty'] / 24) ?> dz</li>
                            <?php endforeach ?>
                            <p>---------------------------------------------</p>

                            Total Order : <?= round($totalPo['totalPo'] / 24) ?> dz

                        </div>

                        <div class="col-md-4">
                            <h6>Summarize <?= $noModel ?> <?= $jarum ?></h6>
                            <li>Kebutuhan Mesin : <?= $kebMesin ?> Machine</li>
                            <li>Duration : <?= $hari ?> days </li>
                            <li>Target Perhari : <?= $target ?> dz/day</li>

                        </div>

                    </div>

                </div>

                <div class="card-body">
                    <?php foreach ($order as $deliv => $val): ?>
                        <div class="row mt-3">
                            <div class="d-flex justify-content-between align-item-center">
                                <h5> <span class='badge  badge-pill badge-lg bg-gradient-info'>Detail Order Delivery <?= date('d M Y', strtotime($deliv)) ?> </span></h5>
                                <h5> <span class='badge  badge-pill badge-lg bg-gradient-info'>Qty Order <?= round($val['totalQty'] / 24) ?> dz</span></h5>

                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Size</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Inisial</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Qty</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Sisa</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Area</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Factory</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($val as $key => $list): ?>
                                            <?php if (is_array($list)): // Pastikan $list adalah array 
                                            ?>
                                                <tr>
                                                    <td> <?= $list['size'] ?></td>
                                                    <td><?= $list['inisial'] ?></td>
                                                    <td><?= round($list['qty'] / 24) ?> dz</td>
                                                    <td><?= round($list['sisa'] / 24) ?> dz</td>
                                                    <td><?= $list['factory'] ?></td>
                                                    <td><?= $list['production_unit'] ?></td>x

                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <hr>
                            </div>
                        </div>
                    <?php endforeach ?>
                    <div class=" card-footer">
                        <div>
                            <br>

                        </div>
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
                        <form action="<?= base_url($role . '/inputinisial') ?>" method="post">

                            <div id="confirmationMessage"></div>
                            <input type="text" class="form-control" name="jarum" id="" value="" hidden>

                            <div class="form-group">
                                <label for="pdk" class="label">PDK</label>
                                <input type="text" class="form-control" name="pdk" id="" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="size" class="label">Style Size</label>
                                <input type="text" name="size" class="form-control" id="" value="" readonly>

                            </div>
                            <div class="form-group">
                                <label for="inisial" class="label">Inisial</label>
                                <input type="text" name="inisial" class="form-control" id="" value="" placeholder="Masukan Inisial">
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

                $('.edit-btn').click(function() {
                    var idAps = $(this).data('id');
                    var area = $(this).data('area');
                    var pdk = $(this).data('pdk');
                    var deliv = $(this).data('deliv');
                    var size = $(this).data('size');
                    var jarum = $(this).data('jarum');
                    $('#editModal').modal('show'); // Show the modal
                    $('#editModal').find('input[name="area"]').val(area);
                    $('#editModal').find('input[name="id"]').val(idAps);
                    $('#editModal').find('input[name="pdk"]').val(pdk);
                    $('#editModal').find('input[name="deliv"]').val(deliv);
                    $('#editModal').find('input[name="size"]').val(size);
                    $('#editModal').find('input[name="jarum"]').val(jarum);
                });



            });
            document.getElementById('selectAll').addEventListener('click', function(e) {
                var checkboxes = document.querySelectorAll('.delivery-checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = e.target.checked;
                });
            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>