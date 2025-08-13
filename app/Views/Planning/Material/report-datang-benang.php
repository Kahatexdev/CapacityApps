<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Filter Datang Benang</h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label for="">Key</label>
                    <input type="text" class="form-control" placeholder="No Model/Item Type/Kode Warna/Warna" style="font-size: 11px;">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Awal (Tanggal Datang)</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Akhir (Tanggal Datang)</label>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Foll Up</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Order</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Order Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">KG Pesan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Datang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs Datang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cones Datang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LOT Datang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Surat Jalan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LMD</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">GW</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Harga</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Nama Cluster</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Update</th>
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
                url: "<?= base_url($role . '/warehouse/filterDatangBenang') ?>",
                type: "GET",
                data: {
                    key: key,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
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
                                item.tgl_masuk,
                                parseFloat(item.kgs_kirim ?? 0).toFixed(2),
                                item.cones_kirim,
                                item.lot_kirim,
                                item.no_surat_jalan,
                                item.l_m_d,
                                item.gw_kirim,
                                item.harga,
                                item.nama_cluster,
                                item.keterangan,
                                `<button class="btn btn-warning btn-update" 
                                    data-id_bon="${item.id_bon || ''}" 
                                    data-id_other="${item.id_other_bon || ''}" 
                                    title="Update">
                                    <i class="fa fa-edit"></i>
                                </button>`
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
            let key = $('input[type="text"]').val();
            let tanggal_awal = $('input[type="date"]').eq(0).val();
            let tanggal_akhir = $('input[type="date"]').eq(1).val();
            window.location.href = "<?= base_url($role . '/warehouse/exportDatangBenang') ?>?key=" + key + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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
</script>


<?php $this->endSection(); ?>