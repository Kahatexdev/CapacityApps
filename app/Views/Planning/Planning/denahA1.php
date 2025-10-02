<?php $this->extend($role . '/layoutmc'); ?>
<?php $this->section('content'); ?>

<style>
    :root {
        --primary-blue: #0d6efd;
        --success-green: #198754;
        --warning-orange: #fd7e14;
        --danger-red: #dc3545;
        --info-blue: #0dcaf0;
        --dark-gray: #6c757d;
        --light-gray: #f8f9fa;
        --border-radius: 0.5rem;
        --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    /* Enhanced Loading Overlay */
    .table-responsive {
        position: relative;
        min-height: 300px;
    }

    #denah-loading {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(2px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        border-radius: var(--border-radius);
        transition: opacity 0.3s ease;
    }

    #denah-loading.d-none {
        display: none !important;
    }

    .loading-content {
        text-align: center;
        color: var(--primary-blue);
    }

    .loading-spinner {
        width: 3rem;
        height: 3rem;
        border: 0.25em solid rgba(13, 110, 253, 0.25);
        border-top-color: var(--primary-blue);
        animation: spin 1s linear infinite;
        text-align: center;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Button Enhancements */
    .btn-spinner {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-radius: var(--border-radius) 0 0 var(--border-radius);
    }

    .btn-group .btn:last-child {
        border-radius: 0 var(--border-radius) var(--border-radius) 0;
    }

    /* Enhanced Cell Styling */
    .cell {
        border: 2px solid transparent;
        padding: 12px 16px;
        margin: 3px;
        border-radius: var(--border-radius);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: block;
        width: 100%;
        text-align: center;
        position: relative;
        overflow: hidden;
        min-height: 80px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-shadow: var(--shadow-sm);
    }

    .cell::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s;
    }

    .cell:hover::before {
        left: 100%;
    }

    /* Cell Color Variations */
    .gray-cell {
        background: linear-gradient(135deg, #6c757d, #5a6169);
        color: white;
        border-color: #5a6169;
    }

    .blue-cell {
        background: linear-gradient(135deg, var(--primary-blue), #0b5ed7);
        color: white;
        border-color: #0b5ed7;
    }

    .orange-cell {
        background: linear-gradient(135deg, var(--warning-orange), #fd6c26);
        color: white;
        border-color: #fd6c26;
    }

    .red-cell {
        background: linear-gradient(135deg, var(--danger-red), #bb2d3b);
        color: white;
        border-color: #bb2d3b;
    }

    .peach-cell {
        background: linear-gradient(135deg, #ffeaa7, #fab1a0);
        color: #2d3436;
        border-color: #fab1a0;
    }

    .mid-blue-cell {
        background: linear-gradient(135deg, #74b9ff, #0984e3);
        color: white;
        border-color: #0984e3;
    }

    .bot-blue-cell {
        background: linear-gradient(135deg, #0984e3, #2d3436);
        color: white;
        border-color: #2d3436;
    }

    .green-cell {
        background: linear-gradient(135deg, var(--success-green), #146c43);
        color: white;
        border-color: #146c43;
    }

    /* Cell Hover Effects */
    .cell:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .cell:active {
        transform: translateY(0);
    }

    /* Typography Inside Cells */
    .cell .m-no {
        font-weight: 700;
        font-size: 1rem;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .cell .m-code {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 2px;
    }

    .cell .m-type {
        font-size: 0.8rem;
        line-height: 1.2;
        opacity: 0.9;
        margin-bottom: 2px;
    }

    .cell .m-year {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    /* Empty and Padding Cells */
    .empty-cell {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        width: 100px;
        min-width: 50px;
        padding: 0 !important;
        cursor: default;
    }

    .empty-cell:hover {
        transform: none;
    }

    .left-pad {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        cursor: default;
    }

    .left-pad:hover {
        transform: none;
    }

    /* Enhanced Card Styling */
    .card.shadow-sm {
        box-shadow: var(--shadow-md);
        border: none;
        border-radius: var(--border-radius);
    }

    .card-header {
        border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
        border-bottom: none;
        background: linear-gradient(135deg, #343a40, #495057) !important;
    }

    .card-body {
        padding: 0;
    }

    .card-footer {
        background: var(--light-gray);
        border-top: 1px solid #dee2e6;
        border-radius: 0 0 var(--border-radius) var(--border-radius);
        padding: 1rem;
    }

    /* Enhanced Input Group */
    .input-group {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .input-group .input-group-text {
        background: white;
        border: 1px solid #ced4da;
        border-right: none;
        color: var(--dark-gray);
    }

    .input-group .form-control {
        border: 1px solid #ced4da;
        border-left: none;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .input-group .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .input-group .form-control:focus+.input-group-text,
    .form-control:focus~.input-group-text {
        border-color: var(--primary-blue);
    }

    /* Table Enhancements */
    .table-bordered th,
    .table-bordered td {
        border: 2px solid #e9ecef;
        text-align: center;
        vertical-align: middle;
        padding: 8px;
    }

    .table-bordered thead th {
        background: var(--light-gray);
        font-weight: 600;
        color: var(--dark-gray);
        border-bottom: 2px solid #dee2e6;
    }

    /* Status Legend Enhancements */
    .status-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }

    .status-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-indicator {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        box-shadow: var(--shadow-sm);
    }

    /* Responsive Improvements */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }

        .card-body.py-3 {
            padding: 1rem !important;
        }

        .d-flex.flex-column.flex-md-row {
            gap: 1rem;
        }

        .cell {
            min-height: 60px;
            padding: 8px 12px;
            margin: 2px;
        }

        .cell .m-no {
            font-size: 0.9rem;
        }

        .status-legend {
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .btn-group {
            display: flex;
            width: 100%;
        }

        .btn-group .btn {
            flex: 1;
        }

        .input-group {
            margin-bottom: 0.5rem;
        }
    }

    /* Animation for content updates */
    #denah-rows {
        transition: opacity 0.3s ease;
    }

    .content-updating {
        opacity: 0.6;
    }

    /* Focus improvements for accessibility */
    .cell:focus,
    .btn:focus {
        outline: 2px solid var(--primary-blue);
        outline-offset: 2px;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm rounded-3 overflow-hidden">
                <div class="card-body py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-dark fw-bold">
                            <i class="fas fa-cogs me-2 text-info"></i>
                            Denah Mesin
                        </h4>
                        <small class="text-muted">Pilih tanggal untuk memfilter denah mesin</small>
                    </div>

                    <form id="form-denah" method="get" class="ms-md-3 w-100 w-md-auto" action="<?= current_url() ?>">
                        <div class="d-flex flex-column flex-sm-row align-items-stretch gap-2">
                            <div class="input-group" style="min-width: 200px;">
                                <span class="input-group-text bg-white" id="calendar-addon">
                                    <i class="far fa-calendar-alt text-info" aria-hidden="true"></i>
                                </span>
                                <input
                                    type="date"
                                    name="date"
                                    id="denah-date"
                                    class="form-control"
                                    value="<?= esc($tanggal ?? ''); ?>"
                                    aria-label="Pilih tanggal"
                                    aria-describedby="calendar-addon">
                            </div>

                            <div class="btn-group" role="group" aria-label="Filter actions">
                                <button
                                    class="btn btn-info d-flex align-items-center justify-content-center gap-2"
                                    type="submit"
                                    id="btn-denah-search"
                                    style="min-width: 100px;">
                                    <span class="btn-text">
                                        <i class="fas fa-search"></i> Cari
                                    </span>
                                    <span class="btn-spinner d-none" aria-hidden="true">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                </button>

                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    id="btn-denah-reset"
                                    title="Reset filter"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="bottom">
                                    <i class="fas fa-undo"></i>
                                    <span class="d-none d-sm-inline ms-1">Reset</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white d-flex justify-content-center align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-white">
                        <i class="fas fa-industry me-2"></i>
                        <?= esc($area ?? 'Area Produksi') ?>
                    </h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <!-- Loading Overlay -->
                        <div id="denah-loading" class="d-none" aria-hidden="true">
                            <div class="loading-content">
                                <div class="loading-spinner rounded-circle mb-3"></div>
                                <div class="fw-semibold">Memuat denah...</div>
                                <small class="text-muted">Mohon tunggu sebentar</small>
                            </div>
                        </div>

                        <table class="table table-bordered mb-0" id="denah-table">
                            <tbody id="denah-rows">
                                <?= view($role . '/Planning/partials/denah_rows', ['layout' => $layout]) ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="status-legend">
                        <span class="status-item">
                            <span class="status-indicator bg-success"></span>
                            <span>Running</span>
                        </span>
                        <span class="status-item">
                            <span class="status-indicator bg-info"></span>
                            <span>Idle</span>
                        </span>
                        <span class="status-item">
                            <span class="status-indicator bg-warning"></span>
                            <span>Sample</span>
                        </span>
                        <span class="status-item">
                            <span class="status-indicator bg-danger"></span>
                            <span>Breakdown</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- modal modalDetailDenah -->
<div class="modal fade" id="modalDetailDenah" tabindex="-1" aria-labelledby="modalDetailDenahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Mesin <?= esc($details[0]['no_mesin'] ?? '') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($details)): ?>
                    <div class="alert alert-warning">Tidak ada data.</div>
                <?php else: ?>
                    <div class="accordion" id="denahAccordion">
                        <?php foreach ($details as $i => $row): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-<?= $i ?>">
                                    <button class="accordion-button <?= $i ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $i ?>" aria-expanded="<?= $i ? 'false' : 'true' ?>" aria-controls="collapse-<?= $i ?>">
                                        <?= esc($row['no_mesin'] ?? 'Mesin') ?> — <?= esc($row['mastermodel'] ?? '') ?>
                                    </button>
                                </h2>
                                <div id="collapse-<?= $i ?>" class="accordion-collapse collapse <?= $i ? '' : 'show' ?>" aria-labelledby="heading-<?= $i ?>" data-bs-parent="#denahAccordion">
                                    <div class="accordion-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>Tanggal Produksi</th>
                                                <td><?= esc($row['tgl_produksi'] ?? '') ?></td>
                                            </tr>
                                            <tr>
                                                <th>No Mesin</th>
                                                <td><?= esc($row['no_mesin'] ?? '') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Area</th>
                                                <td><?= esc($row['area'] ?? '') ?></td>
                                            </tr>
                                            <tr>
                                                <th>PDK</th>
                                                <td><?= esc($row['mastermodel'] ?? '') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Size</th>
                                                <td><?= esc($row['size'] ?? '') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Inisial</th>
                                                <td><?= esc($row['inisial'] ?? '') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Qty</th>
                                                <td><?= esc($row['qty_produksi'] ?? '') ?></td>
                                            </tr>
                                            <tr>
                                                <th>BS Pcs</th>
                                                <td><?= number_format((float)($row['bs_pcs'] ?? 0), 2) ?></td>
                                            </tr>
                                            <tr>
                                                <th>BS Gram</th>
                                                <td><?= number_format((float)($row['bs_gram'] ?? '0'), 2) ?></td>
                                            </tr>
                                            <!-- tambahkan field lain sesuai kebutuhan -->
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        'use strict';

        // Cache DOM elements
        const elements = {
            form: $('#form-denah'),
            btnSearch: $('#btn-denah-search'),
            btnReset: $('#btn-denah-reset'),
            dateInput: $('#denah-date'),
            overlay: $('#denah-loading'),
            tbody: $('#denah-rows'),
            table: $('#denah-table')
        };

        // Config
        const config = {
            animationDuration: 300,
            debounceDelay: 300,
            // tidak ada limit timeout
            ajaxTimeout: 0
        };

        // store original html (if needed)
        if (!elements.btnSearch.data('orig-html')) {
            elements.btnSearch.data('orig-html', elements.btnSearch.html());
        }

        // Utilities
        const utils = {
            showButtonLoading() {
                elements.btnSearch.prop('disabled', true);
                elements.btnSearch.find('.btn-text').addClass('visually-hidden');
                elements.btnSearch.find('.btn-spinner').removeClass('d-none');
            },

            hideButtonLoading() {
                elements.btnSearch.prop('disabled', false);
                elements.btnSearch.find('.btn-text').removeClass('visually-hidden');
                elements.btnSearch.find('.btn-spinner').addClass('d-none');
            },

            showOverlay() {
                if (elements.overlay.length) {
                    elements.overlay.removeClass('d-none').attr('aria-hidden', 'false');
                    elements.tbody.addClass('content-updating');
                }
            },

            hideOverlay() {
                if (elements.overlay.length) {
                    elements.overlay.addClass('d-none').attr('aria-hidden', 'true');
                    elements.tbody.removeClass('content-updating');
                }
            },

            // update URL without leaving trailing '?'
            updateUrl(date) {
                if (history && history.replaceState) {
                    const params = new URLSearchParams(window.location.search);
                    if (date) params.set('date', date);
                    else params.delete('date');

                    const q = params.toString();
                    const newUrl = window.location.pathname + (q ? '?' + q : '');
                    // only replace if changed (avoid noisy history)
                    if (newUrl !== window.location.pathname + window.location.search) {
                        history.replaceState({}, '', newUrl);
                    }
                }
            },

            showToast(message, type = 'info') {
                // placeholder toast — replace with your toast lib if ada
                // types: success, info, warning, error
                console.log(`[${type.toUpperCase()}] ${message}`);
            },

            formatDate(date) {
                if (!date) return '';
                try {
                    return new Date(date).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                } catch (e) {
                    return date;
                }
            }
        };

        // Fetch function (returns jqXHR)
        function fetchDenah(formData) {
            const url = elements.form.attr('action') || window.location.href;

            return $.ajax({
                url: url,
                type: 'GET',
                data: formData,
                dataType: 'text', // request as text first to be tolerant to non-json responses
                timeout: config.ajaxTimeout,
                beforeSend() {
                    utils.showButtonLoading();
                    utils.showOverlay();
                }
            });
        }

        // Handle replacement of table body from different possible server responses
        function replaceTbodyFromResponse(responseText, selectedDate) {
            // try JSON first (some servers return JSON string)
            let parsed;
            try {
                parsed = JSON.parse(responseText);
            } catch (err) {
                parsed = null;
            }

            let html = '';
            if (parsed && parsed.html) {
                html = parsed.html;
            } else {
                // server returned raw HTML — try to extract <tbody> content
                const tbodyMatch = /<tbody[^>]*>([\s\S]*?)<\/tbody>/i.exec(responseText);
                if (tbodyMatch && tbodyMatch[1]) {
                    html = tbodyMatch[1];
                    // wrap with <tbody> in case we need complete rows
                    html = html;
                } else {
                    // fallback: use whole response (useful if controller returns only rows)
                    html = responseText;
                }
            }

            // if html empty -> show warning
            if (!html || html.trim() === '') {
                utils.showToast('Data tidak ditemukan', 'warning');
                return false;
            }

            // animate replacement
            elements.tbody.stop(true, true).fadeOut(150, function() {
                $(this).html(html).fadeIn(150, function() {
                    // after inserted, reinitialize any plugins/listeners
                    reinitAfterDomUpdate();
                });
            });

            // success toast
            utils.updateUrl(selectedDate);
            utils.showToast(
                selectedDate ?
                `Denah berhasil dimuat untuk tanggal ${utils.formatDate(selectedDate)}` :
                'Denah berhasil dimuat untuk hari ini',
                'success'
            );

            return true;
        }

        // re-init tooltips and attach delegated listeners for .cell
        function reinitAfterDomUpdate() {
            // reinit bootstrap tooltips if available
            if ($.fn.tooltip) {
                $('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
            }

            // delegated click handler for .cell (so it keeps working after updates)
            // attach once by namespacing to avoid duplicates
            // inside reinitAfterDomUpdate() click handler
            elements.table.off('click.denah', '.cell').on('click.denah', '.cell', function(e) {
                const $btn = $(this);
                const rawIdProd = $btn.data('idprod'); // could be "123, 456" or "123"
                const rawIdAps = $btn.data('idaps');

                // normalize: extract all number-like tokens (handles commas, spaces, etc.)
                const idprods = ('' + (rawIdProd || '')).match(/\d+/g) || [];
                const idapss = ('' + (rawIdAps || '')).match(/\d+/g) || [];

                if (!idprods.length && !idapss.length) {
                    console.warn('No ids found on .cell', {
                        rawIdProd,
                        rawIdAps
                    });
                    return;
                }

                // prevent duplicate clicks
                if ($btn.data('loading')) return;
                $btn.data('loading', true);

                const BASEURL = '<?= base_url() ?>';
                const ROLE = '<?= $role ?>';

                $.ajax({
                    url: `${BASEURL}/${ROLE}/detailDenah`,
                    method: 'GET',
                    data: {
                        'idprod[]': idprods,
                        'idaps[]': idapss
                    },
                    dataType: 'json'
                }).done(function(res) {
                    let html = '';
                    if (!res.length) {
                        html = '<div class="alert alert-warning">Tidak ada data.</div>';
                    } else {
                        html = '<div class="accordion" id="denahAccordion">';
                        res.forEach((row, i) => {
                            html += `
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-${i}">
                <button class="accordion-button ${i ? 'collapsed' : ''}" 
                        type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapse-${i}">
                  ${row.no_mesin || 'Mesin'} — ${row.mastermodel || ''}
                </button>
              </h2>
              <div id="collapse-${i}" class="accordion-collapse collapse ${i ? '' : 'show'}"
                   data-bs-parent="#denahAccordion">
                <div class="accordion-body">
                  <table class="table table-sm">
                    <tr><th>Tanggal Produksi</th><td>${row.tgl_produksi || ''}</td></tr>
                    <tr><th>No Mesin</th><td>${row.no_mesin || ''}</td></tr>
                    <tr><th>Area</th><td>${row.area || ''}</td></tr>
                    <tr><th>PDK</th><td>${row.mastermodel || ''}</td></tr>
                    <tr><th>Size</th><td>${row.size || ''}</td></tr>
                    <tr><th>Inisial</th><td>${row.inisial || ''}</td></tr>
                    <tr><th>Qty</th><td>${row.qty_produksi || ''}</td></tr>
                    <tr><th>BS Pcs</th><td>${parseFloat(row.bs_pcs || 0).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td></tr>
                    <tr><th>BS Gram</th><td>${parseFloat(row.bs_gram || 0).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td></tr>
                  </table>
                </div>
              </div>
            </div>`;
                        });
                        html += '</div>';
                    }

                    $('#modalDetailDenah .modal-body').html(html);
                    new bootstrap.Modal(document.getElementById('modalDetailDenah')).show();
                })
                .fail(function(xhr, status, error) {
                    console.error('Error fetching modal content', {
                        xhr,
                        status,
                        error
                    });
                    utils.showToast('Gagal memuat detail. Coba lagi.', 'error');
                }).always(function() {
                    $btn.data('loading', false);
                });
            });
        }


        // FORM submit (main)
        elements.form.on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const selectedDate = elements.dateInput.val();

            fetchDenah(formData)
                .done(function(respText) {
                    const ok = replaceTbodyFromResponse(respText, selectedDate);
                    if (!ok) {
                        // nothing updated, but still ok
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Error fetching denah data:', {
                        xhr,
                        status,
                        error
                    });

                    let errorMessage = 'Terjadi kesalahan saat memuat data';
                    if (status === 'timeout') {
                        errorMessage = 'Permintaan timeout, coba lagi';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Data tidak ditemukan (404)';
                    } else if (xhr.status >= 500) {
                        errorMessage = 'Terjadi kesalahan server';
                    } else if (xhr.responseText) {
                        // If server returned HTML (maybe non-AJAX), try to salvage rows
                        const salvaged = replaceTbodyFromResponse(xhr.responseText, selectedDate);
                        if (salvaged) {
                            errorMessage = 'Data dimuat dari respon HTML (non-JSON).';
                        }
                    }

                    utils.showToast(errorMessage, 'error');
                })
                .always(function() {
                    utils.hideButtonLoading();
                    utils.hideOverlay();
                });
        });

        // Reset button — clear date and update URL (no trailing ?)
        elements.btnReset.on('click', function(e) {
            e.preventDefault();
            elements.dateInput.val(new Date().toISOString().slice(0, 10));
            utils.updateUrl(''); // remove date param from URL immediately
            elements.form.trigger('submit');
        });

        // Keyboard shortcuts for date input
        elements.dateInput.on('keydown', function(e) {
            if (e.key === 'Escape') {
                elements.btnReset.trigger('click');
            } else if (e.key === 'Enter') {
                e.preventDefault();
                elements.form.trigger('submit');
            }
        });

        // Auto-submit on date change (debounced)
        let dateChangeTimeout;
        // elements.dateInput.on('change', function() {
        //     clearTimeout(dateChangeTimeout);
        //     dateChangeTimeout = setTimeout(function() {
        //         elements.form.trigger('submit');
        //     }, config.debounceDelay);
        // });

        // init on ready
        $(function() {
            if ($.fn.tooltip) {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
            reinitAfterDomUpdate();
        });

        // Expose utils globally
        window.denahUtils = {
            refresh: () => elements.form.trigger('submit'),
            reset: () => elements.btnReset.trigger('click'),
            setDate: (date) => {
                elements.dateInput.val(date);
                elements.form.trigger('submit');
            },
            hideLoading: () => {
                // provide a function that hides both overlay & button spinner
                utils.hideButtonLoading();
                utils.hideOverlay();
            }
        };

        // visibilitychange (debug)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                console.log('Page hidden — updates paused (if any interval exists).');
            } else {
                console.log('Page visible — ready.');
            }
        });

    })(jQuery);
</script>

<?php $this->endSection(); ?>