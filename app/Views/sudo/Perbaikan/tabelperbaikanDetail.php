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
                                    Data Detail Perbaikan Area
                                </h5>
                            </div>
                        </div>
                        <div class="col-8 text-end">
                            <!-- <a href="<?= base_url($role . '/viewImportPerbaikan') ?>" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Import Data
                            </a>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#exampleModalMessage">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Kode Deffect
                            </button>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#exampleModalMessage2">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Input Kode Deffect
                            </button> -->
                            <!-- <a href="<?= base_url($role . '/detalViewPerbaikan') ?>" class="btn btn-sm btn-info bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Detail Data Perbaikan
                            </a>
                            <button type="button"
                                class="btn btn-sm btn-success bg-gradient-success shadow text-center border-radius-md"
                                data-bs-toggle="modal"
                                data-bs-target="#summaryModal">
                                Summary Global
                            </button> -->

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6 class="card-title">Data In Perbaikan</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row align-items-end">
                        <!-- FORM DISPLAY -->
                        <div class="col-lg-11">
                            <form action="<?= base_url($role . '/detailViewPerbaikan') ?>" method="GET">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="area" class="col-form-label">Dari</label>
                                            <input type="date" name="awal" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="area" class="col-form-label">sampai</label>
                                            <input type="date" name="akhir" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="buyer" class="col-form-label">Buyer</label>
                                            <select class="select2 form-select" id="buyer" name="buyer">
                                                <option value="">Pilih Buyer</option>
                                                <?php foreach ($dataBuyer as $buyer) : ?>
                                                    <option value="<?= $buyer['kd_buyer_order']; ?>"><?= $buyer['kd_buyer_order']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="area" class="col-form-label">Area</label>
                                            <input type="text" name="area" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="area" class="col-form-label">No Model</label>
                                            <input type="text" name="pdk" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-info w-100">Display</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- FORM EXPORT EXCEL -->
                        <div class="col-lg-1 d-flex align-items-end">
                            <form action="<?= base_url($role . '/exportExcelPerbaikan') ?>" method="post" class="w-100">
                                <input type="hidden" name="awal" value="<?= esc($filter['awal'] ?? '') ?>">
                                <input type="hidden" name="akhir" value="<?= esc($filter['akhir'] ?? '') ?>">
                                <input type="hidden" name="buyer" value="<?= esc($filter['buyer'] ?? '') ?>">
                                <input type="hidden" name="area" value="<?= esc($filter['area'] ?? '') ?>">
                                <input type="hidden" name="pdk" value="<?= esc($filter['pdk'] ?? '') ?>">

                                <button type="submit"
                                    class="btn btn-success w-100 excel <?= empty($databs) ? 'disabled' : '' ?>">
                                    Excel
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="datatable">
                        <table id="dataTable1" class="display  striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Tgl In Perbaikan</th>
                                    <th>Area</th>
                                    <th>Buyer</th>
                                    <th>No Model</th>
                                    <th>Style</th>
                                    <th>No Label</th>
                                    <th>No Box</th>
                                    <th>Qty </th>
                                    <th>Kode Deffect</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($databs as $bs) : ?>
                                    <tr>
                                        <td><?= $bs['tgl_perbaikan'] ?></td>
                                        <td><?= $bs['area'] ?></td>
                                        <td><?= $bs['kd_buyer_order'] ?></td>
                                        <td><?= $bs['mastermodel'] ?></td>
                                        <td><?= $bs['size'] ?></td>
                                        <td><?= $bs['no_label'] ?></td>
                                        <td><?= $bs['no_box'] ?></td>
                                        <td><?= $bs['qty'] ?> pcs</td>
                                        <td><?= $bs['kode_deffect'] ?></td>
                                        <td><?= $bs['Keterangan'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#dataTable1').DataTable({
            "order": [
                [0, "desc"]
            ]
        });

    });
</script>
<?php $this->endSection(); ?>