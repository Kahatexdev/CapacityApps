<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <style>
        #loadingOverlay {
            display: none;
            /* default hidden */
            position: fixed;
            inset: 0;
            /* full layar */
            z-index: 20000;
            background: rgba(0, 0, 0, .35);
            backdrop-filter: saturate(180%) blur(2px);
        }

        /* kotak isi di tengah */
        #loadingOverlay .loading-box {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        #loadingOverlay .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: auto;
        }

        #loadingOverlay .loading-text {
            color: #fff;
            margin-top: 12px;
            font-weight: 600;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Saat tombol di-disable */
        .btn[disabled] {
            pointer-events: none;
            opacity: .65;
        }
    </style>

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
                            BS Mesin

                        </h4>
                        <div>
                            <!-- icon BS -->
                            <i class="fas fa-cogs text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row my-3">
        <div class="col-lg-12">
            <div class="card z-index-2">
                <div class="card-body p-3">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="col-form-label">Dari</label>
                                    <input type="date" name="awal" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="col-form-label">Sampai</label>
                                    <input type="date" name="akhir" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="col-form-label">Area</label>
                                    <input type="text" name="area" class="form-control" placeholder="cth: ROSSO_KK1">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="col-form-label">No Model</label>
                                    <input type="text" name="pdk" class="form-control" placeholder="cth: RZ2637">
                                </div>
                            </div>
                            <div class="col-lg-2 text-end">
                                <div class="form-group">
                                    <label class="col-form-label"></label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-info w-100">Cari</button>
                                        <button type="button" id="resetBtn" class="btn btn-secondary w-100">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div id="tableCard" class="card z-index-2" style="display:none;">
                <div class="card-body p-3">
                    <!-- Tabel -->
                    <div class="px-3 pb-3">
                        <div class="table-responsive">
                            <table id="bsTable" class="table table-striped table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>Area</th>
                                        <th>Tanggal Produksi</th>
                                        <th>No Model</th>
                                        <th>Size</th>
                                        <th>Inisial</th>
                                        <th>Qty Pcs</th>
                                        <th>Qty Gram</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">TOTAL</th>
                                        <th id="totalPcs">0</th>
                                        <th id="totalGram">0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="loading-box">
        <div class="spinner"></div>
        <div class="loading-text">Memuat data…</div>
    </div>
</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(function() {
        const endpoint = "<?= base_url($role . '/bsMesinByDate') ?>";
        const $form = $('#filterForm');
        const $btn = $form.find('button[type="submit"]');
        const $inputs = $form.find('input,select');
        const $overlay = $('#loadingOverlay');
        const $card = $('#tableCard');
        let table = null;
        let isLoading = false;

        // cache filter terakhir yg dipakai request
        let lastFilter = null;

        function showLoading() {
            isLoading = true;
            $('#loadingOverlay').fadeIn(100);
            $btn.prop('disabled', true).text('Memuat…');
            // $inputs.prop('disabled', true); // <-- jangan disable di sini
        }

        function hideLoading() {
            isLoading = false;
            $('#loadingOverlay').fadeOut(100);
            $btn.prop('disabled', false).text('Cari');
            // $inputs.prop('disabled', false);
        }

        function initTable() {
            if (table) return;
            showLoading();

            table = $('#bsTable').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [1, 'asc']
                ],
                ajax: {
                    url: endpoint,
                    type: 'POST',
                    // PAKAI lastFilter, bukan FormData (menghindari masalah disabled)
                    data: function(d) {
                        d.awal = lastFilter?.awal ?? '';
                        d.akhir = lastFilter?.akhir ?? '';
                        d.area = lastFilter?.area ?? '';
                        d.pdk = lastFilter?.pdk ?? '';
                        <?php if (csrf_token()): ?>
                            d["<?= csrf_token() ?>"] = "<?= csrf_hash() ?>";
                        <?php endif; ?>
                    },
                    beforeSend: function() {
                        showLoading();
                    },
                    complete: function() {
                        hideLoading();
                    },
                    error: function() {
                        hideLoading();
                    },
                    dataSrc: function(json) {
                        let sumPcs = 0,
                            sumGram = 0;
                        (json.data || []).forEach(row => {
                            sumPcs += parseInt(row[5] || 0, 10);
                            sumGram += parseFloat(row[6] || 0);
                        });
                        $('#totalPcs').text(new Intl.NumberFormat().format(sumPcs));
                        $('#totalGram').text(new Intl.NumberFormat().format(sumGram));
                        return json.data;
                    }
                },
                columns: [{
                        data: 0
                    }, {
                        data: 1
                    }, {
                        data: 2
                    }, {
                        data: 3
                    }, {
                        data: 4
                    },
                    {
                        data: 5,
                        render: $.fn.dataTable.render.number('.', ',', 0)
                    },
                    {
                        data: 6,
                        render: $.fn.dataTable.render.number('.', ',', 2)
                    },
                ]
            });

            // Fallback hooks
            $('#bsTable').on('preXhr.dt', showLoading)
                .on('xhr.dt error.dt', hideLoading);
        }

        // Submit = cache filter dulu, baru init/reload
        $form.on('submit', function(e) {
            e.preventDefault();
            if (isLoading) return;

            const awal = $form.find('input[name="awal"]').val();
            const akhir = $form.find('input[name="akhir"]').val();
            const area = $form.find('input[name="area"]').val();
            const pdk = $form.find('input[name="pdk"]').val();
            if (!awal || !akhir) return;

            // CACHE DI SINI sebelum ada yang di-disable
            lastFilter = {
                awal,
                akhir,
                area,
                pdk
            };

            // (opsional) baru disable agar user nggak ubah saat request berjalan
            // $inputs.prop('disabled', true);

            if (!table) {
                $card.show();
                initTable();
            } else {
                showLoading();
                table.ajax.reload();
            }
        });

        // Reset
        $('#resetBtn').on('click', function() {
            $form[0].reset();
            lastFilter = null; // kosongkan cache
            $('#totalPcs').text('0');
            $('#totalGram').text('0');
            if (table) {
                table.destroy();
                table = null;
            }
            $('#bsTable tbody').empty();
            $card.hide();
        });
    });
</script>


<?php $this->endSection(); ?>