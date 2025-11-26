<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>

<style>
    .custom-outline {
        color: #344767;
        border-color: #344767;
    }

    .custom-outline:hover {
        background-color: #344767;
        color: white;
    }
</style>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card border-0 rounded-top-4 shadow-lg">
        <div class="card-body p-4 rounded-top-4" style="background-color: #344767">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-white me-3 fs-4"></i>
                <h4 class="mb-0 fw-bold" style="color: white;">Filter Kebutuhan Bahan Baku</h4>
            </div>
        </div>
        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row gy-4">
                <div class="col-md-4">
                    <label for="">Jenis</label>
                    <select name="jenis" id="jenis" class="form-select">
                        <option value="">PILIH JENIS</option>
                        <option value="BENANG">BENANG</option>
                        <option value="NYLON">NYLON</option>
                        <option value="SPANDEX">SPANDEX</option>
                        <option value="KARET">KARET</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="">Tanggal Awal (Tanggal Delivery Awal)</label>
                    <input type="date" class="form-control" id="tglAwal">
                </div>
                <div class="col-md-4">
                    <label for="">Tanggal Akhir (Tanggal Delivery Awal)</label>
                    <input type="date" class="form-control" id="tglAkhir">
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="btn-group" role="group">
                            <button class="btn text-white px-4" id="btnSearch" style="background-color: #344767">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <button class="btn btn-outline custom-outline" id="btnReset">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                        <button class="btn btn-info d-none" id="btnExport">
                            <i class="fas fa-file-excel me-2"></i>Ekspor
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <!-- card loading -->
            <div class="card loading" id="loadingCard" style="display: none;">
                <div class="card-body text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="display text-center text-uppercase font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <!-- <th class="text-center">No Model</th>
                            <th class="text-center">Buyer</th>
                            <th class="text-center">Foll Up</th> -->
                            <th class="text-center">Item Type</th>
                            <th class="text-center">Warna</th>
                            <!-- <th class="text-center">Delivery Awal</th>
                            <th class="text-center">Delivery Akhir</th> -->
                            <th class="text-center">Total Kebutuhan (Kg)</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
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
            "serverSide": false,
            "columnDefs": [{
                    "className": "text-center",
                    "targets": "_all"
                } // semua kolom di-center
            ]
        });

        function loadData() {
            let jenis = $('#jenis').val().trim();
            let tanggal_awal = $('#tglAwal').val().trim();
            let tanggal_akhir = $('#tglAkhir').val().trim();
            console.log(jenis);
            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (jenis === '' && tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Isi semua filter terlebih dahulu!',
                });
                return;
            }

            $.ajax({
                url: "http://172.23.44.14/MaterialSystem/public/api/filterReportKebutuhanBahanBaku",
                type: "GET",
                data: {
                    jenis: jenis,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loadingCard').show();
                    // disable btn
                    $('#btnSearch').prop('disabled', true);
                    $('#btnReset').prop('disabled', true);
                    $('#btnExport').prop('disabled', true);
                },
                complete: function() {
                    $('#loadingCard').hide();
                    // enable btn
                    $('#btnSearch').prop('disabled', false);
                    $('#btnReset').prop('disabled', false);
                    $('#btnExport').prop('disabled', false);
                },
                success: function(response) {
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                // item.no_model,
                                // item.buyer,
                                // item.foll_up,
                                item.item_type,
                                item.color,
                                // item.delivery_awal,
                                // item.delivery_akhir,
                                parseFloat(item.total_kebutuhan).toFixed(2)
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
            let jenis = $('#jenis').val().trim();
            let tanggal_awal = $('#tglAwal').val().trim();
            let tanggal_akhir = $('#tglAkhir').val().trim();
            window.location.href = "http://172.23.44.14/MaterialSystem/public/api/excelReportKebutuhanBahanBaku?jenis=" + jenis + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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