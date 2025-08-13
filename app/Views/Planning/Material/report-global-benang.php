<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Filter Global Stock Benang</h5>
                <button class="btn btn-secondary btn-block" id="btnInfo" style="padding: 5px 12px; font-size: 12px;" data-bs-toggle="modal" data-bs-target="#infoModal">
                    <i class="fas fa-info"></i>
                </button>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <label for="">Key</label>
                    <input type="text" class="form-control" placeholder="No Model">
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
            window.location.href = "<?= base_url($role . '/warehouse/exportReportGlobalBenang') ?>?key=" + key;
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
</script>


<?php $this->endSection(); ?>