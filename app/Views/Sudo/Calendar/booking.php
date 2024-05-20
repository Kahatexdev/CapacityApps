<?php $this->extend('Capacity/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">

                                    <i class="fas fa-calendar-alt text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Planning System
                            </h4>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">    

                            <a href="<?php echo base_url().'capacity/datatarget'; ?>" class="btn bg-gradient-warning mr-2">
                                <i class="fas fa-bullseye"></i>
                                Data Target per Product/Needle
                            </a>

                            <div style="margin-right: 10px;">&nbsp;</div> <!-- Space between buttons -->

                            <button class="btn bg-gradient-warning mr-2" data-bs-toggle="modal" data-bs-target="#lihatLibur">
                                Holiday Date
                            </button>

                            <div style="margin-right: 10px;">&nbsp;</div> <!-- Space between buttons -->

                            <button class="btn bg-gradient-success ml-2" data-bs-toggle="modal" data-bs-target="#addLibur">
                                <i class="fas fa-calendar-plus text-lg opacity-10" aria-hidden="true"></i>
                                Add Holidays
                            </button>

                            <div style="margin-left: 10px;">&nbsp;</div> <!-- Space between buttons -->

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



    <div class="row">
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

        <?php foreach ($TotalMesin as $jr) : ?>

            <div class="col-xl-2 col-sm-3 mb-xl-0 mb-4 mt-2">
                <a class=" pilih-jarum " data-bs-toggle="modal" data-bs-target="#generate" data-id="<?= $jr['jarum'] ?>">

                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">

                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $jr['jarum'] ?></p>
                                        <h5 class="font-weight-bolder mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">

                                    <?php if (stripos($jr['jarum'], '10g') !== false || stripos($jr['jarum'], '13G') !== false): ?>
                                        <i class="fas fa-mitten text-lg opacity-10" aria-hidden="true"></i>
                                    <?php elseif (stripos($jr['jarum'], '240N') !== false): ?>
                                        <i class="fab fa-redhat text-lg opacity-10" aria-hidden="true"></i>
                                    <?php elseif (stripos($jr['jarum'], 'POM') !== false): ?>
                                        <i class="fas fa-atom text-lg opacity-10" aria-hidden="true"></i>
                                    <?php else: ?>
                                        <i class="fas fa-socks text-lg opacity-10" aria-hidden="true"></i>
                                    <?php endif; ?>

                                </div>

                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="modal fade  bd-example-modal-lg" id="generate" tabindex="-1" role="dialog" aria-labelledby="generate" aria-hidden="true">
                <div class="modal-dialog  modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Generate Planning</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= base_url('capacity/calendar/'); ?>" method="POST">
                                <div class="form-group">
                                    <label for="awal">Dari</label>
                                    <input type="date" class="form-control" name="awal">
                                </div>
                                <div class="form-group">
                                    <label for="akhir" class="col-form-label">Sampai</label>
                                    <input type="date" name="akhir" class="form-control">
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Generate</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

    </div>
    <div class="row mt-5">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        Tabel Planning Booking
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table  w-100" id="table">
                            <thead>
                                <tr>
                                    <th>
                                        Judul
                                    </th>
                                    <th>
                                        Kebutuhan Mesin
                                    </th>
                                    <th>
                                        Jumlah Hari
                                    </th>
                                    <th>
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kebutuhanMc as $mc) : ?>
                                    <tr>
                                        <td><?= $mc['judul'] ?></td>
                                        <td><?= $mc['total'] ?> Mesin</td>
                                        <td><?= $mc['jumlah_hari'] ?> Hari</td>
                                        <td> <a class="btn bg-gradient-info" href="<?= base_url('capacity/detailbook/' . $mc['judul']) ?>"> Detail Booking </a></td>

                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->renderSection('generatebook'); ?>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#table').DataTable({
            "order": [
                [0, 'desc'] // Kolom pertama (indeks 0) diurutkan secara descending
            ]
        });
    });
</script>
<script script>
    $('.pilih-jarum').click(function() {
        var jarum = $(this).data('id');
        $('#generate').find('form').attr('action', '<?= base_url('capacity/planningbooking/') ?>' + jarum);
    })
</script>
<?php $this->endSection(); ?>