<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Filter Benang Per Minggu</h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <label for="">Tanggal Awal</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="">Tanggal Akhir</label>
                    <input type="date" class="form-control">
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
            let tanggal_awal = $('input[type="date"]').eq(0).val().trim();
            let tanggal_akhir = $('input[type="date"]').eq(1).val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi tanggal sebelum melakukan pencarian!',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterBenangMingguan') ?>",
                type: "GET",
                data: {
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
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
            window.location.href = "<?= base_url($role . '/warehouse/exportReportBenangMingguan') ?>?tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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