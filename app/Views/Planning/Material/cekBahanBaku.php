<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
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
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Status Bahan Baku <?= $model ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">

                            <button class="btn btn-info ml-auto btn-stock">Cek Stok</button>
                            <a href="<?= base_url($role . '/planningpage/' . $idDetail . '/' . $idPln) ?>" class="btn btn-secondary  ml-auto">Back</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row mt-2 stockSection d-none">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="header">
                        Actual Stock GBN
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row stockTable">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (empty($material)): ?>
        <div class="alert alert-warning text-center">
            Data bahan baku belum tersedia di sistem.
        </div>
    <?php else: ?>
        <?php foreach ($material as $item): ?>
            <?php
            // Mapping status ke class badge yang sesuai
            $statusClasses = [
                'done' => 'bg-gradient-success',
                'retur' => 'bg-gradient-warning',
                'sent' => 'bg-gradient-success',
            ];
            $statusClass = $statusClasses[$item['last_status']] ?? 'bg-gradient-info';
            ?>

            <div class="row my-3 mx-2">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="text-header mb-0">
                            <?= esc($item['item_type']) ?>
                        </h5>
                        <span class="badge <?= $statusClass ?> text-sm">
                            <?= esc($item['last_status']) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <span class=" d-block"><strong>Kode Warna:</strong> <?= esc($item['kode_warna']) ?></span>
                                <span class=" d-block"><strong>Warna:</strong> <?= esc($item['color']) ?></span>
                                <span class=" d-block"><strong>Lot Celup:</strong> <?= esc($item['lot_celup']) ?></span>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block"><strong>Qty PO:</strong> <?= number_format((float) $item['qty_po'], 2, ',', '.') ?></span>
                                <span class="d-block"><strong>Qty Celup:</strong> <?= number_format((float) $item['kg_celup'], 2, ',', '.') ?></span>
                                <span class=" d-block"><strong>Start MC:</strong>
                                    <?= !empty($item['start_mc']) ? date_create($item['start_mc'])->format('d M') : '-' ?>
                                </span>
                                <span class=" d-block"><strong>Tgl Schedule:</strong>
                                    <?= !empty($item['tanggal_schedule']) ? date_create($item['tanggal_schedule'])->format('d M') : '-' ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <span class=" d-block"><strong>Tgl Bon:</strong>
                                    <?= !empty($item['tanggal_bon']) ? date_create($item['tanggal_bon'])->format('d M') : '-' ?>
                                </span>
                                <span class=" d-block"><strong>Tgl celup:</strong>
                                    <?= !empty($item['tanggal_celup']) ? date_create($item['tanggal_celup'])->format('d M') : '-' ?>
                                </span>
                                <span class=" d-block"><strong>Tgl bongkar:</strong>
                                    <?= !empty($item['tanggal_bongkar']) ? date_create($item['tanggal_bongkar'])->format('d M') : '-' ?>
                                </span>
                                <span class=" d-block"><strong>Tgl press:</strong>
                                    <?= !empty($item['tanggal_press']) ? date_create($item['tanggal_press'])->format('d M') : '-' ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <span class=" d-block"><strong>Tgl oven:</strong>
                                    <?= !empty($item['tanggal_oven']) ? date_create($item['tanggal_oven'])->format('d M') : '-' ?>
                                </span>
                                <span class=" d-block"><strong>Tgl tl:</strong>
                                    <?= !empty($item['tanggal_tl']) ? date_create($item['tanggal_tl'])->format('d M') : '-' ?>
                                </span>
                                <span class=" d-block"><strong>Tgl rajut_pagi:</strong>
                                    <?= !empty($item['tanggal_rajut_pagi']) ? date_create($item['tanggal_rajut_pagi'])->format('d M') : '-' ?>
                                </span>
                                <span class=" d-block"><strong>Tgl acc:</strong>
                                    <?= !empty($item['tanggal_acc']) ? date_create($item['tanggal_acc'])->format('d M') : '-' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-3">
                                <span class=" d-block"><strong>Tgl kelos:</strong>
                                    <?= !empty($item['tanggal_kelos']) ? date_create($item['tanggal_kelos'])->format('d M') : '-' ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <span class=" d-block"><strong>Tgl reject:</strong>
                                    <?= !empty($item['tanggal_reject']) ? date_create($item['tanggal_reject'])->format('d M') : '-' ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <span class=" d-block"><strong>Tgl perbaikan:</strong>
                                    <?= !empty($item['tanggal_perbaikan']) ? date_create($item['tanggal_perbaikan'])->format('d M') : '-' ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <span class=" d-block"><strong>Ket:</strong> <?= esc($item['ket_daily_cek']) ?></span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
<div class="row my-3">

</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let tombol = document.querySelectorAll(".btn-stock");

        tombol.forEach(btn => {
            btn.addEventListener("click", function() {
                let stockSection = document.querySelector(".stockSection");

                let model = <?= json_encode($model); ?>; // Ganti dengan nilai sebenarnya

                $.ajax({
                    url: '<?= base_url($role . '/cekStok') ?>', // Ganti dengan URL API yang benar
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        model: model
                    },
                    success: function(response) {
                        if (response) {
                            let tableStock = `
        <table id="planTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Benang</th>
                    <th>Kode Warna</th>
                    <th>Warna</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                ${response.map(item => `
                    <tr>
                        <td>${item.item_type}</td>
                                           <td>${item.kode_warna} </td>
                           <td>${item.color} </td>
                           <td>${Number(item.masuk).toFixed(2)} kg</td>
                <td>${Number(item.keluar).toFixed(2)} kg</td>
                <td>${Number(item.stock).toFixed(2)} kg</td>
                    
                    </tr>
                `).join('')}
            </tbody>
        </table>
          
    `;

                            document.querySelector(".stockTable").innerHTML = tableStock;
                            stockSection.classList.toggle("d-none");
                            $('#planTable').DataTable({
                                paging: true, // Pagination aktif
                                searching: true, // Bisa cari data
                                ordering: true, // Bisa sort kolom
                                lengthMenu: [
                                    [5, 10, 25, -1],
                                    [5, 10, 25, "All"]
                                ], // Dropdown jumlah data
                                language: {
                                    search: "Cari:",
                                    lengthMenu: "Tampilkan _MENU_ data",
                                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                                    paginate: {
                                        previous: "Sebelumnya",
                                        next: "Berikutnya"
                                    }
                                }
                            });

                            console.log('Data berhasil diambil:', response);
                        } else {
                            console.error('Error: Response format invalid.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            });
        });
    });
</script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>

<?php $this->endSection(); ?>