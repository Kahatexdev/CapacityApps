<?php $this->extend('Sudo/layout'); ?>
<?php $this->section('content'); ?>

<style>
/* === PAYLOAD VIEWER === */
.payload-box {
    background: #0f172a; /* slate-900 */
    color: #e5e7eb;      /* gray-200 */
    font-size: 12px;
    line-height: 1.5;
    border-radius: 10px;
    padding: 14px;
    max-height: 340px;
    overflow: auto;
    border: 1px solid #1e293b;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

/* scrollbar */
.payload-box::-webkit-scrollbar {
    width: 6px;
}
.payload-box::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 4px;
}

/* header kecil */
.payload-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 6px;
}

/* OLD / NEW distinction */
.payload-old {
    border-left: 4px solid #ef4444; /* red */
}
.payload-new {
    border-left: 4px solid #22c55e; /* green */
}
</style>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="row my-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                            <h5 class="font-weight-bolder mb-0">Audit Log</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-dark shadow text-center rounded-circle">
                            <i class="ni ni-settings text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="text-sm text-muted mb-0 mt-2">
                        Riwayat aktivitas sistem (sudo read-only).
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="row">
        <div class="col-12">
            <div class="card card-frame">
                <div class="card-body">
                    <form id="filterForm" class="row g-3 align-items-end">

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" class="form-control" id="date_from" value="<?= esc($dateFrom ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" class="form-control" id="date_to" value="<?= esc($dateTo ?? '') ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Module</label>
                            <select class="form-control" id="module">
                            <option value="">Semua</option>
                            <?php foreach ($module as $m): ?>
                                <option value="<?= esc($m['module']) ?>">
                                    <?= esc($m['module']) ?>
                                </option>
                            <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Action</label>
                            <select class="form-control" id="action">
                                <option value="">Semua</option>
                                <?php foreach ($action as $a): ?>
                                    <option value="<?= esc($a['action']) ?>">
                                        <?= esc($a['action']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Cari</label>
                            <input type="text" class="form-control" id="q" placeholder="actor/ref/message...">
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="button" class="btn btn-dark mb-0" id="btnApply">
                                <i class="fas fa-filter me-1"></i> Terapkan
                            </button>
                            <button type="button" class="btn btn-outline-secondary mb-0" id="btnReset">
                                Reset
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="card-header p-3 bg-gradient-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bolder">Daftar Log</h6>
                        <small class="text-muted">Klik “Detail” untuk lihat payload</small>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table id="auditTable" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Waktu</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Actor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Module</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Action</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Ref</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Message</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="px-3 pb-3">
                        <small class="text-muted">
                            * Log menyimpan payload_old/payload_new sebagai JSON (jika ada).
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- MODAL DETAIL -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Audit Log Detail</h6>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <small class="text-muted">Info</small>
                    <div id="auditInfo" class="text-sm"></div>
                </div>
                <hr>
                <div class="row g-3">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary mb-2 btn-copy-payload"
                        data-target="#payloadOld"
                    >
                        <i class="fas fa-copy me-1"></i> Copy Payload Old
                    </button>


                    <div class="col-md-6">
                        <div class="payload-title text-danger">
                            <i class="fas fa-history me-1"></i> Payload Old
                        </div>
                        <pre id="payloadOld" class="payload-box payload-old">{}</pre>
                    </div>

                    <div class="col-md-6">
                        <div class="payload-title text-success">
                            <i class="fas fa-check-circle me-1"></i> Payload New
                        </div>
                        <pre id="payloadNew" class="payload-box payload-new">{}</pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark mb-0" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    (function() {
        function getFilters() {
            return {
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val(),
                module: $('#module').val(),
                action: $('#action').val(),
                q: $('#q').val(),
            };
        }

        const dt = $('#auditTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: "<?= base_url('sudo/audit/datatables') ?>",
                type: "GET",
                data: function(d) {
                    Object.assign(d, getFilters());
                }
            },
            columns: [{
                    data: 'log_time_fmt',
                    defaultContent: '-'
                },
                {
                    data: 'actor',
                    defaultContent: '-'
                },
                {
                    data: 'module_badge',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'action_badge',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'ref',
                    defaultContent: '-'
                },
                {
                    data: 'message',
                    defaultContent: '-'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                // ✅ kolom hidden (tidak ditampilkan)
                {
                    data: 'payload_old',
                    visible: false,
                    searchable: false
                },
                {
                    data: 'payload_new',
                    visible: false,
                    searchable: false
                },
                {
                    data: 'info_html',
                    visible: false,
                    searchable: false
                },
            ],
            language: {
                emptyTable: "Tidak ada log.",
                processing: "Memuat data..."
            }
        });

        $('#btnApply').on('click', () => dt.ajax.reload());
        $('#btnReset').on('click', () => {
            $('#filterForm')[0].reset();
            dt.ajax.reload();
        });
        $('#q').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                dt.ajax.reload();
            }
        });

        // open modal
        $('#auditTable').on('click', '.btn-audit-detail', function() {
            // Cari row yang benar, termasuk kalau DataTables responsive (child row)
            let tr = $(this).closest('tr');

            // kalau tombol ada di child row
            if (tr.hasClass('child')) {
                tr = tr.prev();
            }

            const rowData = dt.row(tr).data();
            if (!rowData) return;

            const info = rowData.info_html || '';
            const oldP = rowData.payload_old || '{}';
            const newP = rowData.payload_new || '{}';

            $('#auditInfo').html(info);

            // payload_old/new dari controller sudah string pretty JSON,
            // tapi kalau suatu saat jadi object, tetap aman:
            const pretty = (v) => (typeof v === 'object' ? JSON.stringify(v, null, 2) : String(v));

            $('#payloadOld').text(prettyJson(oldP));
            $('#payloadNew').text(prettyJson(newP));

            new bootstrap.Modal(document.getElementById('auditDetailModal')).show();
        });

    })();
</script>
<script>
function prettyJson(v) {
    try {
        if (typeof v === 'string') v = JSON.parse(v);
        return JSON.stringify(v, null, 2);
    } catch (e) {
        return String(v);
    }
}
</script>

<script>
$(document).on('click', '.btn-copy-payload', function () {
    const target = $(this).data('target');
    const text   = $(target).text().trim();

    if (!text) return;

    // Modern clipboard API
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            toastCopySuccess();
        }).catch(() => {
            fallbackCopy(text);
        });
    } else {
        // Fallback untuk http / browser lama
        fallbackCopy(text);
    }
});

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        toastCopySuccess();
    } catch (err) {
        alert('Gagal menyalin payload');
    }

    document.body.removeChild(textarea);
}

function toastCopySuccess() {
    // simple feedback
    const toast = document.createElement('div');
    toast.innerHTML = 'Payload berhasil disalin';
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.background = '#1f2937';
    toast.style.color = '#fff';
    toast.style.padding = '8px 14px';
    toast.style.borderRadius = '8px';
    toast.style.fontSize = '12px';
    toast.style.zIndex = 9999;

    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 1500);
}
</script>


<?php $this->endSection(); ?>