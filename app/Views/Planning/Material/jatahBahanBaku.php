<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="card">
        <!-- Header & Filter -->
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Jatah Bahan Baku</h5>
                <div class="d-flex gap-2">
                    <input
                        type="text"
                        id="no_model"
                        class="form-control form-control-sm"
                        placeholder="Masukkan No Model"
                        value="<?= esc($noModel) ?>">
                    <button id="btnFilter" class="btn btn-sm bg-gradient-info">
                        <i class="fa fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body position-relative">
            <!-- Loader -->
            <div
                id="loader"
                style="display:none; position:absolute; top:50%; left:50%;
               transform:translate(-50%,-50%); background:rgba(255,255,255,0.9);
               padding:1rem 2rem; border-radius:.5rem; box-shadow:0 0 10px rgba(0,0,0,.2);">
                <i class="fa fa-spinner fa-spin fa-2x"></i><br>
                <small>Sedang menghitung…</small>
            </div>

            <!-- Tabel Kontainer -->
            <div id="table-container">
                <?php if (!empty($result)): ?>
                    <?php
                    $warnaMap = [];
                    foreach ($models ?? [] as $m) {
                        if (isset($m['kode_warna']) && isset($m['color'])) {
                            $warnaMap[$m['kode_warna']] = $m['color'];
                        }
                    }
                    ?>

                    <?php foreach ($result as $delivery => $itemTypes): ?>
                        <h5 class="mt-4">Delivery: <?= date('d M Y', strtotime($delivery)) ?></h5>

                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered table-striped">
                                <thead class="table-info text-center align-middle">
                                    <tr>
                                        <th rowspan="2">Item type</th>
                                        <th rowspan="2">Kode Warna</th>
                                        <th rowspan="2">Warna</th>
                                        <th colspan="<?= count($areas) ?>">Area</th>
                                        <th rowspan="2" class="bg-warning text-dark">Grand Total</th>
                                    </tr>
                                    <tr>
                                        <?php foreach ($areas as $area): ?>
                                            <th><?= esc($area) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($itemTypes as $item_type => $colors): ?>
                                        <?php foreach ($colors as $kode_warna => $areaData): ?>
                                            <tr class="text-end align-middle">
                                                <td class="text-start"><?= esc($item_type) ?></td>
                                                <td class="text-start"><?= esc($kode_warna) ?></td>
                                                <td class="text-start"><?= esc($warnaMap[$kode_warna] ?? '-') ?></td>
                                                <?php foreach ($areas as $area): ?>
                                                    <td><?= number_format($areaData[$area] ?? 0, 2) ?></td>
                                                <?php endforeach; ?>
                                                <td class="fw-bold bg-success text-white"><?= number_format($areaData['Grand Total'] ?? 0, 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning">Tidak ada data material yang ditemukan.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        const $loader = $('#loader'),
            $table = $('#table-container');

        $('#btnFilter').on('click', function() {
            const model = $('#no_model').val().trim();
            if (!model) return;

            $.ajax({
                url: '<?= base_url($role . "/jatah_bahan_baku") ?>',
                data: {
                    no_model: model
                },
                beforeSend() {
                    $table.hide();
                    $loader.show();
                },
                success(html) {
                    // Ambil isi #table-container dari response
                    const newHtml = $(html).find('#table-container').html() || '';
                    $table.html(newHtml).show();
                },
                error() {
                    $table.html('<div class="alert alert-danger">Request gagal. Silakan coba lagi.</div>').show();
                },
                complete() {
                    $loader.hide();
                }
            });
        });
    });
</script>
<?php $this->endSection(); ?>