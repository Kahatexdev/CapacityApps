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
                <h5 class="mb-0 font-weight-bolder">Filter Datang Benang</h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label for="">Key</label>
                    <input type="text" class="form-control" id="key" placeholder="No Model/Item Type/Kode Warna/Warna/Lot" style="font-size: 11px;">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Awal (Tanggal Datang)</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Akhir (Tanggal Datang)</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <div class="form-check mb-0">
                        <label class="form-check-label" for="po_plus">
                            PO(+)
                        </label>
                        <input class="form-check-input" type="checkbox" id="po_plus">
                    </div>
                </div>
                <div class="col-md-2">
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
                <table id="dataTable" class="display text-center text-uppercase" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Foll Up</th>
                            <th class="text-center">No Model</th>
                            <th class="text-center">No Order</th>
                            <th class="text-center">Buyer</th>
                            <th class="text-center">Delivery Awal</th>
                            <th class="text-center">Delivery Akhir</th>
                            <th class="text-center">Order Type</th>
                            <th class="text-center">Item Type</th>
                            <th class="text-center">Kode Warna</th>
                            <th class="text-center">Warna</th>
                            <th class="text-center">KG Pesan</th>
                            <th class="text-center">Tanggal Datang</th>
                            <th class="text-center">Kgs Datang</th>
                            <th class="text-center">Cones Datang</th>
                            <th class="text-center">LOT Datang</th>
                            <th class="text-center">No Surat Jalan</th>
                            <th class="text-center">LMD</th>
                            <th class="text-center">GW</th>
                            <th class="text-center">Harga</th>
                            <th class="text-center">Nama Cluster</th>
                            <th class="text-center">Keterangan</th>
                            <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Update</th> -->
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- modal update keterangan bon -->
<div class="modal fade" id="modalUpdate" tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateLabel">Update Keterangan Datang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalIdBon">
                <input type="hidden" id="modalIdOther">

                <div class="mb-3">
                    <label for="keteranganDatang" class="form-label">Keterangan Datang</label>
                    <textarea class="form-control" id="keteranganDatang" rows="4" placeholder="Tulis keterangan datang..."></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="btnSubmitKeterangan">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let dataTable = $('#dataTable').DataTable({
            "paging": true,
            "searching": true,
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
            let key = $('#key').val().trim();
            let tanggal_awal = $('input[type="date"]').eq(0).val().trim();
            let tanggal_akhir = $('input[type="date"]').eq(1).val().trim();
            let po_plus = $('#po_plus').is(':checked') ? 1 : 0;
            // Log data yang akan dikirim
            let dataToSend = {
                key: key,
                tanggal_awal: tanggal_awal,
                tanggal_akhir: tanggal_akhir,
                po_plus: po_plus
            };
            console.log('Data yang dikirim ke server:', dataToSend);
            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (key === '' && tanggal_awal === '' && tanggal_akhir === '' && po_plus === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi minimal salah satu kolom sebelum melakukan pencarian!',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterDatangBenang') ?>",
                type: "GET",
                data: {
                    key: key,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    po_plus: po_plus
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
                    console.log(response);
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            let poPlus = (item.po_plus === "1") ? "Ya" : "";
                            dataTable.row.add([
                                index + 1,
                                item.foll_up,
                                item.no_model,
                                item.no_order,
                                item.buyer,
                                item.delivery_awal,
                                item.delivery_akhir,
                                item.unit,
                                item.item_type,
                                item.kode_warna,
                                item.warna,
                                parseFloat(item.kgs_material ?? 0).toFixed(2),
                                item.tgl_datang,
                                parseFloat(item.kgs_kirim ?? 0).toFixed(2),
                                item.cones_kirim,
                                item.lot_kirim,
                                item.no_surat_jalan,
                                item.l_m_d,
                                parseFloat(item.gw_kirim ?? 0).toFixed(2),
                                item.harga,
                                item.nama_cluster,
                                poPlus,
                                item.keterangan,
                                // `<button class="btn btn-warning btn-update" 
                                //     data-id_bon="${item.id_bon || ''}" 
                                //     data-id_other="${item.id_other_bon || ''}" 
                                //     title="Update">
                                //     <i class="fa fa-edit"></i>
                                // </button>`
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
        $('#dataTable').on('click', '.btn-update', function() {
            const idBon = $(this).data('id_bon');
            const idOther = $(this).data('id_other');

            console.log('INI' + idBon);

            // Masukkan ke input hidden
            $('#modalIdBon').val(idBon);
            $('#modalIdOther').val(idOther);

            // Kosongkan sementara textarea
            $('#keteranganDatang').val('');

            // AJAX untuk ambil keterangan sebelumnya
            $.ajax({
                url: '<?= base_url($role . "/warehouse/getKeteranganDatang") ?>',
                type: 'GET',
                data: {
                    id_bon: idBon,
                    id_other_bon: idOther
                },
                dataType: 'json',
                success: function(response) {
                    $('#keteranganDatang').val(response.keterangan ?? '');
                    $('#modalUpdate').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal mengambil data keterangan.'
                    });
                }
            });
        });

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExport').click(function() {
            let key = $('#key').val();
            let tanggal_awal = $('input[type="date"]').eq(0).val();
            let tanggal_akhir = $('input[type="date"]').eq(1).val();
            let po_plus = $('#po_plus').is(':checked') ? 1 : 0;
            window.location.href = "<?= base_url($role . '/warehouse/exportDatangBenang') ?>?key=" + key + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&po_plus=" + po_plus;
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('#key').val('');
        $('input[type="date"]').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });
    $('#btnSubmitKeterangan').on('click', function() {
        const idBon = $('#modalIdBon').val();
        const idOther = $('#modalIdOther').val();
        const keterangan = $('#keteranganDatang').val();

        $.ajax({
            url: '<?= base_url($role . "/warehouse/updateKeteranganDatang") ?>',
            type: 'POST',
            data: {
                id_bon: idBon,
                id_other_bon: idOther,
                keterangan: keterangan
            },
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Keterangan berhasil diperbarui.'
                });

                $('#modalUpdate').modal('hide');
                loadData(); // Reload tabel
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan.'
                });
            }
        });
    });

    // Trigger pencarian saat tombol Enter ditekan di input apa pun
    $('#key, input[type="date"]').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Hindari form submit default (jika ada form)
            $('#btnSearch').click(); // Trigger tombol Search
        }
    });
</script>


<?php $this->endSection(); ?>