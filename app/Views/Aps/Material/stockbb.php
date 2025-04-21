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
                                    Cek Stock Bahan Baku
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">

                            <button class="btn btn-info ml-auto btn-stock">Cek Stok</button>
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
                           <td>${item.masuk ?? '0'} kg</td>
                           <td>${item.keluar ?? '0'} kg</td>
                           <td>${item.stock} kg</td>
                    
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