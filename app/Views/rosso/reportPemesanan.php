<?php $this->extend('rosso/layout'); ?>
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
                        <div class="col-6">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                Report Pemesanan Bahan Baku <?= $area ?>
                            </h5>
                        </div>
                        <div class="col-6 d-flex align-items-center text-end gap-2">
                            <input type="date" class="form-control" id="tgl_awal" value="" required>
                            <input type="date" class="form-control" id="tgl_akhir" value="" required>
                            <button id="filterButton" class="btn btn-info ms-2" disabled><i class="fas fa-search"></i></button>
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
                        <table id="example" class="display compact " style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Pakai</th>
                                    <!-- <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Benang</th> -->
                                    <!-- <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Karet</th> -->
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Nylon</th>
                                    <!-- <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Spandex</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Silakan pilih tanggal untuk menampilkan data.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    $(document).ready(function() {
        const area = '<?= $area ?>';
        // base URL tanpa jenis dan params
        const basePdf = "<?= base_url("$role/report/pdf") ?>";

        // helper untuk build URL PDF
        function pdfUrl(jenis, area, tgl) {
            // encodeURIComponent untuk keamanan
            return `${basePdf}/${jenis}/${encodeURIComponent(area)}/${encodeURIComponent(tgl)}`;
        }

        // jenis yang ingin ditampilkan sebagai kolom
        const jenisList = ['NYLON'];

        // bangun definisi kolom DataTable
        const columns = [{
                data: null,
                title: 'No',
                render: (d, t, r, m) => m.row + 1
            },
            {
                data: 'tgl_pakai',
                title: 'Tanggal Pakai'
            },
            // sisipkan 4 kolom jenis secara dinamis
            ...jenisList.map(jenis => ({
                data: null,
                title: jenis.charAt(0).toUpperCase() + jenis.slice(1),
                orderable: false,
                searchable: false,
                render: (data, type, row) => {
                    return `<a href="${pdfUrl(jenis, area, row.tgl_pakai)}" target="_blank">
                  <i class="fas fa-file-pdf"></i>
                </a>`;
                }
            }))
        ];

        // inisialisasi DataTable
        const table = $('#example').DataTable({
            data: [],
            paging: true,
            ordering: true,
            info: false,
            columns
        });

        // toggle tombol filter
        function toggleFilterButton() {
            const a = $('#tgl_awal').val(),
                b = $('#tgl_akhir').val();
            $('#filterButton').prop('disabled', !(a && b));
        }
        $('#tgl_awal, #tgl_akhir').on('change', toggleFilterButton);

        // klik filter -> ambil data
        $('#filterButton').on('click', () => {
            const tglAwal = $('#tgl_awal').val(),
                tglAkhir = $('#tgl_akhir').val();

            $.ajax({
                url: '<?= base_url("$role/filterTglPakai/$area") ?>',
                method: 'POST',
                data: {
                    tgl_awal: tglAwal,
                    tgl_akhir: tglAkhir
                },
                dataType: 'json',
                success: data => {
                    table.clear();
                    if (Array.isArray(data) && data.length) {
                        table.rows.add(data);
                    } else {
                        table.row.add({
                            tgl_pakai: '—'
                        });
                    }
                    table.draw();
                },
                error: (xhr, status, error) => {
                    console.error('AJAX Error:', status, error);
                    alert("Gagal memuat data.");
                }
            });
        });
    });
</script>

<?php $this->endSection(); ?>