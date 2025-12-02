<?php

use PhpParser\Node\Stmt\Echo_;

$this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                                </h5><br>
                                <p> <Strong>Rumus Estimasi SPK :</Strong> <br>((bs + tambahan packing)/Produksi/100 ) * Qty PO</p>
                                <p> <Strong>- Permintaan diatas Pukul 15:00 akan di buka di hari berikutnya. Kecuali Urgent -</p></Strong>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-warning btn-petunjuk" ata-toggle="modal" data-target="#petunjuk">Petunjuk</button>
                            <button id="minta-btn" class="btn btn-info">Minta</button>
                            <!-- <button id="export-btn" class="btn btn-success"><i class="far fa-file-excel"></i></button> -->
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
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Waktu Minta</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php $index = 0; ?>
                                <?php foreach ($perStyle as $item): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="row[<?= $index ?>][selected]" value="1">
                                        </td>
                                        <td class="text-xs"><?= $item['model']; ?></td>
                                        <td class="text-xs"><?= $item['inisial']; ?></td>
                                        <td class="text-xs"><?= $item['size']; ?></td>
                                        <td class="text-xs"><?= $item['jarum']; ?></td>
                                        <td class="text-xs"><?= round($item['qty']); ?> pcs</td>
                                        <td class="text-xs"><?= round($item['ttlProd']); ?> pcs</td>
                                        <td class="text-xs"><?= round($item['sisa']); ?> pcs</td>
                                        <td class="text-xs"><span class="badge bg-info"><?= $item['percentage']; ?>%</span></td>
                                        <td class="text-xs"><?= $item['bs']; ?> pcs</td>
                                        <td class="text-xs">
                                            <input type="number" name="row[<?= $index ?>][poplus]" value="<?= $item['poplus'] ?>" class="form-control"> pcs
                                        </td>
                                        <td class="text-xs">
                                            <input type="number" name="row[<?= $index ?>][estimasi]" value="<?= $item['estimasi'] ?>" class="form-control"> pcs
                                        </td>
                                        <td class="text-xs"><?= $item['status']; ?></td>
                                        <td class="text-xs"><?= $item['waktu']; ?></td>

                                        <!-- Hidden inputs -->
                                        <input type="hidden" name="row[<?= $index ?>][model]" value="<?= $item['model'] ?>">
                                        <input type="hidden" name="row[<?= $index ?>][size]" value="<?= $item['size'] ?>">
                                        <input type="hidden" name="row[<?= $index ?>][area]" value="<?= $area ?>">
                                    </tr>
                                    <?php $index++; ?>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mt-3">

                <div class="">
                    <h3>Form Input SPK 2 Manual</h3>
                </div>

                <!-- Out Celup Section -->
                <div class="row g-3 mt-3">
                    <div class="table-responsive">
                        <table id="poTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">No Model</th>
                                    <th class="text-center">Style Size</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">
                                        <button type="button" class="btn btn-info" id="addRow">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control no_model" name="no_model[]" required>
                                            <option value="">Pilih No Model</option>
                                            <?php foreach ($model as $m): ?>
                                                <option value="<?= $m['mastermodel'] ?>"><?= $m['mastermodel'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control size" name="size[]" required>
                                            <option value="">Pilih Style Size</option>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control" name="qty[]" required></td>
                                    <input type="hidden" class="form-control" name="area[]" value="<?= $area ?>" required>
                                    <td class="text-center">
                                        <!-- Row pertama tidak bisa dihapus -->
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                        <button type="button" id="btnsubmit" class="btn btn-info mt-3">Submit</button>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">
                    <h3>History Permintaan </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="exampleHistory" class="table display hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php $index = 0; ?>
                                <?php foreach ($history as $item): ?>
                                    <tr>
                                        <td class="text-xs"><?= $item['model']; ?></td>
                                        <td class="text-xs"><?= $item['style']; ?></td>
                                        <td class="text-xs"><?= round($item['qty']); ?> pcs</td>
                                        <td class="text-xs">
                                            <?php if ($item['status'] === 'approved'): ?>
                                                <span class="badge bg-success text-white"><?= esc($item['status']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><?= esc($item['status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-xs"><?= $item['updated_at']; ?></td>

                                        <!-- Hidden inputs -->
                                        <input type="hidden" name="row[<?= $index ?>][model]" value="<?= $item['model'] ?>">
                                        <input type="hidden" name="row[<?= $index ?>][style]" value="<?= $item['style'] ?>">
                                        <input type="hidden" name="row[<?= $index ?>][area]" value="<?= $area ?>">
                                    </tr>
                                    <?php $index++; ?>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Inisialisasi DataTable dengan opsi yang diinginkan
        var table = $('#example').DataTable({
            "order": [
                [0, 'desc'] // Urutkan kolom pertama secara descending
            ]
        });
        var tableHistory = $('#exampleHistory').DataTable({
            "order": [
                [4, 'desc'] // Urutkan kolom pertama secara descending
            ]
        });

        // Inisialisasi select2 di row awal
        $('.no_model').select2({
            width: '100%'
        });

        // Handler perubahan No Model → ambil size
        $(document).on('change', '.no_model', function() {
            const $row = $(this).closest('tr');
            const selectedModel = $(this).val();
            const $sizeSelect = $row.find('.size');

            $sizeSelect.html('<option value="">Loading...</option>');

            if (selectedModel) {
                $.ajax({
                    url: '<?= base_url($role . "/getStyleSizeByNoModel") ?>',
                    type: 'POST',
                    data: {
                        no_model: selectedModel
                    },
                    dataType: 'json',
                    success: function(data) {
                        const options = data.map(item =>
                            `<option value="${item.size}">${item.size}</option>`
                        );
                        $sizeSelect.html(`<option value="">Pilih Style Size</option>${options.join('')}`);
                    },
                    error: function() {
                        alert('Gagal mengambil style size. Coba lagi.');
                        $sizeSelect.html('<option value="">Pilih Style Size</option>');
                    }
                });
            } else {
                $sizeSelect.html('<option value="">Pilih Style Size</option>');
            }
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
        $('#minta-btn').on('click', function(e) {
            e.preventDefault();

            var table = $('#example').DataTable(); // Pastikan DataTables sudah di-inisialisasi
            var selectedRows = [];

            // Ambil semua baris yang sedang di-filter/pencarian aktif
            var rows = table.rows({
                search: 'applied'
            }).nodes();

            $('input[type="checkbox"][name^="row"][name$="[selected]"]', rows).each(function() {
                if ($(this).prop('checked')) {
                    let row = $(this).closest('tr');
                    let data = {
                        model: row.find('input[name$="[model]"]').val(),
                        size: row.find('input[name$="[size]"]').val(),
                        area: row.find('input[name$="[area]"]').val(),
                        poplus: row.find('input[name$="[poplus]"]').val(),
                        estimasi: row.find('input[name$="[estimasi]"]').val(),
                    };
                    selectedRows.push(data);
                }
            });

            if (selectedRows.length === 0) {
                alert("Pilih minimal 1 data untuk di-minta.");
                return;
            }

            // Buat form dinamis dan submit POST ke controller
            var form = $('<form>', {
                action: "<?= base_url($role . '/mintaSpk2') ?>",
                method: "POST"
            });

            $.each(selectedRows, function(i, item) {
                form.append(`<input type="hidden" name="data[${i}][model]" value="${item.model}">`);
                form.append(`<input type="hidden" name="data[${i}][size]" value="${item.size}">`);
                form.append(`<input type="hidden" name="data[${i}][area]" value="${item.area}">`);
                form.append(`<input type="hidden" name="data[${i}][poplus]" value="${item.poplus}">`);
                form.append(`<input type="hidden" name="data[${i}][estimasi]" value="${item.estimasi}">`);
            });

            $('body').append(form);
            form.submit();
        });
        $('#export-btn').on('click', function(e) {
            e.preventDefault();

            var table = $('#example').DataTable(); // Pastikan DataTables sudah di-inisialisasi
            var selectedRows = [];

            // Ambil semua baris yang sedang di-filter/pencarian aktif
            var rows = table.rows({
                search: 'applied'
            }).nodes();

            $('input[type="checkbox"][name^="row"][name$="[selected]"]', rows).each(function() {
                if ($(this).prop('checked')) {
                    let row = $(this).closest('tr');
                    let data = {
                        model: row.find('input[name$="[model]"]').val(),
                        size: row.find('input[name$="[size]"]').val(),
                        area: row.find('input[name$="[area]"]').val(),
                        poplus: row.find('input[name$="[poplus]"]').val(),
                        estimasi: row.find('input[name$="[estimasi]"]').val(),
                    };
                    selectedRows.push(data);
                }
            });

            if (selectedRows.length === 0) {
                alert("Pilih minimal 1 data untuk di-export.");
                return;
            }

            // Buat form dinamis dan submit POST ke controller
            var form = $('<form>', {
                action: "<?= base_url($role . '/exportEstimasispk') ?>",
                method: "POST"
            });

            $.each(selectedRows, function(i, item) {
                form.append(`<input type="hidden" name="data[${i}][model]" value="${item.model}">`);
                form.append(`<input type="hidden" name="data[${i}][size]" value="${item.size}">`);
                form.append(`<input type="hidden" name="data[${i}][area]" value="${item.area}">`);
                form.append(`<input type="hidden" name="data[${i}][poplus]" value="${item.poplus}">`);
                form.append(`<input type="hidden" name="data[${i}][estimasi]" value="${item.estimasi}">`);
            });

            $('body').append(form);
            form.submit();
        });

    });
    $('#addRow').on('click', function() {
        const $tbody = $('#poTable tbody');
        const index = $tbody.find('tr').length;
        const area = <?= json_encode($area) ?>

        const newRow = `
            <tr>
                <td>
                    <select class="form-control no_model" name="no_model[]" required>
                        <option value="">Pilih No Model</option>
                        <?php foreach ($model as $m): ?>
                            <option value="<?= $m['mastermodel'] ?>"><?= $m['mastermodel'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <select class="form-control size" name="size[]" required>
                        <option value="">Pilih Style Size</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control" name="qty[]" required>
                </td>
                    <input type="hidden" class="form-control" name="area[]" value="${area}" required>

                <td class="text-center">
                    <button type="button" class="btn btn-danger removeRow">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $tbody.append(newRow);

        // Inisialisasi select2 ulang
        $tbody.find('.no_model').last().select2({
            width: '100%'
        });
    });

    // Hapus row
    $(document).on('click', '.removeRow', function() {
        const $row = $(this).closest('tr');
        const rowCount = $('#poTable tbody tr').length;

        if (rowCount > 1) {
            $row.remove();
        } else {
            alert('Minimal satu baris harus ada.');
        }
    });
    // Panggil pertama kali untuk inisialisasi
    $('#btnsubmit').on('click', function() {
        const rows = $('#poTable tbody tr');
        const data = [];

        let valid = true;

        rows.each(function() {
            const noModel = $(this).find('.no_model').val();
            const size = $(this).find('.size').val();
            const qty = $(this).find('input[name="qty[]"]').val();
            const area = $(this).find('input[name="area[]"]').val();

            if (!noModel || !size || !qty) {
                valid = false;
                return false; // break loop
            }

            data.push({
                no_model: noModel,
                size: size,
                area: area,
                qty: qty
            });
        });

        if (!valid) {
            alert('Mohon lengkapi semua data pada tabel.');
            return;
        }

        // Kirim AJAX ke backend
        $.ajax({
            url: '<?= base_url($role . "/spkmanual") ?>',
            type: 'POST',
            data: {
                items: data
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '<?= session()->getFlashdata('success') ?>',
                    });

                } else {
                    alert('Gagal menyimpan data: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan saat menyimpan data: ' + error);
            }
        });
    });
</script>

<?php $this->endSection(); ?>