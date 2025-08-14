<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Report Global Nylon</h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label for="">No Model</label>
                    <input type="text" class="form-control">
                </div>
                <div class="col-md-3">
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Loss</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty PO</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty PO(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stock Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stock Opname</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Datang Solid</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">(+) Datang Solid</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Ganti Retur</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Datang Lurex</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">(+) Datang Lurex</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Retur PB GBN</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Retur PB Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pakai Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pakai Lain-Lain</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Retur Stock</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Retur Titip</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Dipinjam</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pindah Order</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pindah Ke Stock Mati</th>
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

        function loadData() {
            let key = $('input[type="text"]').val().trim();

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
                url: "<?= base_url($role . '/warehouse/filterReportGlobalNylon') ?>",
                type: "GET",
                data: {
                    key: key
                },
                dataType: "json",
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
            let jenis = 'NYLON';
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
        $('input[type="date"]').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });
</script>


<?php $this->endSection(); ?>