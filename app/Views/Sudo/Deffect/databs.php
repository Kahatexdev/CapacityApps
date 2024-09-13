<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Deffect
                                </h5>
                            </div>
                        </div>
                        <div class="col-8 text-end">
                            <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#exampleModalMessage">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Kode Deffect
                            </button>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#exampleModalMessage2">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Input Kode Deffect
                            </button>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-warning shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#reset">
                                <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i> Reset BS PDK
                            </button>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-warning shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#resetarea">
                                <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i> Reset BS Area
                            </button>
                        </div>
                    </div>
                </div>
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
        <div class="modal fade" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Kode Deffect</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="<?= base_url($role . '/summaryProdPerTanggal'); ?>" method="POST">
                        <div class="modal-body align-items-center">
                            <div class="datatable">
                                <table id="dataTable" class="display  striped" style="width:100%">
                                    <thead>

                                        <tr>
                                            <th>Kode</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach ($kode as $df) : ?>
                                            <tr>
                                                <td><?= $df['kode_deffect'] ?></td>
                                                <td><?= $df['Keterangan'] ?></td>
                                                <td><a href="" class="btn btn-danger">Hapus</a></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>

                                </table>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- modal summary produksi -->
        <div class="modal fade" id="exampleModalMessage2" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Input Kode Deffect</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="<?= base_url($role . '/inputKode'); ?>" method="POST">
                        <div class="modal-body align-items-center">

                            <div class="form-group">
                                <label for="kode" class="col-form-label">Kode Deffect</label>
                                <input type="text" class="form-control" name="kode" required>
                            </div>
                            <div class="form-group">
                                <label for="keterangan" class="col-form-label">Keterangan</label>
                                <input type="text" class="form-control" name="keterangan" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Input</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row my-3">
            <div class="col-lg-12">
                <div class="card z-index-2">
                    <div class="card-header pb-0">
                        <h6 class="card-title">Data Deffect Stocklot</h6>

                    </div>
                    <div class="card-body p-3">
                        <form action="<?= base_url($role . '/viewDataBs') ?>" method="POST">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="area" class="col-form-label">Dari</label>
                                        <input type="date" name="awal" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="area" class="col-form-label">sampai</label>
                                        <input type="date" name="akhir" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="area" class="col-form-label">Area</label>
                                        <input type="text" name="area" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="area" class="col-form-label">No Model</label>
                                        <input type="text" name="pdk" class="form-control">
                                    </div>
                                </div>

                            </div>
                            <div class="row justify-content-end">
                                <div class="col-lg-2 justify-content-end text-end">
                                    <button type="submit" class="btn btn-info"> Display</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- modal reset bs pdk -->
        <div class="modal fade" id="reset" tabindex="-1" role="dialog" aria-labelledby="reset" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Reset BS PDK</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body align-items-center">
                        <form action="<?= base_url($role . '/resetbspdk/'); ?>" method="post">
                            <div class="form-group">
                                <label for="awal">PDK</label>
                                <input type="text" class="form-control" name="pdk">
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Reset</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- modal reset bs area -->
        <div class="modal fade" id="resetarea" tabindex="-1" role="dialog" aria-labelledby="resetarea" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Reset BS Area</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body align-items-center">
                        <form action="<?= base_url($role . '/resetbsarea/'); ?>" method="post">
                            <div class="form-group">
                                <label for="awal">Area</label>
                                <input type="text" class="form-control" name="area" required>
                            </div>
                            <div class="form-group">
                                <label for="awal">Awal</label>
                                <input type="date" class="form-control" name="awal" required>
                            </div>
                            <div class="form-group">
                                <label for="awal">Akhir</label>
                                <input type="date" class="form-control" name="akhir" required>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Reset</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <?= $this->renderSection('bstabel'); ?>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn bg-gradient-info">Reset</button>
    </div>
    </form>
</div>
</div>
</div>
<!-- modal reset bs area -->
<div class="modal fade" id="resetarea" tabindex="-1" role="dialog" aria-labelledby="resetarea" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reset BS Area</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body align-items-center">
                <form action="<?= base_url($role . '/resetbsarea/'); ?>" method="post">
                    <div class="form-group">
                        <label for="awal">Area</label>
                        <input type="text" class="form-control" name="area" required>
                    </div>
                    <div class="form-group">
                        <label for="awal">Awal</label>
                        <input type="date" class="form-control" name="awal" required>
                    </div>
                    <div class="form-group">
                        <label for="awal">Akhir</label>
                        <input type="date" class="form-control" name="akhir" required>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn bg-gradient-info">Reset</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?= $this->renderSection('bstabel'); ?>

</div>

<!-- Skrip JavaScript -->
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [
                [0, "desc"]
            ]
        });

    });
</script>


</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>