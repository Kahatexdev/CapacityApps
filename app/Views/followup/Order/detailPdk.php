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
                            <!-- <button type="button" class="btn bg-gradient-warning btn-assign" data-toggle="modal" data-target="#ModalAssign">
                                Arahkan Ke Areal/dlv
                            </button>
                            <button type="button" class="btn bg-gradient-warning btn-assignall" data-toggle="modal" data-target="#ModalAssignAll">
                                Arahkan Semua
                            </button>
                            <button type="button" class="btn bg-gradient-success" data-bs-toggle="modal" data-bs-target="#ModalInputHistory">Input rev</button> -->

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
                    <div class="row">
                        <div class="col-lg-6">
                            <h6>Repeat Dari</h6>
                            <form action="<?= base_url($role . '/saveRepeat') ?>" method="post">
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control" name="repeat" id="repeatPdk" placeholder="Tulis disini jika ini PDK Repeat" value="<?= $repeat ?>">
                                    <input type="hidden" name="model" id="" value="<?= $noModel ?>">
                                    <button type="submit" class="btn btn-info d-none" id="repeatBtn">Simpan</button>
                                </div>
                            </form>
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
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Inisial</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Size</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Qty</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Sisa</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">SMV</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Area</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Factory</th>
                                            <!-- <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($val as $key => $list): ?>
                                            <?php if (is_array($list)): // Pastikan $list adalah array 
                                            ?>
                                                <tr>
                                                    <td> <?= $list['inisial'] ?></td>
                                                    <td> <?= $list['size'] ?></td>
                                                    <td><?= round($list['qty'] / 24) ?> dz</td>
                                                    <td><?= round($list['sisa'] / 24) ?> dz</td>
                                                    <td><?= $list['smv'] ?></td>
                                                    <td><?= $list['factory'] ?></td>
                                                    <td><?= $list['production_unit'] ?></td>x
                                                    <!-- <td>
                                                        <button type="button" class="btn btn-warning btn-sm split-btn" data-toggle="modal" data-target="#splitModal" data-id="<?= $list['idapsperstyle']; ?>" data-no-model="<?= $list['mastermodel']; ?>" data-delivery="<?= $list['delivery']; ?>" data-jarum="<?= $jarum ?>" data-style="<?= $list['size']; ?>" data-qty="<?= $list['qty']; ?>" data-sisa="<?= $list['sisa']; ?>" data-seam="<?= $list['seam']; ?>" data-factory="<?= $list['factory']; ?>" data-smv=" <?= $list['smv']; ?> " data-order=" <?= $list['no_order']; ?> " data-country=" <?= $list['country']; ?> ">
                                                            Split
                                                        </button>
                                                        <button type=" button" class="btn btn-info btn-sm edit-btn" data-toggle="modal" data-target="#ModalEdit" data-id="<?= $list['idapsperstyle']; ?>" data-area="<?= $list['factory']; ?>" data-pdk="<?= $list['mastermodel']; ?>" data-deliv="<?= $list['delivery']; ?> " data-size="<?= $list['size']; ?>" data-jarum="<?= $jarum ?>">
                                                            Edit Area
                                                        </button>
                                                        <button type=" button" class="btn btn-success btn-sm edit-qty-btn" data-toggle="modal" data-target="#ModalEdit" data-id="<?= $list['idapsperstyle']; ?>" data-area="<?= $list['factory']; ?>" data-pdk="<?= $list['mastermodel']; ?>" data-deliv="<?= $list['delivery']; ?> " data-size="<?= $list['size']; ?>" data-jarum="<?= $jarum ?>">
                                                            Edit Qty
                                                        </button>
                                                    </td> -->
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

                <div class="card-body">
                    <div class="row mt-3">
                        <div class="d-flex justify-content-between align-item-center">
                            <h5> <span class='badge  badge-pill badge-lg bg-gradient-info'>History Revisi</span></h5>

                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Tanggal Revisi</th>
                                        <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historyRev as $key): ?>
                                        <tr>
                                            <td><?= $key['tanggal_rev'] ?></td>
                                            <td><?= $key['keterangan'] ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <hr>
                        </div>
                    </div>
                    <div class=" card-footer">
                        <div>
                            <br>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal fade  bd-example-modal-lg" id="ModalInputHistory" tabindex="-1" role="dialog" aria-labelledby="ModalInputHistory" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">History Revisi Order <?= $noModel ?></h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="<?= base_url($role . '/inputhistoryrevise/' . $noModel) ?>" method="post">
                        <div class="modal-body">
                            <input type="hidden" name="no_model" value="<?= $noModel ?>">
                            <input type="hidden" name="jarum" value="<?= $jarum ?>">
                            <label for="tanggal_rev">Tanggal Revisi:</label>
                            <input type="datetime-local" name="tanggal_rev" id="tanggal_rev" class="form-control" style="margin-bottom: 10px;" required>
                            <label for="keterangan">Keterangan:</label>
                            <input type="text" name="keterangan" id="keterangan" class="form-control" style="margin-bottom: 10px;" required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-success">Simpan</button>
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
                            <input type="text" name="no_model" hidden value="<?= $noModel ?>">
                            <div id="confirmationMessage"></div>
                            <input class="form-check-input" type="text" value="<?= $jarum ?>" name="jarum" id="jarum">

                            <div class="form-group">
                                <label for="selectMachineType">Pilih Delivery:</label>

                                <!-- Checkbox untuk Select All -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" />
                                    <label class="custom-control-label" for="selectAll">Select All</label>
                                </div>

                                <?php foreach ($headerRow as $row): ?>
                                    <div class="form-check">
                                        <input class="form-check-input delivery-checkbox" type="checkbox" value="<?= $row['delivery'] ?>" name="delivery[]" id="ceklist">
                                        <label class="custom-control-label" for="customCheck1"> <?= date('d-F-y', strtotime($row['delivery'])) ?></label>
                                    </div>
                                <?php endforeach ?>
                            </div>

                            <div class="form-group">
                                <label for="selectArea">Pilih Area:</label>
                                <select class="form-control" id="selectArea" name="area">
                                    <?php
                                    foreach ($dataMc as $area) :
                                    ?>
                                        <option value="<?= $area; ?>"> <?= $area; ?></option>
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
                                    <option value=""> Pilih Area</option>
                                    <?php
                                    foreach ($dataMc as $area) :
                                    ?>
                                        <option value="<?= $area; ?>"> <?= $area; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="qty">Qty Area 1:</label>
                                <input type="number" name="qty1" class="form-control" id="" placeholder="pcs">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Area 2:</label>
                                <select class="form-control" id="selectArea" name="area2">
                                    <option value=""> Pilih Area</option>

                                    <?php
                                    foreach ($dataMc as $area) :
                                    ?>
                                        <option value="<?= $area; ?>"> <?= $area; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="qty">Qty Area 2:</label>
                                <input type="number" name="qty2" class="form-control" id="" placeholder="pcs">
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
                            <input type="text" name="jarum" id="" hidden value="<?= $jarum ?>">
                            <div id="confirmationMessage"></div>
                            <div class="form-group">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Pilih Area:</label>
                                <select class="form-control" id="selectArea" name="area">
                                    <option value="">Pilih Area</option>
                                    <?php
                                    foreach ($dataMc as $area) :
                                    ?>
                                        <option value="<?= $area; ?>"> <?= $area; ?></option>
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
                            <input type="text" name="size" id="" hidden value="">
                            <input type="text" name="jarum" id="" hidden value="">
                            <div id="confirmationMessage"></div>
                            <div class="form-group">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Pilih Area:</label>
                                <select class="form-control" id="selectArea" name="area">
                                    <option value="">-- Choose --</option>

                                    <?php

                                    foreach ($dataMc as $area) :
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
        <div class="modal fade bd-example-modal-lg" id="editQtyModal" tabindex="-1" role="dialog" aria-labelledby="editQtyModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Qty<Area:d></Area:d>
                        </h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/editqtyarea') ?>" method="post">
                            <input type="text" name="id" id="" hidden value="">

                            <input type="text" name="pdk" id="" hidden class="form-control" value="">
                            <input type="text" name="deliv" id="" hidden class="form-control" value="">
                            <div class="form-group">
                                <label for="selectArea">Style size:</label>
                                <input type="text" name="size" id="" readonly class="form-control" value="">
                            </div>
                            <input type="text" name="jarum" id="" hidden class="form-control" value="">
                            <div class="form-group">
                                <label for="selectArea">Area:</label>
                                <input type="text" name="area" id="" readonly class="form-control" value="">
                            </div>
                            <div id="confirmationMessage"></div>
                            <div class="form-group">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Input Qty:</label>
                                <input type="number" name="qty" id="" class="form-control" placeholder="masukan qty pcs">
                            </div>
                            <div class="form-group">
                                <label for="selectArea">Input sisa:</label>
                                <input type="number" name="sisa" id="" class="form-control" placeholder="masukan qty pcs">
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
                $('.edit-qty-btn').click(function() {
                    var idAps = $(this).data('id');
                    var area = $(this).data('area');
                    var pdk = $(this).data('pdk');
                    var deliv = $(this).data('deliv');
                    var size = $(this).data('size');
                    var jarum = $(this).data('jarum');
                    $('#editQtyModal').modal('show'); // Show the modal
                    $('#editQtyModal').find('input[name="area"]').val(area);
                    $('#editQtyModal').find('input[name="id"]').val(idAps);
                    $('#editQtyModal').find('input[name="pdk"]').val(pdk);
                    $('#editQtyModal').find('input[name="deliv"]').val(deliv);
                    $('#editQtyModal').find('input[name="size"]').val(size);
                    $('#editQtyModal').find('input[name="jarum"]').val(jarum);
                    $('#editQtyModal').find('input[name="area"]').val(area);
                });
                $('.btn-recomend').click(function() {

                    var pdk = $(this).data('model');
                    var deliv = $(this).data('delivery');
                    $('#recomendModal').modal('show'); // Show the modal


                    $('#recomendModal').find('input[name="deliv"]').val(deliv);
                });


            });
            document.getElementById('selectAll').addEventListener('click', function(e) {
                var checkboxes = document.querySelectorAll('.delivery-checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = e.target.checked;
                });
            });

            const repeatInput = document.getElementById('repeatPdk');
            const repeatBtn = document.getElementById('repeatBtn');

            repeatInput.addEventListener('input', function() {
                if (this.value.trim() !== "") {
                    repeatBtn.classList.remove('d-none');
                } else {
                    repeatBtn.classList.add('d-none');
                }
            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>