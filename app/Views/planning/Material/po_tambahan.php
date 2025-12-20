<?php $this->extend($role . '/layout'); ?>
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

    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div class="col-7">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                List PO Tambahan
                            </h5>
                        </div>
                        <div class="col-5 d-flex align-items-center text-end gap-2">
                            <select name="area" id="area" class="form-control" required>
                                <option value="">Pilih Area</option>
                                <?php foreach ($areas as $ar) : ?>
                                    <option value="<?= $ar ?>"><?= $ar ?></option>
                                <?php endforeach ?>
                            </select>
                            <input type="text" class="form-control" id="no_model" value="" placeholder="No Model">
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
                                <!-- <button id="generatePdfBtn" class="btn btn-danger">Export PDF</button> -->
                                <button id="generateExcelBtn" class="btn btn-success">Export Excel</button>
                            </div>
                        </div>

                        <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal PO(+)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style Size</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Item_Type</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode Warna</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Color</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty (Pcs)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kgs</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Terima (Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa BB_Mesin(Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Order(Pcs)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">BS Mesin(Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">BS Setting(Pcs)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Mesin (Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Mesin (Cns)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Packing (Pcs)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Packing (Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Packing (Cns)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Lebih Pakai(Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal export pdf -->
<div class="modal fade" id="exportPdf" tabindex="-1" role="dialog" aria-labelledby="exportPdf" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export PDF</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body align-items-center">
                <div class="form-group">
                    <label for="area" class="col-form-label">Tanggal Buka PO(+)</label>
                    <input type="date" class="form-control" id="tgl_buat">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="generatePdfBtn" class="btn bg-gradient-info">Generate</button>
                <button type="button" id="generateExcelBtn" class="btn bg-gradient-success">Generate Excel</button>
            </div>
        </div>
    </div>
</div>

<script src=" <?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    // SweetAlert untuk menampilkan pesan sukses/gagal
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil flashdata dari server dan pastikan nilainya berupa string
        const successMessage = "<?= htmlspecialchars(is_string(session()->getFlashdata('success')) ? session()->getFlashdata('success') : '') ?>";
        const errorMessage = "<?= htmlspecialchars(is_string(session()->getFlashdata('error')) ? session()->getFlashdata('error') : '') ?>";

        // Tampilkan SweetAlert jika ada pesan sukses
        if (successMessage && successMessage.trim() !== "") {
            Swal.fire({
                title: "Berhasil!",
                text: successMessage,
                icon: "success",
                confirmButtonText: "OK"
            });
        }

        // Tampilkan SweetAlert jika ada pesan gagal
        if (errorMessage && errorMessage.trim() !== "") {
            Swal.fire({
                title: "Gagal!",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "OK"
            });
        }
    });
</script>
<script type="text/javascript">
    let btnSearch = document.getElementById('searchFilter');

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

    btnSearch.onclick = function() {
        let area = document.getElementById('area').value;
        let model = document.getElementById('no_model').value;
        let tglBuat = document.getElementById('tgl_po').value;
        let role = <?= json_encode($role) ?>;
        let info = document.getElementById('info');

        // Cek apakah tgl_po kosong
        if (!tglBuat) {
            alert("Silakan isi tanggal PO terlebih dahulu!");
            return; // Stop eksekusi
        }

        if (!area) {
            alert("Silakan pilih area terlebih dahulu!");
            return; // Stop eksekusi
        }

        console.log("Area: " + area);
        console.log("Model: " + model);
        console.log("Tgl PO: " + tglBuat);

        $.ajax({
            url: "<?= base_url($role . '/filter_list_potambahan/') ?>" + area,
            type: "GET",
            data: {
                model: model,
                tglBuat: tglBuat
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
                fethcData(response, model, tglBuat, area);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            },
            complete: function() {
                updateProgress(100); // pastikan full
                setTimeout(() => hideLoading(), 400); // kasih jeda biar animasi progress keliatan
            }
        });
    };

    function fethcData(data, model, tglBuat, area) {
        $('#tgl_buat').val(tglBuat);

        let dataTable = $('#dataTable').DataTable();
        dataTable.clear(); // Hapus semua data sebelumnya

        if (data.length === 0) {
            $('#headerModel').hide();

            const node = dataTable.row.add(['']).draw().node(); // Tambahkan satu kolom kosong
            const $td = $(node).find('td').first(); // Ambil td pertama

            $td
                .attr('colspan', 16)
                .html(`Data tidak ditemukan untuk model: <strong>${model}</strong>, area: <strong>${area}</strong> & tgl Po: <strong>${tglBuat}</strong>`)
                .css({
                    'text-align': 'center',
                    'font-style': 'italic',
                    'color': '#a71d2a',
                    'background-color': '#ffe5e5'
                });

            // Hapus sisa kolom jika ada lebih dari 1 (buat jaga-jaga)
            $(node).find('td:gt(0)').remove();

            return;
        }


        // ✅ Tampilkan header dan isi span table-header
        $('#headerModel').css('display', 'flex');
        $('#table-header').text(tglBuat);

        let no = 1;
        data.forEach(item => {
            dataTable.row.add([
                no++,
                item.created_at || '',
                item.no_model || '',
                item.style_size || '',
                item.item_type || '',
                item.kode_warna || '',
                item.color || '',
                item.qty_pcs || 0,
                item.kgs || 0,
                item.terima_kg || 0,
                item.sisa_bb_mc || 0,
                item.sisa_order_pcs || 0,
                item.bs_mesin_kg || 0,
                item.bs_st_pcs || 0,
                item.poplus_mc_kg || 0,
                item.poplus_mc_cns || 0,
                item.plus_pck_pcs || 0,
                item.plus_pck_kg || 0,
                item.plus_pck_cns || 0,
                item.lebih_pakai_kg || 0,
                item.keterangan || '',
            ]);
        });

        dataTable.draw(); // Refresh DataTables
    }

    $(document).ready(function() {
        $('#dataTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{
                    targets: '_all',
                    defaultContent: ''
                } // agar baris dinamis tidak error
            ]
        });
    });

    $(document).ready(function() {
        $('#exportPdfBtn').on('click', function() {
            $('#exportPdf').modal('show');
        });
    });

    $('#generatePdfBtn').on('click', function() {
        const area = $('#area').val();
        const model = $('#no_model').val();
        const tglBuat = $('#tgl_buat').val();
        const role = <?= json_encode($role) ?>;

        // if (!tglBuat) {
        //     alert("Silakan isi No Model terlebih dahulu.");
        //     return;
        // }

        const url = "<?= base_url($role . '/generate_po_tambahan') ?>" +
            "?area=" + encodeURIComponent(area) +
            "&model=" + encodeURIComponent(model) +
            "&tgl_buat=" + encodeURIComponent(tglBuat);

        window.open(url, '_blank');
    });

    $('#generateExcelBtn').on('click', function() {
        const area = $('#area').val();
        const model = $('#no_model').val();
        const tglBuat = $('#tgl_buat').val();
        const role = <?= json_encode($role) ?>;

        // if (!tglBuat) {
        //     alert("Silakan isi No Model terlebih dahulu.");
        //     return;
        // }

        const url = "<?= base_url($role . '/generate_excel_po_tambahan') ?>" +
            "?area=" + encodeURIComponent(area) +
            "&model=" + encodeURIComponent(model) +
            "&tgl_buat=" + encodeURIComponent(tglBuat);

        window.open(url, '_blank');
    });
</script>
<?php $this->endSection(); ?>