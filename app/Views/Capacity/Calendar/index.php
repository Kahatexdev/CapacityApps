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

                                <button class="btn bg-gradient-warning mr-2" data-bs-toggle="modal" data-bs-target="#lihatLibur">
                                    Lihat Data Libur
                                </button>
                                <div> &nbsp;</div>
                                <button class="btn bg-gradient-success ml-2" data-bs-toggle="modal" data-bs-target="#addLibur">
                                    <i class="fas fa-calendar-plus text-lg opacity-10" aria-hidden="true"></i>

                                    Tambah
                                </button>
                                <div> &nbsp;</div>
                                <button class="btn bg-gradient-info ml-2" data-bs-toggle="modal" data-bs-target="#generate">
                                    <i class="fas fa-calendar-plus text-lg opacity-10" aria-hidden="true"></i>
                                    Generate Planning
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
                <button class="btn" data-bs-toggle="modal" data-bs-target="#generate" href="<?= base_url('capacity/calendar/' . $jr['jarum']) ?>">
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

                                    <i class="fas fa-pen-fancy text-sm opacity-10 text-dark" aria-hidden="true"></i>

                                </div>

                            </div>
                        </div>
                    </div>
                </button>
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
                            <form action="<?= base_url('capacity/calendar/' . $jr['jarum']); ?>" method="POST">
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
    <?= $this->renderSection('generate'); ?>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>