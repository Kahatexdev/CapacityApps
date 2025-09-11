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
                <h5 class="mb-0 font-weight-bolder">Report Global Benang</h5>
                <button class="btn btn-secondary btn-block" id="btnInfo" style="padding: 5px 12px; font-size: 12px;" data-bs-toggle="modal" data-bs-target="#infoModal">
                    <i class="fas fa-info"></i>
                </button>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <label for="">Key</label>
                    <input type="text" id="keyInput" class="form-control" placeholder="No Model">
                </div>
                <div class="col-md-8">
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
            <div class="table-responsive">
                <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LOSS</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty PO</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty PO(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stock Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stock Opname</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Datang Solid</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">(+)Datang Solid</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Ganti Retur</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Datang Lurex</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">(+)Datang Lurex</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Retur PB GBN</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Retur PB Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pakai Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pakai Lain-Lain</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Retur Stock</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Retur Titip</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Dipinjam</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pindah Order</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pindah Stock Mati</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stock Akhir</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tagihan GBN</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jatah Area</th>
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
                <p><strong>Note:</strong> Untuk rumus Sisa Tagihan GBN:</p>

                <div class="mb-3">
                    <p>Apabila qty ganti retur > 0 maka:</p>
                    <div class="border p-2">
                        Tagihan GBN = (Stock Awal + Stock Opname + Total Qty Datang + Retur Stock + Qty Ganti Retur) - Qty PO - Qty PO(+) - Retur Belang GBN - Retur Belang Area
                    </div>
                </div>

                <div class="mb-3">
                    <p>Jika qty ganti retur = 0, maka:</p>
                    <div class="border p-2">
                        Tagihan GBN = (Stock Awal + Stock Opname + Total Qty Datang + Retur Stock) - Qty PO - Qty PO(+)
                    </div>
                </div>

                <div class="border p-2 mt-3">
                    <strong>KHUSUS BAHAN BAKU LUREX:</strong> Pengurangan datang diambil dari kolom DTG LUREX / (+) DTG LUREX
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
            "paging": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "responsive": true,
            "processing": true,
            "serverSide": false
        });

        function showLoading() {
            $('#loadingOverlay').addClass('active');
            $('#btnSearch').prop('disabled', true);
            // show DataTables processing indicator if available
            try {
                dataTable.processing(true);
            } catch (e) {}
        }

        function hideLoading() {
            $('#loadingOverlay').removeClass('active');
            $('#btnSearch').prop('disabled', false);
            try {
                dataTable.processing(false);
            } catch (e) {}
        }

        function updateProgress(percent) {
            $('#progressBar')
                .css('width', percent + '%')
                .attr('aria-valuenow', percent);
            $('#progressText').text(percent + '%');
        }

        function loadData() {
            let key = $('#keyInput').val().trim();
            let jenis = 'BENANG';

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (key === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi minimal salah satu kolom sebelum melakukan pencarian!',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterReportGlobalBenang') ?>",
                type: "GET",
                data: {
                    key: key,
                    jenis: jenis
                },
                dataType: "json",
                beforeSend: function() {
                    showLoading();
                    updateProgress(0);
                },
                xhr: function() {
                    let xhr = new window.XMLHttpRequest();

                    // progress download data dari server
                    xhr.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            let percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            updateProgress(percentComplete);
                        }
                    }, false);

                    return xhr;
                },
                success: function(response) {
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            // konversi dulu ke Number, default 0
                            const kgs = Number(item.kgs) || 0;
                            const poTambahan = Number(item.qty_poplus) || 0;
                            const kgsStockAwal = Number(item.stock_awal) || 0;
                            const datangSolid = Number(item.datang_solid) || 0;
                            const plusDatangSolid = Number(item.plus_datang_solid) || 0;
                            const gantiRetur = Number(item.ganti_retur) || 0;
                            const datangLurex = Number(item.datang_lurex) || 0;
                            const plusDatangLurex = Number(item.plus_datang_lurex) || 0;
                            const returPbGbn = Number(item.retur_pb_area) || 0;
                            const returPbArea = Number(item.retur_pb_area) || 0;
                            const pakaiArea = Number(item.pakai_area) || 0;
                            const stockAkhir = Number(item.stock_akhir) || 0;
                            const kgsOtherOut = Number(item.kgs_other_out) || 0;
                            const loss = Number(item.loss) || 0;

                            // perhitungan
                            const tagihanGbn = kgs - (datangSolid + plusDatangSolid + kgsStockAwal);
                            const jatahArea = kgs - pakaiArea;

                            // fungsi bantu untuk format
                            const fmt = v => v !== 0 ? v.toFixed(2) : '0';

                            dataTable.row.add([
                                index + 1,
                                item.no_model || '-', // no model
                                item.item_type || '-', // item type
                                item.kode_warna || '-', //kode warna
                                item.color || '-', // warna
                                fmt(loss), // loss
                                fmt(kgs), // qty po
                                fmt(poTambahan), // qty po (+)
                                fmt(kgsStockAwal), // stock awal
                                '-', // stock opname
                                fmt(datangSolid), // datang solid
                                fmt(plusDatangSolid), // (+) datang solid
                                fmt(gantiRetur), // ganti retur
                                fmt(datangLurex), // datang lurex
                                fmt(plusDatangLurex), // (+) datang lurex
                                fmt(returPbGbn), // retur pb gbn
                                fmt(returPbArea), // retur pb area
                                fmt(pakaiArea), // pakai area 
                                fmt(kgsOtherOut), // pakai lain-lain
                                '-', // retur stock
                                '-', // retur titip
                                '-', // dipinjam
                                '-', // pindah order
                                '-', // pindah ke stock mati
                                fmt(stockAkhir), // stock akhir
                                fmt(tagihanGbn), // tagihan gbn
                                fmt(jatahArea), // jatah area
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
                        let colCount = $('#dataTable thead th').length;
                        $('#dataTable tbody').html(`
                            <tr>
                                <td colspan="${colCount}" class="text-center text-danger font-weight-bold">
                                    ⚠ Tidak ada data ditemukan
                                </td>
                            </tr>
                        `);

                        $('#btnExport').addClass('d-none'); // Sembunyikan jika tidak ada data
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                },
                complete: function() {
                    updateProgress(100); // pastikan full
                    setTimeout(() => hideLoading(), 400); // kasih jeda biar animasi progress keliatan
                }
            });
        }

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExport').click(function() {
            let key = $('#keyInput').val();
            let jenis = 'BENANG';
            window.location.href = "<?= base_url($role . '/warehouse/exportGlobalReport') ?>" +
                "?key=" + encodeURIComponent(key) +
                "&jenis=" + encodeURIComponent(jenis);
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('#keyInput').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });
</script>
<!-- <script>
    $(document).ready(function() {
        let dataTable = $('#dataTable').DataTable({
            "paging": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "responsive": true,
            "processing": true,
            "serverSide": false
        });

        function loadData() {
            let key = $('input[type="text"]').val().trim();

            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterReportGlobalBenang') ?>",
                type: "GET",
                data: {
                    key: key

                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    dataTable.clear().draw();
                    if (response.length > 0) {
                        $.each(response, function(index, item) {

                            const stockAwal = item.kgs_stock_awal ?? 0;
                            const stockOpname = item.stock_opname ?? 0;
                            const datangSolid = item.datang_solid ?? 0;
                            const returStock = item.retur_stock ?? 0;
                            const gantiRetur = item.ganti_retur ?? 0;
                            const qtyPo = item.qty_po ?? 0;
                            const qtyPoPlus = item.qty_po_plus ?? 0;
                            const pakaiArea = item.pakai_area ?? 0;
                            const returGbn = item.retur_gbn ?? 0;
                            const returArea = item.retur_area ?? 0;
                            const returTitipArea = item.retur_titip_area ?? 0;
                            let tagihanBenang = 0;
                            console.log('stock awal :', stockAwal);
                            console.log('opname :', stockOpname);
                            console.log('datang solid :', datangSolid);
                            console.log('retur stock :', returStock);
                            console.log('qty po :', qtyPo);
                            console.log('po plus :', qtyPoPlus);
                            // console.log('stock :', stockAwal, stockOpname, datangSolid, returStock, qtyPo, qtyPoPlus, kgsOut);

                            if (gantiRetur > 0) {
                                tagihanBenang = (stockAwal + stockOpname + datangSolid + returStock + gantiRetur) - qtyPo - qtyPoPlus - returGbn - returArea;
                            } else {
                                tagihanBenang = (stockAwal + stockOpname + datangSolid + returStock) - qtyPo - qtyPoPlus;
                            }
                            const jatahArea = pakaiArea - returArea - returStock - returTitipArea - qtyPo - qtyPoPlus;
                            console.log('jatah area :', jatahArea);

                            dataTable.row.add([
                                index + 1,
                                item.no_model,
                                item.item_type,
                                item.kode_warna,
                                item.warna,
                                item.loss,
                                parseFloat(item.qty_po ?? 0).toFixed(2),
                                parseFloat(item.qty_po_plus ?? 0).toFixed(2),
                                parseFloat(item.kgs_stock_awal ?? 0).toFixed(2),
                                parseFloat(item.stock_opname ?? 0).toFixed(2),
                                parseFloat(item.datang_solid ?? 0).toFixed(2),
                                parseFloat(item.datang_solid_plus ?? 0).toFixed(2),
                                parseFloat(item.ganti_retur ?? 0).toFixed(2),
                                parseFloat(item.datang_lurex ?? 0).toFixed(2),
                                parseFloat(item.datang_lurex_plus ?? 0).toFixed(2),
                                parseFloat(item.retur_gbn ?? 0).toFixed(2),
                                parseFloat(item.retur_area ?? 0).toFixed(2),
                                parseFloat(item.pakai_area ?? 0).toFixed(2),
                                parseFloat(item.pakai_lain_lain ?? 0).toFixed(2),
                                parseFloat(item.retur_stock ?? 0).toFixed(2),
                                parseFloat(item.retur_titip_area ?? 0).toFixed(2),
                                parseFloat(item.dipinjam ?? 0).toFixed(2),
                                parseFloat(item.pindah_order ?? 0).toFixed(2),
                                parseFloat(item.pindah_stock_mati ?? 0).toFixed(2),
                                parseFloat(item.stock_akhir ?? 0).toFixed(2),
                                (tagihanBenang ?? 0).toFixed(2),
                                (jatahArea ?? 0).toFixed(2)
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
                        $('#btnExport').addClass('d-none'); // Sembunyikan jika tidak ada data
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExport').click(function() {
            let key = $('input[type="text"]').val();
            let jenis = 'BENANG';
            window.location.href = "<?= base_url($role . '/warehouse/exportGlobalReport') ?>" +
                "?key=" + encodeURIComponent(key) +
                "&jenis=" + encodeURIComponent(jenis);
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });
</script> -->


<?php $this->endSection(); ?>