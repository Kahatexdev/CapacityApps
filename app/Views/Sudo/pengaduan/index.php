<?= $this->extend('sudo/layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <!-- Tombol Trigger Modal -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5>Daftar Pengaduan</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPengaduan">
            <i class="fas fa-plus"></i> Buat Pengaduan
        </button>
    </div>

    <!-- Cek apakah ada pengaduan -->
    <?php if (empty($pengaduan)): ?>
        <div class="alert alert-secondary text-center">
            Tidak ada pesan/aduan.
        </div>
    <?php else: ?>
        <!-- List Pengaduan -->
        <?php foreach ($pengaduan as $p): ?>
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <h6><?= esc($p['username']) ?>
                        <small class="text-muted">→ <?= ucfirst($p['target_role']) ?></small>
                    </h6>
                    <span class="text-xs text-muted"><?= date('d M Y H:i', strtotime($p['created_at'])) ?></span>
                </div>
                <div class="card-body">
                    <p><?= esc($p['isi']) ?></p>

                    <!-- List Reply -->
                    <?php if (!empty($p['replies'])): ?>
                        <div class="mt-3 ms-3 border-start ps-3">
                            <?php foreach ($p['replies'] as $r): ?>
                                <div class="mb-2">
                                    <strong><?= esc($r['username']) ?>:</strong> <?= esc($r['isi']) ?>
                                    <div class="text-xs text-muted"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Reply -->
                    <form action="<?= site_url('pengaduan/reply') ?>" method="post" class="mt-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="parent_id" value="<?= $p['id'] ?>">
                        <div class="input-group">
                            <input type="text" name="isi" class="form-control" placeholder="Tulis balasan..." required>
                            <button class="btn btn-outline-primary" type="submit">Balas</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Buat Pengaduan -->
<div class="modal fade" id="modalPengaduan" tabindex="-1" aria-labelledby="modalPengaduanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPengaduanLabel">Buat Pengaduan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('pengaduan/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ditujukan untuk Role</label>
                        <select name="target_role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="capacity">Capacity</option>
                            <option value="planning">PPC</option>
                            <option value="aps">APS</option>
                            <option value="user">Area</option>
                            <option value="rosso">Rosso</option>
                            <option value="gbn">GBN</option>
                            <option value="monitoring">Monitoring Bahan Baku</option>
                            <option value="sudo">Monitoring Produksi</option>

                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Pengaduan</label>
                        <textarea name="isi" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>