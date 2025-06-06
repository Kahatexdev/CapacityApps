<?php $this->extend('User/layout'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                    <div class="d-flex justify-content-between">
                        <div class="col-9">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                List PO Tambahan <?= $area ?>
                            </h5>
                        </div>
                        <div class="col-3 d-flex align-items-center text-end gap-2">
                            <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                            <input type="text" class="form-control" id="no_model" value="" placeholder="No Model" required>
                            <button id="searchModel" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
                            <button class="btn btn-info ms-2">
                                <a href="<?= base_url($role . '/form-potambahan/' . $area) ?>" class="fa fa-list text-white" style="text-decoration: none;"> List</a>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="headerModel" class="table-header" style="display: none; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h4 style="margin: 0;"><span id="no-model"></span></h4>
                            <button id="exportPdfBtn" class="btn btn-danger">Export PDF</button>
                        </div>

                        <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal PO(+)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style Size</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Item Type</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode Warna</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Color</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Pcs PO(+)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kg PO(+)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src=" <?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    // SweetAlert untuk menampilkan pesan sukses/gagal
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil flashdata dari server dan pastikan nilainya berupa string
        const successMessage = "<?= htmlspecialchars(is_string(session()->getFlashdata('success')) ? session()->getFlashdata('success') : '') ?>";
        const errorMessage = "<?= htmlspecialchars(is_string(session()->getFlashdata('error')) ? session()->getFlashdata('error') : '') ?>";

        // Tampilkan SweetAlert jika ada pesan sukses
        if (successMessage && successMessage.trim() !== "") {
            Swal.fire({
                title: "Berhasil!",
                text: successMessage,
                icon: "success",
                confirmButtonText: "OK"
            });
        }

        // Tampilkan SweetAlert jika ada pesan gagal
        if (errorMessage && errorMessage.trim() !== "") {
            Swal.fire({
                title: "Gagal!",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "OK"
            });
        }
    });
</script>
<script type="text/javascript">
    let btnSearch = document.getElementById('searchModel');

    btnSearch.onclick = function() {
        let area = document.getElementById('area').value;
        let model = document.getElementById('no_model').value;
        let role = <?= json_encode($role) ?>;
        let info = document.getElementById('info');

        console.log("Area: " + area);
        console.log("Model: " + model);

        $.ajax({
            url: "<?= base_url($role . '/filter_list_potambahan/') ?>" + area,
            type: "GET",
            data: {
                model: model
            },
            dataType: "json",
            success: function(response) {
                fethcData(response, model, area);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            },
        });
    };

    function fethcData(data, model, area) {
        let dataTable = $('#dataTable').DataTable();
        dataTable.clear(); // Hapus semua data sebelumnya

        if (data.length === 0) {
            $('#headerModel').hide();

            const node = dataTable.row.add(['']).draw().node(); // Tambahkan satu kolom kosong
            const $td = $(node).find('td').first(); // Ambil td pertama

            $td
                .attr('colspan', 11)
                .html(`Data tidak ditemukan untuk model: <strong>${model}</strong>`)
                .css({
                    'text-align': 'center',
                    'font-style': 'italic',
                    'color': '#a71d2a',
                    'background-color': '#ffe5e5'
                });

            // Hapus sisa kolom jika ada lebih dari 1 (buat jaga-jaga)
            $(node).find('td:gt(0)').remove();

            return;
        }


        // ✅ Tampilkan header dan isi span no-model
        $('#headerModel').css('display', 'flex');
        $('#no-model').text(model);

        let no = 1;
        data.forEach(item => {
            dataTable.row.add([
                no++,
                item.created_at || '',
                item.no_model || '',
                item.style_size || '',
                item.item_type || '',
                item.kode_warna || '',
                item.color || '',
                item.pcs_po_tambahan || 0,
                item.kg_po_tambahan || 0,
                item.keterangan || '',
            ]);
        });

        dataTable.draw(); // Refresh DataTables
    }

    $(document).ready(function() {
        $('#dataTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{
                    targets: '_all',
                    defaultContent: ''
                } // agar baris dinamis tidak error
            ]
        });
    });

    $('#exportPdfBtn').on('click', function() {
        const area = $('#area').val();
        const model = $('#no_model').val();
        const role = <?= json_encode($role) ?>;

        if (!model) {
            alert("Silakan isi No Model terlebih dahulu.");
            return;
        }

        const url = "<?= base_url($role . '/generate_po_tambahan') ?>" +
            "?area=" + encodeURIComponent(area) +
            "&model=" + encodeURIComponent(model);

        window.open(url, '_blank');
    });
</script>
<?php $this->endSection(); ?>