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

    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class=" d-flex justify-content-between">

                        <h4>
                            BS Mesin Perbulan <?= $areas ?>

                        </h4>
                        <div>
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

<div class="modal fade" id="summaryBS" tabindex="-1" role="dialog" aria-labelledby="summaryBS" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Summary BS MC</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?= base_url($role . '/exportSummaryBs'); ?>" method="POST">
                <div class="modal-body align-items-center">
                    <div class="form-group">
                        <label for="buyer" class="col-form-label">Buyer</label>
                        <select class="form-control" id="buyer" name="buyer">
                            <option></option>
                            <?php foreach ($buyer as $buy) : ?>
                                <option><?= $buy['kd_buyer_order'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="area" class="col-form-label">Area</label>
                        <select class="form-control" id="area" name="area">
                            <option></option>
                            <?php foreach ($area as $ar) : ?>
                                <option><?= $ar ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jarum" class="col-form-label">Jarum</label>
                        <select class="form-control" id="jarum" name="jarum">
                            <option></option>
                            <option value="13">13</option>
                            <option value="84">84</option>
                            <option value="92">92</option>
                            <option value="96">96</option>
                            <option value="106">106</option>
                            <option value="108">108</option>
                            <option value="116">116</option>
                            <option value="120">120</option>
                            <option value="124">124</option>
                            <option value="126">126</option>
                            <option value="144">144</option>
                            <option value="168">168</option>
                            <option value="200">200</option>
                            <option value="240">240</option>
                            <option value="POM-POM">POM-POM</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pdk" class="col-form-label">No Model</label>
                        <input type="text" class="form-control" name="pdk">
                    </div>
                    <div class="form-group">
                        <label for="awal" class="col-form-label">Dari</label>
                        <input type="date" class="form-control" name="awal">
                    </div>
                    <div class="form-group">
                        <label for="akhir" class="col-form-label">Sampai</label>
                        <input type="date" class="form-control" name="akhir">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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