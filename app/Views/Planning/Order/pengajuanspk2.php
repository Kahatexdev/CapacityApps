<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Pengajuan SPK 2
                                </h5><br>
                                <p> <Strong>Rumus Estimasi SPK :</Strong> <br>((bs + tambahan packing)/Produksi/100 ) * Qty PO</p>
                            </div>
                        </div>
                        <div>
                            <button id="approve-btn" class="btn btn-info">Approve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="display compact " style="width:100%">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 text-center">Tanggal Dibuat</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 text-center">Jam</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 text-center">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Style</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Area</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Qty</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $row) : ?>
                                    <tr>
                                        <td class="text-center"><input type="checkbox" name="row[]" value="<?= $row['id']; ?>"></td>
                                        <td class="text-center"><?= $row['tgl_buat'] ?></td>
                                        <td class="text-center"><?= $row['jam'] ?></td>
                                        <td class="text-center"><?= $row['model'] ?></td>
                                        <td class="text-center"><?= $row['style'] ?></td>
                                        <td class="text-center"><?= $row['area'] ?></td>
                                        <td class="text-center"><?= $row['qty'] ?> pcs</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="display compact " style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 text-center">Tanggal Approve</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 text-center">Jam</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 text-center">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Style</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Area</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Qty</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Status</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $row1) : ?>
                                    <tr>
                                        <td class="text-center"><?= $row1['tgl_buat'] ?></td>
                                        <td class="text-center"><?= $row1['jam'] ?></td>
                                        <td class="text-center"><?= $row1['model'] ?></td>
                                        <td class="text-center"><?= $row1['style'] ?></td>
                                        <td class="text-center"><?= $row1['area'] ?></td>
                                        <td class="text-center"><?= $row1['qty'] ?> pcs</td>
                                        <td class="text-center"><?= $row1['status'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= session()->getFlashdata('success') ?>',
            });
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= session()->getFlashdata('error') ?>',
            });
        });
    </script>
<?php endif; ?>


<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Inisialisasi DataTable dengan opsi yang diinginkan
        var table = $('#example').DataTable({
            "order": [
                [0, 'desc'] // Urutkan kolom pertama secara descending
            ]
        });
        var table1 = $('#example1').DataTable({
            "order": [
                [0, 'desc'] // Urutkan kolom pertama secara descending
            ]
        });

        // Saat klik "select all", pilih semua checkbox pada baris yang tampil (mengikuti order & filter)
        $('#select-all').on('click', function() {
            var rows = table.rows({
                'order': 'applied',
                'search': 'applied'
            }).nodes();
            $('input[name="row[]"]', rows).prop('checked', this.checked);
        });

        // Handler untuk tombol Export
        $('#approve-btn').on('click', function(e) {
            e.preventDefault();

            var table = $('#example').DataTable();
            var selected = [];
            // Mengambil semua baris dari seluruh halaman yang sesuai dengan pencarian (applied search)
            var rows = table.rows({
                search: 'applied'
            }).nodes();
            $('input[name="row[]"]', rows).each(function() {
                if ($(this).prop('checked')) {
                    selected.push($(this).val());
                }
            });

            if (selected.length === 0) {
                alert("Pilih minimal 1 data untuk di-export.");
                return;
            }
            console.log(selected);
            // Kirim data dengan AJAX POST
            // Buat form tersembunyi dan submit data via POST
            var form = $('<form action="<?= base_url($role . '/approveSpk2') ?>" method="POST"></form>');
            // Masukkan data checkbox ke dalam form
            $.each(selected, function(index, value) {
                form.append('<input type="hidden" name="data[]" value="' + value + '">');
            });

            // Tambahkan form ke body dan submit
            $('body').append(form);
            form.submit();
        });

    });
</script>

<?php $this->endSection(); ?>