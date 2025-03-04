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
                                    Estimasi SPK 2 di Area <?= $area ?>
                                </h5>
                                <p> <Strong>Rumus Estimasi SPK :</Strong> <br>((bs + tambahan packing)/Produksi/100 ) * Qty PO
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-warning btn-petunjuk" ata-toggle="modal" data-target="#petunjuk">Petunjuk</button>
                            <button id="export-btn" class="btn btn-success">Export Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="petunjuk" tabindex="-1" role="dialog" aria-labelledby="petunjuk" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Petunjuk Permintaan SPK <Area:d></Area:d>
                        </h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ol>
                            <li>Pilih PDK serta Style yang akan diminta</li>
                            <li>Klik Tombol Export</li>
                            <li>Sesuaikan QTY dengan kebutuhan</li>
                            <li>Jika ada pdk yang tidak muncul di sistem, anda dapat mengisi secara manual pada excel</li>
                            <li>Simpan File lalu kirimkan melalui email ke : me.sock@bd.kaha.com</li>
                        </ol>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>

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
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Inisial</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Produksi</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Presentase Produksi</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">BS Stocklot</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">PO+</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Estimasi SPK 2</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($perStyle as $item): ?>
                                    <tr>
                                        <td><input type="checkbox" name="row[]" value="<?= $item['model'] . '|' . $item['size'] . '|' . $area; ?>"></td>
                                        <td class="text-xs"><?= $item['model']; ?></td>
                                        <td class="text-xs"><?= $item['inisial']; ?></td>
                                        <td class="text-xs"><?= $item['size']; ?></td>
                                        <td class="text-xs"><?= $item['jarum']; ?></td>
                                        <td class="text-xs"><?= round($item['qty']); ?> pcs</td>
                                        <td class="text-xs"><?= round($item['ttlProd']); ?>pcs</td>
                                        <td class="text-xs"><?= round($item['sisa']); ?>pcs</td>
                                        <td class="text-xs"><span class="badge bg-info"><?= $item['percentage']; ?>% </span></td>
                                        <td class="text-xs"><?= $item['bs']; ?>pcs</td>
                                        <td class="text-xs"><?= $item['poplus']; ?>pcs</td>
                                        <td class="text-xs"><?= $item['estimasi']; ?>pcs</td>
                                    <?php endforeach ?>
                                    </tr>
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
        $('.btn-petunjuk').click(function() {
            $('#petunjuk').modal('show'); // Show the modal
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
        $('#export-btn').on('click', function(e) {
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
            var form = $('<form action="<?= base_url($role . '/exportEstimasispk') ?>" method="POST"></form>');
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