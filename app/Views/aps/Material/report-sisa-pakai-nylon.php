<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <style>
        /* Overlay transparan */
        #loadingOverlay {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
        }

        .loader-wrap {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-card {
            background: rgba(0, 0, 0, 0.75);
            padding: 20px 30px;
            border-radius: 12px;
            text-align: center;
            width: 260px;
            /* kecilkan modal */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .loader-text {
            margin-top: 8px;
            color: #fff;
            font-weight: 500;
            font-size: 12px;
        }


        #loadingOverlay.active {
            display: block;
            opacity: 1;
        }

        .loader {
            width: 50px;
            height: 50px;
            margin: 0 auto 10px;
            position: relative;
        }

        .loader:after {
            content: "";
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: loader-dual-ring 1.2s linear infinite;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.5);
        }

        @keyframes loader-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }


        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .progress-bar {
            transition: width .3s ease;
        }
    </style>
    <!-- overlay -->
    <div id="loadingOverlay">
        <div class="loader-wrap">
            <div class="loading-card">
                <div class="loader" role="status" aria-hidden="true"></div>
                <div class="loader-text">Memuat data...</div>

                <!-- Progress bar -->
                <div class="progress mt-3" style="height: 6px; border-radius: 6px;">
                    <div id="progressBar"
                        class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small id="progressText" class="text-white mt-1 d-block">0%</small>
            </div>
        </div>
    </div>

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Report Sisa Pakai Nylon</h5>
                <button class="btn btn-secondary btn-block" id="btnInfo" style="padding: 5px 12px; font-size: 12px;" data-bs-toggle="modal" data-bs-target="#infoModal">
                    <i class="fas fa-info"></i>
                </button>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label for="">Delivery</label>
                    <select name="delivery" id="delivery" class="form-select">
                        <option value="">Pilih Bulan Delivery</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="">No Model</label>
                    <input type="text" class="form-control" name="no_model" id="no_model" placeholder="No Model">
                </div>
                <div class="col-md-3">
                    <label for="">Kode Warna</label>
                    <input type="text" class="form-control" name="kode_warna" id="kode_warna" placeholder="Kode Warna">
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Aksi</label><br>
                        <button class="btn btn-info btn-block" id="btnSearch"><i class="fas fa-search"></i></button>
                        <button class="btn btn-danger" id="btnReset"><i class="fas fa-redo-alt"></i></button>
                        <button class="btn btn-primary d-none" id="btnExport"><i class="fas fa-file-excel"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mb-3 font-weight-bolder">Tabel Sisa Pakai Nylon</h5>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-hover text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal PO</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Foll Up</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Order</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Start Mc</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Order Type</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th colspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stock Awal</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pesan Kg</th>
                            <th colspan="4" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Po Tambahan Gbn</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pakai</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">(+) Pakai</th>
                            <th colspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Retur</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sisa</th>
                        </tr>
                        <tr>
                            <!-- Sub-header Stock Awal -->
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot</th>

                            <!-- Sub-header Po Tambahan Gbn -->
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tgl Terima Po(+) Gbn</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tgl Po(+) Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Po(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg Po (+)</th>

                            <!-- Sub-header Retur -->
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot</th>
                        </tr>
                    </thead>

                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel">Informasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Note:</strong> Rumus Sisa Pakai Nylon</p>
                <div class="mb-3">
                    <div class="border p-2">
                        ((Pakai + (+)Pakai) - Retur - (Pesan + PO(+)))
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let dataTable = $('#dataTable').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            paging: true,
            info: true,
            ordering: true,
            data: [],
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                }, // No
                {
                    data: "lco_date"
                }, // Tanggal PO
                {
                    data: "foll_up"
                }, // Foll Up
                {
                    data: "no_model"
                }, // No Model
                {
                    data: "no_order"
                }, // No Order
                {
                    data: "area_out"
                }, // Area
                {
                    data: "buyer"
                }, // Buyer
                {
                    data: "start_mc"
                }, // Start Mc
                {
                    data: "delivery_awal"
                }, // Delivery Awal
                {
                    data: "delivery_akhir"
                }, // Delivery Akhir
                {
                    data: "unit"
                }, // Order Type
                {
                    data: "item_type"
                }, // Item Type
                {
                    data: "kode_warna"
                }, // Kode Warna
                {
                    data: "color"
                }, // Warna
                {
                    data: "kgs_stock_awal",
                    render: d => (parseFloat(d) || 0).toFixed(2)
                }, // Stock Awal Kg
                {
                    data: "lot_awal"
                }, // Stock Awal Lot
                {
                    data: "kg_pesan",
                    render: d => (parseFloat(d) || 0).toFixed(2)
                }, // Pesan Kg
                {
                    data: "tgl_terima_po_plus"
                }, // Tgl Terima Po (+) Gbn
                {
                    data: "tgl_po_plus_area"
                }, // Tgl Po (+) Area
                {
                    data: "delivery_po_plus"
                }, // Delivery Po (+)
                {
                    data: "kg_po_plus",
                    render: d => (parseFloat(d) || 0).toFixed(2)
                }, // Kg Po (+)
                { // Pakai
                    data: null,
                    render: d => {
                        let val = (d.kgs_out !== undefined && d.kgs_out !== null && d.kgs_out !== '' && parseFloat(d.kgs_out) !== 0) ?
                            parseFloat(d.kgs_out) :
                            (parseFloat(d.kgs_other_out) || 0);
                        return (val || 0).toFixed(2);
                    }
                },
                {
                    data: "kgs_out_plus",
                    render: d => (parseFloat(d) || 0).toFixed(2)
                }, // Pakai (+)
                {
                    data: "kgs_retur",
                    render: d => (parseFloat(d) || 0).toFixed(2)
                }, // Retur Kg
                {
                    data: "lot_retur"
                }, // Retur Lot
                { // Sisa
                    data: null,
                    render: d => {
                        const stockAwal = parseFloat(d.kgs_stock_awal) || 0;
                        const pesan = parseFloat(d.kg_pesan) || 0;
                        const poPlus = parseFloat(d.kg_po_plus) || 0;
                        const retur = parseFloat(d.kgs_retur) || 0;

                        const pakai = (d.kgs_out !== undefined && d.kgs_out !== null && d.kgs_out !== '' && parseFloat(d.kgs_out) !== 0) ?
                            parseFloat(d.kgs_out) :
                            (parseFloat(d.kgs_other_out) || 0);
                        const pakaiPlus = parseFloat(d.kgs_out_plus) || 0;

                        // Rumus: Stock + Pesan + Po Tambah + Retur - (Pakai + Pakai Plus)
                        const sisa = (pakai + pakaiPlus) - retur - (pesan + poPlus);

                        return `<span style="${sisa < 0 ? 'color:red;font-weight:bold;' : ''}">${sisa.toFixed(2)}</span>`;
                    }
                }
            ]
        });

        function showLoading() {
            $('#loadingOverlay').addClass('active');
            $('#btnSearch').prop('disabled', true);
            dataTable.processing(true);
        }

        function hideLoading() {
            $('#loadingOverlay').removeClass('active');
            $('#btnSearch').prop('disabled', false);
            dataTable.processing(false);
        }

        function updateProgress(percent) {
            $('#progressBar')
                .css('width', percent + '%')
                .attr('aria-valuenow', percent);
            $('#progressText').text(percent + '%');
        }

        function loadData() {
            const delivery_awal = $('#delivery').val();
            const no_model = $('#no_model').val().trim();
            const kode_warna = $('#kode_warna').val().trim();
            const jenis = 'NYLON';

            if (!delivery_awal && !no_model && !kode_warna) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Tidak Boleh Kosong!',
                    text: 'Silakan isi minimal salah satu input untuk pencarian.',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterSisaPakaiNylon') ?>",
                type: "GET",
                data: {
                    delivery: delivery_awal,
                    no_model: no_model,
                    kode_warna: kode_warna,
                    jenis: jenis
                },
                dataType: "json",
                beforeSend: function() {
                    showLoading();
                    updateProgress(0);
                },
                xhr: function() {
                    let xhr = new window.XMLHttpRequest();
                    xhr.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            let percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            updateProgress(percentComplete);
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    if (response.length > 0) {
                        dataTable.clear().rows.add(response).draw();
                        $('#btnExport').removeClass('d-none');
                    } else {
                        dataTable.clear().draw();
                        $('#btnExport').addClass('d-none');
                        Swal.fire({
                            icon: 'info',
                            title: 'Tidak ada data ditemukan',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                },
                complete: function() {
                    updateProgress(100);
                    setTimeout(() => hideLoading(), 400);
                }
            });
        }

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExport').click(function() {
            const delivery = $('#delivery').val();
            const no_model = $('#no_model').val().trim();
            const kode_warna = $('#kode_warna').val().trim();
            const jenis = 'NYLON';
            const url = "<?= base_url($role . '/warehouse/exportReportSisaPakaiNylon') ?>" +
                "?delivery=" + encodeURIComponent(delivery) +
                "&no_model=" + encodeURIComponent(no_model) +
                "&kode_warna=" + encodeURIComponent(kode_warna) +
                "&jenis=" + encodeURIComponent(jenis);

            window.location.href = url;
        });

        // Fitur Reset
        $('#btnReset').click(function() {
            $('input[type="text"]').val('');
            $('#delivery').val('');
            dataTable.clear().draw();
            $('#btnExport').addClass('d-none');
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Trigger pencarian saat tombol Enter ditekan di input apa pun
        $('#delivery, #no_model, #kode_warna').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Hindari form submit default (jika ada form)
                $('#btnSearch').click(); // Trigger tombol Search
            }
        });
    });
</script>

<?php $this->endSection(); ?>