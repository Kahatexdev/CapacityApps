<?php $this->extend('rosso/layout'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div class="col-6">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                Report Sisa Kebutuhan
                            </h5>
                        </div>
                        <div class="col-3 d-flex align-items-center text-end gap-2">
                            <input type="text" class="form-control" name="filter_model" id="filter_model" value="" placeholder="No Model" required>
                            <button id="filterButton" class="btn btn-info ms-2"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="card-body">
                    <div class="table-responsive text-center">
                        <table id="example" class="table align-items-center" style="width:100%">
                            <thead>
                                <tr style="font-weight: bold; background-color: #f0f0f0;" class="text-center">
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Tanggal Pakai</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Tanggal Retur</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Loss</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Item Type</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Kode Warna</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Warna</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Total Kebutuhan Area</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Qty Pesan (Kg)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">PO(+)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Qty Kirim (Kg)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Lot Kirim</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Qty Retur (Kg)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Lot Retur</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Ket Gbn</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Sisa (Kirim - Kebutuhan - Retur)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($dataPemesanan) && !empty($area) && !empty($noModel)) { ?>
                                    <tr>
                                        <th colspan="16">Tidak Ada Data</th>
                                    </tr>
                                <?php
                                } elseif (empty($dataPemesanan) && empty($area) && empty($noModel)) { ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Silakan pilih area dan isi no model untuk menampilkan data.</td>
                                    </tr>
                                    <?php
                                } elseif (!empty($dataPemesanan) && !empty($area) && !empty($noModel)) {

                                    $prevKey = null;
                                    $ttlKgPesan = 0;
                                    $ttlKgOut = 0;
                                    $ttlKgRetur = 0;
                                    $ttlKebTotal = 0;
                                    $sisa = 0;

                                    foreach ($dataPemesanan as $key => $id) {
                                        // Buat key unik untuk kombinasi
                                        $currentKey = $id['item_type'] . '|' . $id['kode_warna'] . '|' . $id['color'];

                                        if ($prevKey !== null && $currentKey !== $prevKey) {
                                    ?>
                                            <tr style="font-weight: bold; background-color: #f0f0f0;">
                                                <th colspan="7" class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Total Kebutuhan</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKebTotal, 2) ?></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgPesan, 2) ?></th>
                                                <th></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgOut, 2) ?></th>
                                                <th></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgRetur, 2) ?></th>
                                                <th colspan="2"></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center" style="color: <?= $color; ?>"><?= number_format($sisa, 2) ?></th>
                                            </tr>
                                        <?php
                                            // Reset total untuk grup berikutnya
                                            $ttlKgPesan = 0;
                                            $ttlKgOut = 0;
                                            $ttlKgRetur = 0;
                                            $ttlKebTotal = 0;
                                            $sisa = 0;
                                        }
                                        // Hitung total sementara
                                        $ttlKgPesan += $id['ttl_kg'];
                                        $ttlKgOut += $id['kg_out'];
                                        $ttlKgRetur += $id['kgs_retur'];
                                        // Ambil ttl_keb satu kali per grup
                                        if (!isset($shownKebutuhan[$currentKey])) {
                                            $ttlKebTotal = $id['ttl_keb']; // Ambil hanya sekali
                                            $shownKebutuhan[$currentKey] = true;
                                        }
                                        $sisa = $ttlKebTotal - $ttlKgOut + $ttlKgRetur;
                                        if ($sisa < 0) {
                                            $color = "red";
                                        } else {
                                            $color = "green";
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-xs text-center"><?= $id['tgl_pakai']; ?></td>
                                            <td class="text-xs text-center"><?= $id['tgl_retur']; ?></td>
                                            <td class="text-xs text-center"><?= $id['no_model']; ?></td>
                                            <td class="text-xs text-center"><?= $id['max_loss'] ?? ''; ?></td>
                                            <td class="text-xs text-center"><?= $id['item_type']; ?></td>
                                            <td class="text-xs text-center"><?= $id['kode_warna']; ?></td>
                                            <td class="text-xs text-center"><?= $id['color']; ?></td>
                                            <td></td>
                                            <td class="text-xs text-center"><?= number_format($id['ttl_kg'], 2) ?></td>
                                            <td class="text-xs text-center"><?= $id['po_tambahan'] == 1 ? 'YA' : ''; ?></td>
                                            <td class="text-xs text-center"><?= number_format($id['kg_out'], 2) ?></td>
                                            <td class="text-xs text-center"><?= $id['lot_out']; ?></td>
                                            <td class="text-xs text-center"><?= number_format($id['kgs_retur'], 2) ?></td>
                                            <td class="text-xs text-center"><?= $id['lot_retur']; ?></td>
                                            <td class="text-xs text-center"><?= $id['ket_gbn']; ?></td>
                                            <td></td>
                                        </tr>
                                    <?php
                                        $prevKey = $currentKey;
                                    }

                                    // Tampilkan total untuk grup terakhir
                                    if ($prevKey !== null) {
                                    ?>
                                        <tr style="font-weight: bold; background-color: #f0f0f0;">
                                            <th colspan="7" class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Total Kebutuhan</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKebTotal, 2) ?></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgPesan, 2) ?></th>
                                            <th></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgOut, 2) ?></th>
                                            <th></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgRetur, 2) ?></th>
                                            <th colspan="2"></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center" style="color: <?= $color; ?>"><?= number_format($sisa, 2) ?></th>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    //Filter data pemesanan
    document.getElementById('filterButton').addEventListener('click', function() {
        const area = '<?= $area ?>';
        const filterModel = document.getElementById('filter_model').value.trim();

        // Validasi input
        if (!area || !filterModel) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'No Model harus diisi!',
                confirmButtonText: 'OK',
            });
            return; // Hentikan eksekusi jika input kosong
        }

        // Redirect ke controller dengan parameter
        const url = `<?= base_url($role . "/sisaKebutuhanArea") ?>/${area}?filter_model=${encodeURIComponent(filterModel)}`;
        // Redirect
        window.location.href = url;
    });
</script>

<?php $this->endSection(); ?>