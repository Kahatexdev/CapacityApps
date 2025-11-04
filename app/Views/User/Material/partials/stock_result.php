<div id="result">
    <?php foreach ($stock as $st): ?>
        <div class="result-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="badge bg-info"> No Model:<?= $st['no_model']; ?></h5>
                <span class="badge bg-danger">No Karung: <?= $st['no_karung']; ?></span>
            </div>
            <div class="row g-1">
                <div class="col-md-6">
                    <p><strong>Jenis :</strong> <?= $st['item_type']; ?> </p>
                    <p><strong>Lot :</strong> <?= $st['lot']; ?></p>
                    <p><strong>Kode Warna:</strong> <?= $st['kode_warna']; ?> </p>
                    <p><strong>Warna:</strong> <?= $st['warna']; ?> </p>
                    <p><strong>Total Cones: <?= $st['cns_in_out']; ?></strong>
                    <p><strong>Total Kgs: <?= $st['kgs_in_out']; ?></strong>
                </div>
                <div class="col-md-6 d-flex flex-column gap-2">
                    <form action="<?= base_url($role . '/stockarea/outStock') ?>" method="post" class="keluarkan-form">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Cones Out:</label>
                                <input type="hidden" name="idStock" class="form-control" value="<?= $st['id_stock_area']; ?>">
                                <input type="hidden" name="total_cns" class="form-control total-cns" value="<?= $st['cns_in_out']; ?>">
                                <input type="hidden" name="area" class="form-control total-cns" value="<?= $area ?>">
                                <input type="number" name="cns" class="form-control cns-out" placeholder="Cones Out" min="0" step="1">
                                <input type="hidden" name="kg_cns" class="form-control kg_cns" value="<?= $st['kg_cns']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="">Kg Out:</label>
                                <input type="number" name="kg" class="form-control kg-out" placeholder="KG Out" readonly>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <button type="submit" class="btn btn-outline-info btn-sm">
                                Keluarkan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
            <div class="row text-end">
                <div class="col">

                    <span class="badge bg-secondary"> <?= $st['created_at']; ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>