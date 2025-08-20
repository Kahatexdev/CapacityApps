<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <!-- <h5 class="mb-0 font-weight-bolder">Filter Pengiriman</h5> -->
            </div>
            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="">No Model</label>
                    <input type="text" class="form-control" name="no_model" id="no_model">
                </div>
                <div class="col-md-3">
                    <label for="">Kode Warna</label>
                    <input type="text" class="form-control" name="kode_warna" id="kode_warna">
                </div>
                <div class="col-md-7">
                    <label for="">Aksi</label><br>
                    <button class="btn btn-info btn-block" id="btnSearch"><i class="fas fa-search"></i></button>
                    <button class="btn btn-danger" id="btnReset"><i class="fas fa-redo-alt"></i></button>
                    <!-- selalu tampil -->
                    <!-- <button id="btnExportAll" class="btn btn-success btn-block"><i class="fas fa-file-excel"></i> Export All</button> -->
                    <button class="btn btn-primary" id="btnExportAll"><i class="fas fa-file-excel"></i></button>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cones</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cluster</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--  -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let dataTable = $('#dataTable').DataTable({
            paging: true,
            searching: false,
            ordering: true,
            info: true,
            responsive: true,
            processing: true,
            serverSide: false
        });

        // Ambil data awal pas page load
        loadDefaultData();

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExportAll').click(function() {
            window.location.href = "<?= base_url("$role/warehouse/exportHistoryPindahOrder") ?>";
        });

        $('#btnExport').click(function() {
            const m = $('#no_model').val().trim();
            const k = $('#kode_warna').val().trim();
            window.location.href = "<?= base_url("$role/warehouse/exportHistoryPindahOrder") ?>" +
                "?model=" + encodeURIComponent(m) +
                "&kode_warna=" + encodeURIComponent(k);
        });

        $('#btnReset').click(function() {
            $('#no_model').val('');
            $('#kode_warna').val('');
            $('input[type="date"]').val('');

            loadDefaultData();

            $('#btnExportAll').removeClass('d-none');
            $('#btnExport').addClass('d-none');
        });

        function loadData() {
            let no_model = $('input[name="no_model"]').val().trim();
            let kode_warna = $('input[name="kode_warna"]').val().trim();

            if (no_model === '' && kode_warna === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi minimal salah satu kolom sebelum melakukan pencarian!',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/warehouse/historyPindahOrder') ?>",
                type: "GET",
                data: {
                    model: no_model,
                    kode_warna: kode_warna
                },
                dataType: "json",
                success: function(response) {
                    dataTable.clear().draw();
                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.no_model_old || '-',
                                item.delivery_awal,
                                item.delivery_akhir,
                                item.item_type || '-',
                                item.kode_warna || '-',
                                item.warna || '-',
                                item.kgs,
                                item.cns,
                                item.lot,
                                item.cluster_old,
                                item.created_at + ' ' + item.keterangan + ' ke ' + item.no_model_new + ' kode ' + item.kode_warna
                            ]).draw(false);
                        });

                        // Atur tombol export
                        $('#btnExportAll').addClass('d-none'); // Hilangkan tombol export all
                        $('#btnExport').removeClass('d-none');
                    } else {
                        let colCount = $('#dataTable thead th').length;
                        $('#dataTable tbody').html(`
                            <tr>
                                <td colspan="${colCount}" class="text-center text-danger font-weight-bold">
                                    ⚠ Tidak ada data ditemukan
                                </td>
                            </tr>
                        `);
                        $('#btnExportAll').addClass('d-none'); // Hilangkan tombol export all
                        $('#btnExport').removeClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        };

        function loadDefaultData() {
            $.ajax({
                url: "<?= base_url($role . '/warehouse/historyPindahOrder') ?>",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    dataTable.clear().draw();

                    $.each(response, function(index, item) {
                        dataTable.row.add([
                            index + 1,
                            item.no_model_old || '-',
                            item.delivery_awal,
                            item.delivery_akhir,
                            item.item_type || '-',
                            item.kode_warna || '-',
                            item.warna || '-',
                            item.kgs,
                            item.cns,
                            item.lot,
                            item.cluster_old,
                            item.created_at + ' ' + item.keterangan + ' ke ' + item.no_model_new + ' kode ' + item.kode_warna
                        ]).draw(false);
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }
    });
</script>


<?php $this->endSection(); ?>