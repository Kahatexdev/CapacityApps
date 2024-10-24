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

                            <a href="#" class="btn btn-danger btn-delete-all" Data-bs-toggle="modal" data-bs-target="ModalDeleteAll" data-no-model="<?= $noModel ?>">Delete All</a>
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
                            <li>Machine Requirments : <?= $kebMesin ?> Machine</li>
                            <li>Duration : <?= $hari ?> days </li>
                            <li>Daily Target : <?= $target ?> dz/day</li>

                        </div>
                        <div class="col-md-4">
                            <h6>Recomended Area</h6>
                            <ol>
                                <?php foreach ($rekomendasi as $val): ?>

                                    <li><?= strtoupper($val['area']) ?></li>
                                    <ul>
                                        <li>
                                            Maximum Capacity : <?= $val['max'] ?> dz/day
                                        </li>
                                        <li>
                                            Used : <?= $val['used'] ?> dz
                                        </li>
                                        <li>

                                            Availabel Capacity :
                                            <?php if (is_string($val['avail'])): ?> <span class="badge bg-warning"><?= $val['avail'] ?> dz</span>
                                            <?php else: ?> <span class="badge bg-info"> <?= $val['avail'] ?> dz</span>
                                            <?php endif ?>
                                        </li>
                                    </ul>

                                <?php endforeach ?>
                            </ol>

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
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Qty</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Sisa</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">SMV</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Area</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Factory</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($val as $key => $list): ?>
                                            <?php if (is_array($list)): // Pastikan $list adalah array 
                                            ?>
                                                <tr>
                                                    <td> <?= $list['size'] ?></td>
                                                    <td><?= round($list['qty'] / 24) ?> dz</td>
                                                    <td><?= round($list['sisa'] / 24) ?> dz</td>
                                                    <td><?= $list['smv'] ?></td>
                                                    <td><?= $list['factory'] ?></td>
                                                    <td><?= $list['production_unit'] ?></td>x
                                                    <td>

                                                        <button type="button" class="btn btn-success btn-sm edit-btn" data-toggle="modal" data-target="#editModal" data-id="<?= $list['idapsperstyle']; ?>" data-no-model="<?= $list['mastermodel']; ?>" data-delivery="<?= $list['delivery']; ?>" data-jarum="<?= $jarum; ?>" data-style="<?= $list['size']; ?>" data-qty="<?= $list['qty']; ?>" data-sisa="<?= $list['sisa']; ?>" data-factory="<?= $list['factory']; ?>" data-production_unit="<?= $list['production_unit']; ?>">
                                                            Edit
                                                        </button>
                                                    </td>
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
                            <input type="text" name="no_model" id="" hidden value="<?= $noModel ?>">
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
                            <input class="form-check-input" type="text" value="<?= $jarum ?>" name="jarum" id="jarum">

                            <div class="form-group">
                                <label for="selectMachineType">Pilih Delivery:</label>
                                <?php foreach ($headerRow as $row): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="<?= $row['delivery'] ?>" name="delivery[]" id="ceklist">
                                        <label class="custom-control-label" for="customCheck1"> <?= date('d-F-y', strtotime($row['delivery'])) ?></label>
                                    </div>
                                <?php endforeach ?>
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
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="" class="col-form-label">ID</label>
                                        <input type="text" class="form-control" name="idapsperstyle" readonly>
                                        <input type="text" class="form-control" name="jarum" value="<?= $jarum ?>" hidden>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">No Model</label>
                                        <input type="text" class="form-control" name="no_model" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Style</label>
                                        <input type="text" class="form-control" name="style">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Delivery</label>
                                        <input type="date" name="delivery" id="" class="form-control">
                                    </div>
                                    <div class=" form-group">
                                        <label for="" class="col-form-label">Quantity</label>
                                        <input type="number" name="qty" id="" class="form-control">
                                    </div>
                                    <div class=" form-group">
                                        <label for="" class="col-form-label">Sisa</label>
                                        <input type="text" name="sisa" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Seam</label>
                                        <input type="text" name="seam" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">SMV</label>
                                        <input type="text" name="smv" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Production Unit</label>
                                        <input type="text" name="production_unit" id="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-form-label">Areal</label>
                                        <input type="text" name="factory" id="" class="form-control">
                                    </div>

                                </div>
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
                            <input type="text" name="pdkRecomen" id="" hidden value="<?= $noModel ?>">
                            <input type="text" name="delivRecomen" id="" hidden value="">

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
                            <div id="recommendationResult" class="mt-3"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn bg-gradient-info rekomenBtn" onclick="areaRecomendation()">Sumbit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script>
            function areaRecomendation() {
                // Ambil nilai dari form di dalam modal
                var pdk = $('input[name="pdkRecomen"]').val();
                var start = $('input[name="start"]').val();
                var stop = $('input[name="stop"]').val();
                var deliv = $('input[name="delivRecomen"]').val(); // Tambahkan pengambilan delivery date
                var data = {
                    pdk: pdk,
                    start: start,
                    stop: stop,
                    deliv: deliv
                };
                console.log(data)
                $.ajax({
                    url: '<?= base_url($role . "/recomendationarea") ?>', // URL ke controller function
                    type: 'POST',
                    data: {
                        pdk: pdk,
                        start: start,
                        stop: stop,
                        deliv: deliv
                    },
                    beforeSend: function() {
                        // Opsional: tampilkan loading spinner atau ubah teks tombol
                        $('.rekomenBtn').text('Loading...');
                    },
                    success: function(response) {
                        console.log(data);
                        if (response.status === 'success') {

                            // Jika sukses, tampilkan rekomendasi di dalam elemen #recommendationResult
                            var resultHtml = '';
                            $.each(response.rekomendasi_area, function(jarum, rekomendasi) {
                                resultHtml += '<h5>Jarum: ' + jarum + '</h5>';
                                resultHtml += '<ul>';
                                $.each(rekomendasi, function(index, area) {
                                    resultHtml += '<li>Factory: ' + area.factory + '<br>';
                                    resultHtml += 'Kebutuhan Kapasitas Perhari: ' + area['Kebutuhan Kapasitas Perhari'] + ' dz' + '<br>';
                                    resultHtml += 'Sisa Kapasitas: ' + area.sisa_kapasitas + ' dz' + '<br>';
                                    resultHtml += 'Selisih: ' + area.difference + ' dz' + '</li>';
                                });
                                resultHtml += '</ul>';
                            });
                            $('#recommendationResult').html(resultHtml);
                        } else {
                            // Jika tidak ada rekomendasi atau terjadi error
                            $('#recommendationResult').html('<p>No recommendation available.</p>');
                        }
                        // Kembalikan teks tombol ke "Submit"
                        $('.btn.bg-gradient-info').text('Submit');
                    },
                    error: function(xhr, status, error) {
                        // Tangani error
                        console.error('Error: ' + error);
                        $('#recommendationResult').html('<p>An error occurred. Please try again later.</p>');
                        $('.btn.bg-gradient-info').text('Submit');
                    }
                });
            }


            $(document).ready(function() {
                $('#dataTable').DataTable();
                $('.btn-assign').click(function() {
                    var noModel = $(this).data('no-model');
                    $('#ModalAssign').modal('show'); // Show the modal
                    var selectedMachineTypeId = document.getElementById("jarum").value;
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
                    var apsperstyle = $(this).data('id');
                    var noModel = $(this).data('no-model');
                    var delivery = $(this).data('delivery');
                    var jarum = $(this).data('jarum');
                    var style = $(this).data('style');
                    var qty = $(this).data('qty');
                    var sisa = $(this).data('sisa');
                    var seam = $(this).data('seam');
                    var smv = $(this).data('smv');
                    var production_unit = $(this).data('production_unit');
                    var factory = $(this).data('factory');

                    var formattedDelivery = new Date(delivery).toISOString().split('T')[0];
                    $('#editModal').find('form').attr('action', '<?= base_url($role . '/updatedetailorder/') ?>' + apsperstyle);
                    $('#editModal').find('input[name="idapsperstyle"]').val(apsperstyle);
                    $('#editModal').find('input[name="style"]').val(style);
                    $('#editModal').find('input[name="no_model"]').val(noModel);
                    $('#editModal').find('input[name="delivery"]').val(formattedDelivery);
                    $('#editModal').find('input[name="qty"]').val(qty);
                    $('#editModal').find('input[name="sisa"]').val(sisa);
                    $('#editModal').find('input[name="seam"]').val(seam);
                    $('#editModal').find('input[name="smv"]').val(smv);
                    $('#editModal').find('input[name="production_unit"]').val(production_unit);
                    $('#editModal').find('input[name="factory"]').val(factory);

                    $('#editModal').modal('show'); // Show the modal
                });
                $('.btn-recomend').click(function() {

                    var pdk = $(this).data('model');
                    var deliv = $(this).data('delivery');
                    $('#recomendModal').modal('show'); // Show the modal


                    $('#recomendModal').find('input[name="deliv"]').val(deliv);
                });
                $('.btn-delete-all').click(function() {
                    var noModel = $(this).data('no-model');
                    $('#ModalDeleteAll').find('form').attr('action', '<?= base_url($role . '/deletedetailorder/') ?>' + noModel);
                    $('#ModalDeleteAll').find('input[name="idapsperstyle"]').val(noModel);
                    $('#ModalDeleteAll').modal('show'); // Show the modal
                });


            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>