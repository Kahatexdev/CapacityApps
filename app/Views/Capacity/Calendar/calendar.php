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
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('capacity/kebutuhanMesinBooking') ?>" method="POST">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">

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
                                    <input class="form-control" type="text" value="<?= $jmlHari ?>" id="jarum" name="hari" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="jarum" class="form-control-label">Total Kebutuhan Mesin</label>
                                    <input class="form-control" type="text" value="<?= $totalKebutuhan ?>" id="totalMc" name="totalMc" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="startmc" class="form-control-label">Start Mesin</label>
                                    <input class="form-control" type="text" value="<?= $start ?>" id="startMc" name="startMc" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="stopMc" class="form-control-label">Stop Mesin</label>
                                    <input class="form-control" type="text" value="<?= $stopmc ?>" id="stopMc" name="stopMc" readonly>
                                </div>
                                <input type="date" value="<?= $start ?>" hidden name="tgl_awal">
                                <input type="date" value="<?= $end ?>" hidden name="tgl_akhir">
                                <input type="text" value="<?= $desk ?>" hidden name="deskripsi">

                            </div>

                        </div>
                        <hr>
                        <div class="row">
                            <?php foreach ($kebMesin as $type => $value) : ?>
                                <div class="col-lg-3 col-md-3">
                                    <div class="form-group">
                                        <label for="jarum" class="form-control-label"><?= $value['type'] ?></label>
                                        <input class="form-control <?= $value['kebutuhanMc'] != 0 ? 'border-info text-bold' : '' ?>" type="text" value="<?= $value['kebutuhanMc'] ?> Mesin <?= $value['JumlahHari'] ?> Hari" id="text-input" readonly>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn bg-gradient-info w-100">
                                    Save
                                </button>
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
                                        <td>F-PS</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['fps'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>

                                    <tr>
                                        <td>F-MP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['fmp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>F-FP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['ffp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>

                                    <tr>
                                        <td>S-PS</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['sps'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>S-MP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['smp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>S-FP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['sfp'] ?></td>
                                        <?php endforeach; ?>
                                    <tr>
                                    <tr>
                                        <td>SS-PS</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['ssps'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>SS-MP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['ssmp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>SS-FP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['ssfp'] ?></td>
                                        <?php endforeach; ?>
                                    <tr>
                                        <td>NS-PS</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['nsps'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>NS-MP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['nsmp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>NS-FP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['nsfp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>


                                    </tr>

                                    <tr>
                                        <td>KH-PS</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['khps'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>KH-MP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['khmp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>KH-FP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['khfp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>TG-PS</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['tgps'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>TG-MP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['tgmp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>TG-FP</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['tgfp'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>GL-FL</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['glfl'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>GL-MT</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['glmt'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>GL-PT</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['glpt'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>GL-ST</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['glst'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>HT-ST</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['htst'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>HT-PL</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['htpl'] ?></td>
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