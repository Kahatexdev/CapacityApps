<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/flatpickr/flatpickr.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/js/plugins/flatpickr/style.css') ?>">
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
                <h5 class="mb-0 font-weight-bolder">Report Benang Bulanan</h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <label for="">Bulan</label>
                    <input type="text" id="bulanPicker" name="bulan" class="form-control" placeholder="Pilih bulan">
                </div>
                <div class="col-md-4">
                    <label for="">Aksi</label><br>
                    <button class="btn btn-info btn-block" id="btnSearch"><i class="fas fa-search"></i></button>
                    <button class="btn btn-danger" id="btnReset"><i class="fas fa-redo-alt"></i></button>
                    <button class="btn btn-primary d-none" id="btnExport"><i class="fas fa-file-excel"></i></button>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No SJ</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal SJ</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Penerimaan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Benang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">L/M/D</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Krg/Pck</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">GW (Kg)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">NW (Kg)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Harga Per Kg (USD)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total (USD)</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/css/flatpickr/flatpickr.min.js') ?>"></script>
<script src="<?= base_url('assets/js/plugins/flatpickr/index.js') ?>"></script>
<script>
    flatpickr("#bulanPicker", {
        dateFormat: "Y-m",
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y-m",
                altFormat: "F Y"
            })
        ]
    });
</script>
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
            // let bulan = $('input[type="text"]').eq(0).val().trim();
            let bulan = $('#bulanPicker').eq(0).val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (bulan === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi bulan sebelum melakukan pencarian!',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterBenangBulanan') ?>",
                type: "GET",
                data: {
                    bulan: bulan
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
                    let total = 0;
                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            let totalUsd = item.kgs_kirim * item.harga;
                            dataTable.row.add([
                                index + 1,
                                item.no_surat_jalan ?? '',
                                item.tgl_masuk ?? '',
                                item.tgl_input ?? '',
                                item.item_type ?? '',
                                item.ukuran ?? '',
                                item.kode_warna ?? '',
                                item.warna ?? '',
                                item.l_m_d ?? '',
                                item.total_karung ?? '',
                                parseFloat(item.gw ?? 0).toFixed(2),
                                parseFloat(item.kgs_kirim ?? 0).toFixed(2),
                                item.harga ?? '',
                                parseFloat(totalUsd ?? '').toFixed(2),
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
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
            // let bulan = $('input[type="text"]').eq(0).val();
            let bulan = $('#bulanPicker').eq(0).val().trim();
            window.location.href = "<?= base_url($role . '/warehouse/exportReportBenangBulanan') ?>?bulan=" + bulan;
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');
        $('input[type="date"]').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });
</script>

<?php $this->endSection(); ?>