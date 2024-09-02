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


    <div class="row mt-3">
        <div class="col-xl-12">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">

                        <h4>Capacity Mesin dari <mark><small><?= $start ?> </mark> </small> sampai <mark><small><?= $end ?> </mark> </small> </h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url($role . '/kebutuhanMesinBooking') ?>" method="POST">
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
                                    <label for="jarum" class="form-control-label">Total Kebutuhan Mesin</label>
                                    <input class="form-control" type="text" value="<?= $totalKebutuhan ?>" id="totalMc" name="totalMc" readonly>
                                </div>

                                <input type="date" value="<?= $start ?>" hidden name="startMc">
                                <input type="date" value="<?= $end ?>" hidden name="stopMc">
                                <input type="date" value="<?= $start ?>" hidden name="tgl_awal">
                                <input type="date" value="<?= $end ?>" hidden name="tgl_akhir">
                                <input type="text" value="<?= $jmlHari ?>" hidden name="hari">
                                <input type="text" value="<?= $desk ?>" hidden name="deskripsi">

                            </div>

                        </div>
                        <hr>
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
    <?php foreach ($planMc as $month => $ranges) : ?>
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h3><?= $month ?></h3>
                            <div class="holiday">
                                <div class="week-holiday d-flex justify-content-between">
                                    <?php foreach ($ranges as $index => $value) : ?>
                                        <?php if (is_array($value) && isset($value['week'])) : ?>
                                            <?php if (isset($value['holidays']) && !empty($value['holidays'])) : ?>
                                                <h5>Week <?= $value['week'] + 1 ?> :</h5>
                                                <div class="week">
                                                    <ul>
                                                        <?php foreach ($value['holidays'] as $holiday) : ?>
                                                            <li class="text-danger"><span class="badge bg-danger">
                                                                    <?= $holiday['nama'] ?> (<?= $holiday['tanggal'] ?>)
                                                                </span></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Minggu Ke</th>
                                        <th> Range</th>
                                        <th>Jumlah Hari</th>
                                        <th>Sisa Order</th>
                                        <th>Kebutuhan Mesin </th>
                                    </tr>
                                </thead>
                                <tbody class="text">
                                    <?php foreach ($ranges as $index => $value) : ?>
                                        <?php if (is_array($value) && isset($value['week'])) : ?> <!-- Pastikan $value adalah array dan punya key 'week' -->
                                            <tr>
                                                <td>Week - <?= $value['week'] ?> </td>
                                                <td><?= $value['start_date'] ?> - <?= $value['end_date'] ?> </td>
                                                <td><?= $value['number_of_days'] ?> hari </td>
                                                <td><?= ceil($value['sisa']) ?> dz</td>
                                                <td><?= $value['totalMc'] ?> mesin</td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end"> Kebutuhan Bulan <?= $month ?> :</th>
                                        <th>
                                            <?= $ranges['kebutuhanMcPerbulan'] ?> Mesin

                                        </th>
                                    </tr>
                                </tfoot>
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