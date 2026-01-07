<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid">
    <div class="row mt-1">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Tabel Data Karyawan
                    </h4>
                    <div class="table-responsive">
                        <table id="karyawanTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <!-- <th>No</th> -->
                                <th>NIK</th>
                                <th>Kode Kartu</th>
                                <th>Nama Karyawan</th>
                                <th>Shift</th>
                                <th>Warna Baju</th>
                                <th>Bagian</th>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable with export options
        $('#karyawanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: HrisUrl + 'karyawan/dataMontir',
                type: "POST",
            },
            columns: [
                { data: 'nik' },
                { data: 'kode_kartu' },
                { data: 'nama_karyawan' },
                { data: 'shift' },
                { data: 'warna_baju' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `${data.nama_bagian} - ${data.area} - ${data.area_utama}`;
                    }
                }
            ]
        });



        // Flash message SweetAlerts
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: '<?= session()->getFlashdata('success') ?>',
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: '<?= session()->getFlashdata('error') ?>',
            });
        <?php endif; ?>
    });
</script>
<?php $this->endSection(); ?>