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


    <div class="row mt-3">
        <div class="col-xl-12">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">

                        <h4>Capacity Mesin dari <mark><small><?= $start ?> </mark> </small> sampai <mark><small><?= $end ?> </mark> </small> </h4>
                        <a href="" class="btn bg-gradient-success">Export Excel</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="row">
                            <div class="col-lg-6">

                                <div class="form-group">
                                    <label for="jarum" class="form-control-label">Judul</label>
                                    <input class="form-control" type="text" value="" placeholder="Masukan Judul" required id="judul" name="judul">
                                </div>
                                <div class="form-group">
                                    <label for="jarum" class="form-control-label">Jarum</label>
                                    <input class="form-control" type="text" value="<?= $jarum ?>" id="jarum" name="jarum" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="jarum" class="form-control-label">Jumlah Hari</label>
                                    <input class="form-control" type="text" value="<?= $jmlHari ?>" id="jarum" name="jarum" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="jarum" class="form-control-label">Total Kebutuhan Mesin</label>
                                    <input class="form-control" type="text" value="<?= $totalKebutuhan ?>" id="totalMc" name="totalMc" readonly>
                                </div>

                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Normal Sock</label>
                                            <input class="form-control" type="text" value="<?= $mesinNormal ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Stop MC</label>
                                            <input class="form-control" type="text" value="<?= $mesinNormal ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Sneaker</label>
                                            <input class="form-control" type="text" value="<?= $mesinSneaker ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Stop MC</label>
                                            <input class="form-control" type="text" value="<?= $mesinSneaker ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Knee High</label>
                                            <input class="form-control" type="text" value="<?= $mesinKnee ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Stop MC</label>
                                            <input class="form-control" type="text" value="<?= $mesinKnee ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Footies</label>
                                            <input class="form-control" type="text" value="<?= $mesinFooties ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Stop MC</label>
                                            <input class="form-control" type="text" value="<?= $mesinFooties ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Shaftless</label>
                                            <input class="form-control" type="text" value="<?= $mesinShaftless ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Stop MC</label>
                                            <input class="form-control" type="text" value="<?= $mesinShaftless ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Tight</label>
                                            <input class="form-control" type="text" value="<?= $mesinTight ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Stop MC</label>
                                            <input class="form-control" type="text" value="<?= $mesinTight ?> Mesin" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- kalender -->
    <?php foreach ($weeklyRanges as $month => $ranges) : ?>
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h3><?= $month ?></h3>
                            <div class="holiday">
                                <div class="week-holiday d-flex justify-content-between">
                                    <?php foreach ($ranges as $index => $range) : ?>
                                        <?php if (isset($range['holidays']) && !empty($range['holidays'])) : ?>
                                            <div class="week ">
                                                <h5>Week <?= $index + 1 ?> :</h5>
                                                <ul class="">
                                                    <?php foreach ($range['holidays'] as $holiday) : ?>
                                                        <li class="text-danger"><span class="badge bg-danger">
                                                                <?= $holiday['nama'] ?> (<?= $holiday['tanggal'] ?>)
                                                            </span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table ">
                                <thead>
                                    <tr>
                                        <th rowspan="4" class="text-center">Product Style</th>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <th class="text-center">Week <?= $range['week'] ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <th class="text-xs"> <span><?= $range['start_date'] ?> - <?= $range['end_date'] ?> </span></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <th class="text-xs "> <span class="badge bg-success"> Hari Kerja : <?= $range['number_of_days'] ?> </span></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr>
                                        <td>Normal Sock</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['normal'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>Sneakers</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['sneaker'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>Footies</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['footies'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>Knee High</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['knee'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>Tight</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['tight'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({});

        // Trigger import modal when import button is clicked
        $('.import-btn').click(function() {
            console.log("a");
            var idModel = $(this).data('id');
            var noModel = $(this).data('no-model');

            $('#importModal').find('input[name="id_model"]').val(idModel);
            $('#importModal').find('input[name="no_model"]').val(noModel);

            $('#importModal').modal('show'); // Show the modal
        });
    });
</script>

<?php $this->endSection(); ?>