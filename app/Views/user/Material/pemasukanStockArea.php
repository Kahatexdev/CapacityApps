<?php $this->extend('user/layout'); ?>
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
                        <div class="col-7">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                Penerimaan Bahan Baku <?= $area ?>
                            </h5>
                            <!-- <p style="color: red; font-size: 12px;">*Filter tgl po tambahan terlebih dahulu!</p> -->
                        </div>
                        <div class="col-4 d-flex align-items-center text-end gap-2">
                            <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                            <label for="tgl_po">Tgl Kirim Gbn</label>
                            <input type="date" class="form-control" id="tgl_po" value="" required>
                            <button id="searchFilter" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
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
                    <div class="table-responsive">
                        <div id="headerModel" class="table-header" style="display: none; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h4 style="margin: 0;"><span id="table-header"></span></h4>

                            <div class="d-flex align-items-center  gap-3">
                                <!-- <button id="exportPdfBtn" class="btn btn-danger">Export PDF</button> -->
                                <input type="hidden" id="tgl_buat" name="tgl_buat" value="">
                                <button id="generatePdfBtn" class="btn btn-danger">Export PDF</button>
                                <button id="generateExcelBtn" class="btn btn-success">Export Excel</button>
                            </div>
                        </div>

                        <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Item Type</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode Warna</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Color</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kgs Terima</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Cns Terima</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Lot Terima</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($dataPengiriman): ?>
                                    <?php foreach ($dataPengiriman as $i => $row): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= esc($row['no_model'] ?? '-') ?></td>
                                            <td><?= esc($row['item_type'] ?? '-') ?></td>
                                            <td><?= esc($row['kode_warna'] ?? '-') ?></td>
                                            <td><?= esc($row['color'] ?? '-') ?></td>
                                            <td><?= esc($row['kgs_terima'] ?? 0) ?></td>
                                            <td><?= esc($row['cns_terima'] ?? 0) ?></td>
                                            <td><?= esc($row['lot_terima'] ?? '-') ?></td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-primary btn-detail"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailModal"
                                                    data-model="<?= esc($row['no_model'] ?? '-') ?>"
                                                    data-itemtype="<?= esc($row['item_type'] ?? '-') ?>"
                                                    data-warna="<?= esc($row['color'] ?? '-') ?>">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
        const tableEl = $('#dataTable');

        const dataTable = tableEl.DataTable({
            destroy: true,
            columns: [{
                    data: 'no'
                },
                {
                    data: 'no_model'
                },
                {
                    data: 'item_type'
                },
                {
                    data: 'kode_warna'
                },
                {
                    data: 'color'
                },
                {
                    data: 'kgs_terima'
                },
                {
                    data: 'cns_terima'
                },
                {
                    data: 'lot_terima'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                emptyTable: `
                    <?php if (empty($tgl)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-danger fw-bold">
                                Silakan pilih tanggal terlebih dahulu untuk menampilkan data.
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-danger fw-bold">
                                Tidak ada data untuk tanggal <?= esc($tgl) ?>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </div>
            `
            },
            paging: false,
            searching: false,
            info: false
        });

        // 🔵 Tombol filter pencarian
        document.getElementById('searchFilter').addEventListener('click', function() {
            const area = document.getElementById('area').value;
            const tglPo = document.getElementById('tgl_po').value;

            if (!tglPo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Filter belum lengkap',
                    text: 'Silakan pilih tanggal terlebih dahulu!',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Redirect ke URL function inStock dengan query ?tgl=
            window.location.href = `<?= base_url($role . '/stockareaInStock') ?>/${area}?tgl=${tglPo}`;
        });
    });
</script>

<?php $this->endSection(); ?>