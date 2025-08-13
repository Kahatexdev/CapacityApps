<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <!-- <h5 class="mb-0 font-weight-bolder">Filter Pengiriman</h5> -->
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
                url: "<?= base_url($role . '/warehouse/filterReportGlobal') ?>",
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
                            const kgsStockAwal = Number(item.kgs_stock_awal) || 0;
                            const kgsKirim = Number(item.kgs_kirim) || 0;
                            const kgsRetur = Number(item.kgs_retur) || 0;
                            const kgsOut = Number(item.kgs_out) || 0;
                            const kgsInOut = Number(item.kgs_in_out) || 0;
                            const kgsOtherOut = Number(item.kgs_other_out) || 0;
                            const loss = Number(item.loss) || 0;

                            // perhitungan
                            const tagihanGbn = kgs - (kgsKirim + kgsStockAwal);
                            const jatahArea = kgs - kgsOut;

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
                                '-', // qty po (+)
                                fmt(kgsStockAwal), // stock awal
                                '-', // stock opname
                                fmt(kgsKirim), // datang solid
                                '-', // (+) datang solid
                                '-', // ganti retur
                                '-', // datang lurex
                                '-', // (+) datang lurex
                                '-', // retur pb gbn
                                fmt(kgsRetur), // retur pb area
                                fmt(kgsOut), // pakai area 
                                fmt(kgsOtherOut), // pakai lain-lain
                                '-', // retur stock
                                '-', // retur titip
                                '-', // dipinjam
                                '-', // pindah order
                                '-', // pindah ke stock mati
                                fmt(kgsInOut), // stock akhir
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
            let tanggal_awal = $('input[type="date"]').eq(0).val();
            let tanggal_akhir = $('input[type="date"]').eq(1).val();
            window.location.href = "<?= base_url($role . '/warehouse/exportGlobalReport') ?>?key=" + key;
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