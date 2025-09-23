<?php $this->extend('user/layout'); ?>
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
                        <div class="col-7">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                List PO Tambahan <?= $area ?>
                            </h5>
                            <p style="color: red; font-size: 12px;">*Filter tgl po tambahan terlebih dahulu!</p>
                        </div>
                        <div class="col-5 d-flex align-items-center text-end gap-2">
                            <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                            <label for="no_model">No Model</label>
                            <input type="text" class="form-control" id="no_model" value="" placeholder="No Model">
                            <label for="tgl_po">Tgl Po Tambahan</label>
                            <input type="date" class="form-control" id="tgl_po" value="" required>
                            <button id="searchFilter" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
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
                            <h4 style="margin: 0;"><span id="table-header"></span></h4>

                            <div class="d-flex align-items-center  gap-3">
                                <!-- <button id="exportPdfBtn" class="btn btn-danger">Export PDF</button> -->
                                <input type="hidden" id="tgl_buat" name="tgl_buat" value="">
                                <button id="generatePdfBtn" class="btn btn-danger">Export PDF</button>
                                <button id="generateExcelBtn" class="btn btn-success">Export Excel</button>
                            </div>
                        </div>

                        <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal PO(+)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style Size</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Item_Type</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode Warna</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Color</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty (Pcs)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kgs</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Terima (Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa BB_Mesin(Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Order(Pcs)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">BS Mesin(Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">BS Setting(Pcs)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Mesin (Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Mesin (Cns)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Packing (Pcs)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Packing (Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">(+)Packing (Cns)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Lebih Pakai(Kg)</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
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
<!-- modal export pdf -->
<div class="modal fade" id="exportPdf" tabindex="-1" role="dialog" aria-labelledby="exportPdf" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export PDF</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body align-items-center">
                <div class="form-group">
                    <label for="area" class="col-form-label">Tanggal Buka PO(+)</label>
                    <input type="date" class="form-control" id="tgl_buat">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="generatePdfBtn" class="btn bg-gradient-info">Generate</button>
                <button type="button" id="generateExcelBtn" class="btn bg-gradient-success">Generate Excel</button>
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
    let btnSearch = document.getElementById('searchFilter');

    btnSearch.onclick = function() {
        let area = document.getElementById('area').value;
        let model = document.getElementById('no_model').value;
        let tglBuat = document.getElementById('tgl_po').value;
        let role = <?= json_encode($role) ?>;
        let info = document.getElementById('info');

        // Cek apakah tgl_po kosong
        if (!tglBuat) {
            alert("Silakan isi tanggal PO terlebih dahulu!");
            return; // Stop eksekusi
        }

        console.log("Area: " + area);
        console.log("Model: " + model);
        console.log("Tgl PO: " + tglBuat);

        $.ajax({
            url: "<?= base_url($role . '/filter_list_potambahan/') ?>" + area,
            type: "GET",
            data: {
                model: model,
                tglBuat: tglBuat
            },
            dataType: "json",
            success: function(response) {
                fethcData(response, model, tglBuat, area);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            },
        });
    };

    function fethcData(data, model, tglBuat, area) {
        $('#tgl_buat').val(tglBuat);

        let dataTable = $('#dataTable').DataTable();
        dataTable.clear(); // Hapus semua data sebelumnya

        if (data.length === 0) {
            $('#headerModel').hide();

            const node = dataTable.row.add(['']).draw().node(); // Tambahkan satu kolom kosong
            const $td = $(node).find('td').first(); // Ambil td pertama

            $td
                .attr('colspan', 16)
                .html(`Data tidak ditemukan untuk model: <strong>${model}</strong> & tgl Po: <strong>${tglBuat}</strong>`)
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


        // ✅ Tampilkan header dan isi span table-header
        $('#headerModel').css('display', 'flex');
        $('#table-header').text(tglBuat);

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
                item.qty_pcs || 0,
                item.kgs || 0,
                item.ttl_terima_kg || 0,
                item.ttl_sisa_bb_dimc || 0,
                item.sisa_order_pcs || 0,
                item.bs_mesin_kg || 0,
                item.bs_st_pcs || 0,
                item.poplus_mc_kg || 0,
                item.poplus_mc_cns || 0,
                item.plus_pck_pcs || 0,
                item.plus_pck_kg || 0,
                item.plus_pck_cns || 0,
                item.lebih_pakai_kg || 0,
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

    $(document).ready(function() {
        $('#exportPdfBtn').on('click', function() {
            $('#exportPdf').modal('show');
        });
    });

    $('#generatePdfBtn').on('click', function() {
        const area = $('#area').val();
        const model = $('#no_model').val();
        const tglBuat = $('#tgl_buat').val();
        const role = <?= json_encode($role) ?>;

        // if (!tglBuat) {
        //     alert("Silakan isi No Model terlebih dahulu.");
        //     return;
        // }

        const url = "<?= base_url($role . '/generate_po_tambahan') ?>" +
            "?area=" + encodeURIComponent(area) +
            "&model=" + encodeURIComponent(model) +
            "&tgl_buat=" + encodeURIComponent(tglBuat);

        window.open(url, '_blank');
    });

    $('#generateExcelBtn').on('click', function() {
        const area = $('#area').val();
        const model = $('#no_model').val();
        const tglBuat = $('#tgl_buat').val();
        const role = <?= json_encode($role) ?>;

        // if (!tglBuat) {
        //     alert("Silakan isi No Model terlebih dahulu.");
        //     return;
        // }

        const url = "<?= base_url($role . '/generate_excel_po_tambahan') ?>" +
            "?area=" + encodeURIComponent(area) +
            "&model=" + encodeURIComponent(model) +
            "&tgl_buat=" + encodeURIComponent(tglBuat);

        window.open(url, '_blank');
    });
</script>
<?php $this->endSection(); ?>