<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Filter Sisa Pakai Karet</h5>
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
            <h5 class="mb-3 font-weight-bolder">Tabel Sisa Pakai Karet</h5>
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
                <p><strong>Note:</strong> Rumus Sisa Pakai Karet</p>
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
        function loadData() {
            const delivery_awal = $('#delivery').val();
            const no_model = $('#no_model').val().trim();
            const kode_warna = $('#kode_warna').val().trim();

            if (!delivery_awal && !no_model && !kode_warna) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Tidak Boleh Kosong!',
                    text: 'Silakan isi minimal salah satu input untuk pencarian.',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterSisaPakaiKaret') ?>",
                type: "GET",
                data: {
                    delivery: delivery_awal,
                    no_model: no_model,
                    kode_warna: kode_warna

                },
                dataType: "json",
                success: function(response) {
                    let html = '';
                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            const pakai = parseFloat(item.kgs_out) || 0;
                            const pakaiPlus = parseFloat(item.kgs_out_plus) || 0;
                            const retur = parseFloat(item.kgs_retur) || 0;
                            const pesan = parseFloat(item.kg_po) || 0;
                            const poPlus = parseFloat(item.kg_po_plus) || 0;

                            const sisa = (pakai + pakaiPlus) - retur - (pesan + poPlus);

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.lco_date || ''}</td>
                                    <td>${item.foll_up || ''}</td>
                                    <td>${item.no_model || ''}</td>
                                    <td>${item.no_order || ''}</td>
                                    <td>${item.area_out || ''}</td>
                                    <td>${item.buyer || ''}</td>
                                    <td>${item.start_mc || ''}</td>
                                    <td>${item.delivery_awal || ''}</td>
                                    <td>${item.delivery_akhir || ''}</td>
                                    <td>${item.unit || ''}</td>
                                    <td>${item.item_type || ''}</td>
                                    <td>${item.kode_warna || ''}</td>
                                    <td>${item.warna || ''}</td>
                                    <td>${item.kgs_stock_awal || 0}</td>
                                    <td>${item.lot_awal || ''}</td>
                                    <td>${item.kg_po || 0}</td>
                                    <td>${item.tgl_terima_po_plus || ''}</td>
                                    <td>${item.tgl_po_plus_area || ''}</td>
                                    <td>${item.delivery_po_plus || ''}</td>
                                    <td>${item.kg_po_plus || 0}</td>
                                    <td>${item.kgs_out || 0}</td>
                                    <td>${item.kgs_out_plus || 0}</td>
                                    <td>${item.kgs_retur || 0}</td>
                                    <td>${item.lot_retur || ''}</td>
                                    <td>${sisa.toFixed(2)}</td>
                                </tr>
                            `;
                        });
                        $('#dataTable tbody').html(html);
                        $('#btnExport').removeClass('d-none');
                    } else {
                        $('#dataTable tbody').html('<tr><td colspan="26" class="text-center">Tidak ada data</td></tr>');
                        $('#btnExport').addClass('d-none');
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
            const delivery = $('#delivery').val();
            const no_model = $('#no_model').val().trim();
            const kode_warna = $('#kode_warna').val().trim();
            const url = "<?= base_url($role . '/warehouse/exportReportSisaPakaiKaret') ?>" +
                "?delivery=" + encodeURIComponent(delivery) +
                "&no_model=" + encodeURIComponent(no_model) +
                "&kode_warna=" + encodeURIComponent(kode_warna);

            window.location.href = url;
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');

        // Kosongkan delivery
        $('#delivery').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
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