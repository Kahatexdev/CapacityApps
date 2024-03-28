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

    <div class="row mb-2 mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="font-weight-bolder mb-0">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                                Capasity Calendar
                            </h3>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">

                                <button class="btn bg-gradient-warning mr-2" data-bs-toggle="modal" data-bs-target="#lihatLibur">
                                    Lihat Data Libur
                                </button>
                                <div> &nbsp;</div>
                                <button class="btn bg-gradient-success ml-2" data-bs-toggle="modal" data-bs-target="#addLibur">
                                    Tambah Libur
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade  bd-example-modal-lg" id="addLibur" tabindex="-1" role="dialog" aria-labelledby="tambahLibur" aria-hidden="true">
        <div class="modal-dialog  modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Data Libur</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('capacity/inputLibur') ?>" method="post">
                        <div class="form-group">
                            <label for="tgl-bk-form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tgl_libur">
                        </div>
                        <div class="form-group">
                            <label for="No Model" class="col-form-label">Nama</label>
                            <input type="text" name="nama" class="form-control">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-info">Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade  bd-example-modal-lg" id="lihatLibur" tabindex="-1" role="dialog" aria-labelledby="lihatLibur" aria-hidden="true">
        <div class="modal-dialog  modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Data Libur</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="example" class="display compact " style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        Tanggal
                                    </th>
                                    <th>
                                        Nama
                                    </th>
                                    <th>
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($DaftarLibur as $libur) : ?>
                                    <tr>
                                        <td><?= $libur['tanggal'] ?></td>
                                        <td><?= $libur['nama'] ?></td>
                                        <td><a href="<?= base_url('capacity/hapusLibur/' . $libur['id']) ?>" class="btn btn-danger"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-info">Simpan</button>
                </div>
                </form>
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
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th rowspan="4" class="text-center">Product Style</th>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <th class="text-center">Week <?= $range['week'] ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <th><?= $range['start_date'] ?> - <?= $range['end_date'] ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <th>Hari Kerja : <?= $range['number_of_days'] ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Keterangan</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <?php foreach ($range['data'] as $data) : ?>
                                                <td><?= $data['total_qty'] ?></td>
                                            <?php endforeach; ?>
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