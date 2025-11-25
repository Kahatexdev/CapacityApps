<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                    html: `<?= session()->getFlashdata('error') ?> <br>
                <?php if (session()->getFlashdata('error_list')): ?>
                    <ul style="text-align: left; padding-left: 20px;">
                        <?php foreach (session()->getFlashdata('error_list') as $item): ?>
                            <li><?= esc($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>`
                });
            });
        </script>
    <?php endif; ?>


    <!-- <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card pb-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Input BS Mesin</h5>
                        <div class="d-flex gap-2 ms-auto">
                            <a href="http://172.23.44.14/CapacityApps/public/templateExcel/Template Form Input BS MC.xlsx" target="_blank" class="btn btn-success"><i class="fa fa-download"></i> Download Template</a>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importBs">Import</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="bsMesinForm">
                            <div class="row">
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="nama" class="form-control-label">Nama</label>
                                        <select name="nama" id="nama" class="form-control" onchange="getInfo()">
                                            <option value="" selected>Pilih Nama</option>
                                            <?php if (!empty($karyawan) && is_array($karyawan)): ?>
                                                <?php foreach ($karyawan as $kar): ?>
                                                    <option value="<?= htmlspecialchars($kar['id_karyawan']); ?>">
                                                        <?= htmlspecialchars($kar['nama_karyawan']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">No employees found</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="kode_kartu" class="form-control-label">Kode Kartu</label>
                                        <input type="hidden" class="form-control" id="nama_kar" name="namakar" value="">
                                        <input type="text" class="form-control" id="kode_kartu" name="kode_kartu" placeholder="Masukkan Kode Kartu">
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="shift" class="form-control-label">Shift</label>
                                        <input class="form-control" type="text" id="shift" name="shift">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="tgl_prod" class="form-control-label">Tanggal Produksi</label>
                                        <input class="form-control" type="date" id="tgl_prod" name="tgl_prod" required>
                                    </div>
                                </div>
                            </div>

                            <div id="dynamicRowContainer">
                                <div class="row" id="row_0">
                                    <div class="col-lg-1 col-sm-12">
                                        <div class="form-group">
                                            <label for="no_mesin_0" class="form-control-label">No MC</label>
                                            <input class="form-control" type="text" id="no_mesin_0" name="no_mesin[]" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="no_model_0" class="form-control-label">PDK</label> -->
    <!--<input class="form-control" type="text" id="no_model_0" name="no_model[]" required> -->
    <!--<select name="no_model[]" id="no_model_0" class="select2 form-select" onchange="getIn(0)" required>
                                                <option value="" selected>Pilih No Model</option>
                                                <?php if (!empty($pdk) && is_array($pdk)): ?>
                                                    <?php foreach ($pdk as $pd): ?>
                                                        <option value="<?= htmlspecialchars($pd['mastermodel']); ?>">
                                                            <?= htmlspecialchars($pd['mastermodel']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">No employees found</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="inisial_0" class="form-control-label">Inisial</label>
                                            <select class="select2 form-select" id="inisial_0" name="inisial[]" onchange="getSize(0)" required>
                                                <option value="" selected>Pilih inisial</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="size_0" class="form-control-label">Size</label>
                                            <input class="form-control" type="text" id="size_0" name="size[]" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="gram_0" class="form-control-label">Qty (Gram)</label>
                                            <input class="form-control" type="number" id="gram_0" name="gram[]" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="pcs_0" class="form-control-label">Qty (pcs)</label>
                                            <input class="form-control" type="number" id="pcs_0" name="pcs[]" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-sm-12">
                                        <div class="form-group">
                                            <label for="" class="form-control-label"></label>
                                            <button type="button" class="btn btn-info" onclick="addRow()">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <button type="button" class="btn btn-info" id="submitForm">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class=" d-flex justify-content-between">

                        <h4>
                            BS Mesin Perbulan <?= $areas ?>

                        </h4>
                        <div>

                            <button type="button" class="btn btn-sm btn-danger bg-gradient-danger shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#deleteBs">
                                <i class="fas fa-trash text-lg opacity-10" aria-hidden="true"></i> Delete BS MC
                            </button>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#summaryBS">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Summary BS MC
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <?php foreach ($month as $bln): ?>
            <div class="col-lg-3 mt-2">
                <button type="button"
                    class="btn w-100 p-0 border-0 bg-transparent openAreaModal"
                    data-bulan="<?= $bln ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAreaSelect">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-calendar-alt text-lg opacity-10"></i> <b><?= $bln ?></b>
                            </h5>
                        </div>
                    </div>
                </button>
            </div>
        <?php endforeach ?>
    </div>
</div>

<!-- modal bs mc perbulan -->
<div class="modal fade" id="modalAreaSelect" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Pilih Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="areaSelect">Area</label>
                    <select id="areaSelect" class="form-select">
                        <option value="">-- Pilih Area --</option>
                        <?php foreach ($listArea as $ar): ?>
                            <option value="<?= $ar; ?>"><?= $ar; ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal </button>
                <button type="button" id="submitArea" class="btn btn-success">Lanjut</button>
            </div>

        </div>
    </div>
</div>


<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<!-- jQuery (Diperlukan oleh Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    let selectedBulan = "";

    // Saat kartu diklik → simpan bulan
    $(".openAreaModal").on("click", function() {
        selectedBulan = $(this).data("bulan");
    });

    // Saat user klik "Lanjut"
    $("#submitArea").on("click", function() {
        const area = $("#areaSelect").val();

        if (!area) {
            Swal.fire("Error", "Pilih area terlebih dahulu", "error");
            return;
        }

        // Redirect ke route sesuai pilihan
        window.location.href = "<?= base_url($role . '/bsMesinPerbulan') ?>/" + area + "/" + selectedBulan;
    });
</script>

<?php $this->endSection(); ?>