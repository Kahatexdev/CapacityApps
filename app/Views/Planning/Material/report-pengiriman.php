<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Filter Pengiriman</h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label for="">Key</label>
                    <input type="text" class="form-control" placeholder="PDK / Item Type / Kode Warna dan Warna">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Awal (Tanggal Keluar)</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Akhir (Tanggal Keluar)</label>
                    <input type="date" class="form-control">
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs Pesan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Keluar</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs Kirim</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cones Kirim</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Karung Kirim</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LOT Kirim</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Nama Cluster</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
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
            let tanggal_awal = $('input[type="date"]').eq(0).val().trim();
            let tanggal_akhir = $('input[type="date"]').eq(1).val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (key === '' && tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi minimal salah satu kolom sebelum melakukan pencarian!',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterPengiriman') ?>",
                type: "GET",
                data: {
                    key: key,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
                success: function(response) {
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.no_model,
                                item.area_out,
                                item.delivery_awal,
                                item.delivery_akhir,
                                item.item_type,
                                item.kode_warna,
                                item.color,
                                item.kgs_pesan,
                                item.tgl_out,
                                item.kgs_pakai,
                                item.cones_pakai,
                                item.krg_pakai,
                                item.lot_pakai,
                                item.nama_cluster,
                                item.keterangan_gbn,
                                item.admin
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
            window.location.href = "<?= base_url($role . '/warehouse/exportPengiriman') ?>?key=" + key + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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