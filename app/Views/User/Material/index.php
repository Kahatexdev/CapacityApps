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
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                Form Pemesanan Bahan Baku <?= $area ?>
                            </h5>
                        </div>
                        <div>
                            <a href="<?= base_url($role . '/listPemesanan/' . $area) ?>" class="btn btn-info">List Pemesanan</a>
                        </div>
                    </div>
                    <!-- <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div> -->
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="card-body">
                        <form id="pemesananBbForm">
                            <div class="row mb-4">
                                <!-- PO Tambahan -->
                                <div class="col-md-1 mb-3">
                                    <label for="po_tambahan" class="form-label">PO Tambahan</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="po_tambahan" name="po_tambahan">
                                        <label class="form-check-label" for="po_tambahan">Ya</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <input type="hidden" name="area" id="area" value="<?= $area ?>">
                                    <label>Tanggal Pakai Benang Nylon</label>
                                    <input type="date" class="form-control" id="tgl_pakai_benang_nylon" name="tgl_pakai_benang_nylon" min="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Tanggal Pakai Spandex Karet</label>
                                    <input type="date" class="form-control" id="tgl_pakai_spandex_karet" name="tgl_pakai_spandex_karet" min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-2">
                                <div class="col-md-12">
                                    <label for="itemType">No Model</label>
                                    <select class="form-control" id="no_model" name="no_model" required>
                                        <option value="">Pilih No Model</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-1" id="detailContainer">
                                <div class="col-md-12">
                                    <!-- detail akan ditampilkan di sini -->
                                </div>
                            </div>

                            <div class="row g-3 mb-2" id="perstyleContainer" style="display: none;">
                                <div class="mt-5">
                                    <h3 class="text-center">Data Bahan Baku Per Style</h3>
                                </div>
                                <!-- Bahan Baku Section -->
                                <div class="row" id="detailBbCardsContainer">
                                    <!-- data ditampilkan disini -->
                                </div>
                                <button type="submit" class="btn btn-info w-100">Simpan Ke Tabel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h5 class="font-weight-bolder mb-0">
                        List Pemesanan Bahan Baku
                    </h5>
                    <div class="card-body">
                        <form id="formPemesanan">
                            <div class="table-responsive">
                                <table id="header" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center">
                                                <div class="form-check">
                                                    <input type="checkbox" id="select-all" class="form-check-input">
                                                </div>
                                            </th>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Tgl Pakai</th>
                                            <th class="text-center">No Model</th>
                                            <th class="text-center">Style Size</th>
                                            <th class="text-center">Item Type</th>
                                            <th class="text-center">Kode Warna</th>
                                            <th class="text-center">Warna</th>
                                            <th class="text-center">Jalan Mc</th>
                                            <th class="text-center">Ttl Cns</th>
                                            <th class="text-center">Ttl Berat Cns (Kg)</th>
                                            <th class="text-center">PO (+)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        foreach ($groupedData as $groupKey => $group) {
                                            // Inisialisasi total untuk grup ini
                                            $total_jalan_mc = 0;
                                            $total_ttl_cns = 0;
                                            $total_ttl_berat_cns = 0;
                                            // Tampilkan tiap record dalam grup
                                            foreach ($group as $index => $record) {
                                                $total_jalan_mc += number_format((float)$record['jalan_mc'], 2);
                                                $total_ttl_cns += number_format((float)$record['ttl_cns'], 2);
                                                $total_ttl_berat_cns += number_format((float)$record['ttl_berat_cns'], 2);
                                        ?>
                                                <tr>
                                                    <!-- kolom hide -->
                                                    <input type="hidden" name="role" value="<?= $role; ?>">
                                                    <input type="hidden" name="id_material[]" value="<?= $record['id_material']; ?>">
                                                    <input type="hidden" name="area[]" value="<?= $area; ?>">
                                                    <!-- kolom hide end -->
                                                    <td class="text-center">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input checkbox-pemesanan" name="checkbox[]" value="<?= $record['id_material'] . ',' . $record['tgl_pakai']; ?>">
                                                        </div>
                                                    </td>
                                                    <td class="text-center"><?= $no++; ?></td>
                                                    <td class="text-center"><input type="text" class="form-control text-center w-100" name="tgl_pakai[]" value="<?= $record['tgl_pakai']; ?>" readonly></td>
                                                    <td class="text-center"><input type="text" class="form-control text-center w-100" name="no_model[]" value="<?= $record['no_model']; ?>" readonly></td>
                                                    <td class="text-center"><input type="text" class="form-control text-center w-100" name="style_size[]" value="<?= $record['style_size']; ?>" readonly></td>
                                                    <td class="text-center"><input type="text" class="form-control text-center w-100" name="item_type[]" value="<?= $record['item_type']; ?>" readonly></td>
                                                    <td class="text-center"><input type="text" class="form-control text-center w-100" name="kode_warna[]" value="<?= $record['kode_warna']; ?>" readonly></td>
                                                    <td class="text-center"><input type="text" class="form-control text-center w-100" name="warna[]" value="<?= $record['warna']; ?>" readonly></td>
                                                    <td class="text-center"><input type="text" class="form-control text-center w-100" name="jalan_mc[]" value="<?= $record['jalan_mc']; ?>" readonly></td>
                                                    <td class="text-center"><input type="text" class="form-control text-center w-100" name="ttl_cns[]" value="<?= $record['ttl_cns']; ?>" readonly></td>
                                                    <td class="text-center"><input type="text" class="form-control text-center w-100" name="ttl_berat_cns[]" value="<?= $record['ttl_berat_cns']; ?>" readonly></td>
                                                    <!-- <td class="text-center"><input type="text" class="form-control text-center w-100" name="po_tambahan[]" value="<?= $record['po_tambahan']; ?>" readonly></td> -->
                                                    <td class="text-center">
                                                        <input type="hidden" class="form-control text-center w-100"
                                                            value="<?= $record['po_tambahan'] ?>">
                                                        <?php if ($record['po_tambahan'] == 1): ?>
                                                            <span class="text-success fw-bold">✅</span>
                                                        <?php else: ?>
                                                            <!-- Biarkan kosong -->
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            // Dapatkan data grouping dari record pertama grup
                                            $first = $group[0];
                                            ?>
                                            <!-- Baris subtotal dengan informasi grouping ditampilkan di kolom yang sesuai -->
                                            <tr class="subtotal">
                                                <!-- Kolom No dan Tanggal Pakai bisa dikosongkan atau diberi label Total -->
                                                <td colspan="8" class="font-weight-bolder" style="text-align: right;">TOTAL <?= $first['no_model'] . ' / ' . $first['item_type'] . ' / ' . $first['kode_warna'] . ' / ' . $first['warna']; ?></td>
                                                <td class="text-center font-weight-bolder"><?= $total_jalan_mc; ?></td>
                                                <td class="text-center font-weight-bolder"><?= $total_ttl_cns; ?></td>
                                                <td class="text-center font-weight-bolder"><?= $total_ttl_berat_cns; ?></td>
                                            </tr>

                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="11" class="text-center font-weight-bolder align-middle">Hapus List Pemesanan</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger" id="delete-selected"><i class="fas fa-trash"></i></button>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-info w-100" id="submit-selected">Buat List Pemesanan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tglPakaiBNInput = document.getElementById('tgl_pakai_benang_nylon');
        const tglPakaiSKInput = document.getElementById('tgl_pakai_spandex_karet');
        const noModelSelect = document.getElementById('no_model');
        const poTambahanCheckbox = document.getElementById('po_tambahan');
        const area = $('#area').val(); // Ambil nilai area dari input hidden

        // Fungsi untuk mengubah status select berdasarkan nilai input tgl_pakai
        function toggleNoModel() {
            if (tglPakaiBNInput.value.trim() === "" || tglPakaiSKInput.value.trim() === "") {
                noModelSelect.disabled = true;
            } else {
                noModelSelect.disabled = false;
            }
        }

        // Panggil fungsi saat halaman pertama kali dimuat
        toggleNoModel();

        // Panggil fungsi setiap kali input tgl_pakai berubah
        tglPakaiBNInput.addEventListener('change', toggleNoModel);
        tglPakaiSKInput.addEventListener('change', toggleNoModel);

        // Fungsi untuk mengambil data no model
        function fetchNoModelData() {
            const poTambahanChecked = poTambahanCheckbox.checked ? 1 : 0;
            $('#no_model').empty();
            $('#no_model').append('<option value="">Pilih No Model</option>');
            $('#detailBbCardsContainer').empty(); // kosongkan data style size yg telah di pilih sebelumnya

            $.ajax({
                url: '<?= base_url($role . '/bahanBaku/getNomodel') ?>',
                method: 'GET',
                data: {
                    po_tambahan: poTambahanChecked,
                    area: area
                }, // Kirim data PO Tambahan sebagai parameter
                dataType: 'json',
                success: function(data) {
                    // Kosongkan dropdown
                    $('#no_model').empty();
                    $('#no_model').append('<option value="">Pilih No Model</option>');

                    // Tambahkan data baru ke dropdown
                    data.forEach(function(model) {
                        $('#no_model').append(`<option value="${model.model}">${model.model}</option>`);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching No Model data:', error);
                }
            });
        }

        // Panggil fetchNoModelData saat halaman pertama kali dimuat
        fetchNoModelData();

        // Deteksi perubahan pada checkbox PO Tambahan
        poTambahanCheckbox.addEventListener('change', fetchNoModelData);
    });
    $(document).ready(function() {
        const area = $('#area').val(); // Ambil nilai area dari input hidden
        const poTambahanCheckbox = document.getElementById('po_tambahan');

        // Inisialisasi Select2 di kolom no model
        $('#no_model').select2({
            width: '100%'
        });

        let row = 0;
        let globalData = []; // Variabel global untuk menyimpan data AJAX
        // Event ketika no model berubah
        $('#no_model').change(function() {
            const poTambahanChecked = poTambahanCheckbox.checked ? 1 : 0;
            let noModel = $(this).val(); // Ambil value yang dipilih di select2
            if (noModel) {
                // ambil style size by model
                $.ajax({
                    url: '<?= base_url($role . '/getStyleSizeByNoModelPemesanan') ?>',
                    type: "GET",
                    data: {
                        no_model: noModel,
                        po_tambahan: poTambahanChecked,
                        area: area
                    }, // Kirim dalam format object
                    dataType: "json",
                    success: function(data) {
                        globalData = data; // Simpan data di variabel global
                        $('#detailContainer').empty();
                        $('#detailBbCardsContainer').empty();
                        $('#perstyleContainer').show();

                        var cardHtml = `
                            <div class="table-responsive">
                                <table id="header" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="450" class="text-center">Style Size</th>
                                            <th class="text-center">Gw</th>
                                            <th class="text-center">Inisial</th>
                                            <th class="text-center">Jalan MC</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="450">
                                                <select class="form-control style-size" name="items[${row}][style_size]" required>
                                                    <option value="">Pilih Style Size</option>
                                                    ${data.map(item => `<option value="${item.size}">${item.size}</option>`).join('')}
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control gw" name="items[${row}][gw]" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control inisial" name="items[${row}][inisial]" readonly>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control jalan-mc" name="items[${row}][jalan_mc]">
                                                <input type="hidden" class="form-control qty" name="items[${row}][qty]">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-info add-row" id="addTable">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width=20 class="text-center">No</th>
                                            <th width=50 class="text-center">Komp(%)</th>
                                            <th width=50 class="text-center">Loss(%)</th>
                                            <th width=120 class="text-center">Kg Kebutuhan</th>
                                            <th class="text-center">Item Type</th>
                                            <th class="text-center">Kode Warna</th>
                                            <th class="text-center">Warna</th>

                                        </tr>
                                    </thead>
                                    <tbody class="material-usage">
                                    </tbody>
                                </table>
                            </div>
                        `;
                        $('#detailBbCardsContainer').append(cardHtml);
                    },
                    error: function() {
                        alert('Gagal mengambil data! Silakan coba lagi.');
                    }
                });
            }
        });

        // Event untuk menambah baris
        $('#detailBbCardsContainer').on('click', '.add-row', function() {
            row++;
            let newRow = `
                <div class="table-responsive">
                    <table id="header" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="450" class="text-center">Style Size</th>
                                <th class="text-center">Gw</th>
                                <th class="text-center">Inisial</th>
                                <th class="text-center">Jalan MC</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="450">
                                    <select class="form-control style-size" name="items[${row}][style_size]" required>
                                        <option value="">Pilih Style Size</option>
                                        ${globalData.map(item => `<option value="${item.size}">${item.size}</option>`).join('')}
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control gw" name="items[${row}][gw]" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control inisial" name="items[${row}][inisial]" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control jalan-mc" name="items[${row}][jalan_mc]">
                                    <input type="hidden" class="form-control qty" name="items[${row}][qty]">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger remove-row" id="addTable">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width=20 class="text-center">No</th>
                                <th width=50 class="text-center">Komp(%)</th>
                                <th width=50 class="text-center">Loss(%)</th>
                                <th width=120 class="text-center">Qty PO</th>
                                <th class="text-center">Item Type</th>
                                <th class="text-center">Kode Warna</th>
                                <th class="text-center">Warna</th>

                            </tr>
                        </thead>
                        <tbody class="material-usage">
                        </tbody>
                    </table>
                </div>
            `;
            // rowCount++;
            $('#detailBbCardsContainer').append(newRow);
        });

        // kalkulasi ketika jl mc berubah
        $('#detailBbCardsContainer').on('input', '.jalan-mc', function() {
            const jalanMc = parseFloat($(this).val()) || 0; // Ambil nilai jalan_mc
            const table = $(this).closest('.table-responsive').find('.material-usage'); // Temukan tabel terkait

            // Perbarui semua baris di tabel terkait
            table.find('tr').each(function() {
                const row = $(this); // Ambil baris saat ini
                const jalan_mc = parseFloat(row.find('.jalan_mc').val()) || 0; // Nilai Qty Cones
                const qty = parseFloat(row.find('.qty_cns').val()) || 0; // Nilai Qty Cones
                const berat = parseFloat(row.find('.qty_berat_cns').val()) || 0; // Nilai Berat Cones

                // Hitung ulang Total Cones dan Total Berat Cones
                const ttlCns = qty * jalanMc;
                const ttlBeratCns = qty * berat * jalanMc;

                // Perbarui nilai Total Cones dan Total Berat Cones
                row.find('.jalan_mc').val(jalanMc);
                row.find('.ttl_cns').val(ttlCns.toFixed(2));
                row.find('.ttl_berat_cns').val(ttlBeratCns.toFixed(2));
            });
        });

        // Event untuk menghapus baris
        $('#detailBbCardsContainer').on('click', '.remove-row', function() {
            $(this).closest('.table-responsive').remove(); // Cari elemen ".table-responsive" terdekat dan hapus
        });

        // Event untuk mengambil "Jalan MC" ketika "Style Size" berubah
        $('#detailBbCardsContainer').on('change', '.style-size', function() {
            let selectedStyleSize = $(this).val(); // Ambil nilai Style Size yang dipilih
            let jalanMcInput = $(this).closest('tr').find('.jalan-mc'); // Cari input "Jalan MC" di baris yang sama
            let qty = $(this).closest('tr').find('.qty'); // Cari input "Jalan MC" di baris yang sama
            let gw = $(this).closest('tr').find('.gw'); // Cari input "Jalan MC" di baris yang sama
            let inisial = $(this).closest('tr').find('.inisial'); // Cari input "Jalan MC" di baris yang sama
            let tgl_pakai = $('#tgl_pakai').val(); // Ambil nilai No Model
            let noModel = $('#no_model').val(); // Ambil nilai No Model
            let area = $('#area').val(); // Ambil nilai No Model
            // Cari elemen <tbody> "material-usage" yang terkait dengan baris ini
            let table = $(this).closest('.table-responsive').find('.material-usage');

            // Validasi untuk memastikan Style Size tidak duplikat di baris lain
            let isDuplicate = false;
            let currentElement = $(this); // Elemen yang memicu event 'change'
            $('.style-size').each(function() {
                // Bandingkan nilai elemen lain dengan nilai elemen saat ini
                if ($(this).val() === selectedStyleSize && $(this).get(0) !== currentElement.get(0)) {
                    isDuplicate = true;
                    return false; // Hentikan iterasi jika duplikat ditemukan
                }
            });

            if (isDuplicate) {
                alert('Style Size sudah ada');
                $(this).val(''); // Kosongkan dropdown Style Size
                jalanMcInput.val(''); // Kosongkan input Jalan MC
                qty.val(''); // Kosongkan input Jalan MC
                gw.val(''); // Kosongkan input Jalan MC
                inisial.val(''); // Kosongkan input Jalan MC
                table.empty(); // Kosongkan tabel Material Usage
                return; // Hentikan proses jika duplikat ditemukan
            }

            if (selectedStyleSize !== "" && selectedStyleSize !== null &&
                noModel !== "" && noModel !== null &&
                area !== "" && area !== null) {
                // AJAX untuk mengambil Jalan MC
                $.ajax({
                    url: '<?= base_url($role . '/getJalanMc') ?>', // Ganti dengan URL endpoint Anda
                    type: 'POST',
                    data: {
                        style_size: selectedStyleSize,
                        no_model: noModel,
                        area: area
                    },
                    dataType: "json",
                    success: function(response) {

                        jalanMcInput.val(response.jalan_mc); // Isi input "Jalan MC" dengan data dari server
                    },
                    error: function() {
                        alert('Gagal mengambil data Jalan MC! Silakan coba lagi.');
                    }
                });

                $.ajax({
                    url: '<?= base_url($role . '/getQty') ?>', // Ganti dengan URL endpoint Anda
                    type: 'POST',
                    data: {
                        style_size: selectedStyleSize,
                        no_model: noModel,
                        area: area
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response)
                        qty.val(response.qty);
                        const poTambahanChecked = poTambahanCheckbox.checked ? 1 : 0;
                        if (poTambahanChecked == 1) {
                            urlMu = `http://172.23.44.14/MaterialSystem/public/api/getMUPoTambahan?no_model=${encodeURIComponent(noModel)}&style_size=${encodeURIComponent(selectedStyleSize)}&area=${encodeURIComponent(area)}`;
                        } else {
                            urlMu = '<?= base_url($role . '/getMU') ?>/' + noModel + '/' + encodeURIComponent(selectedStyleSize) + '/' + area + '/' + qty.val();
                        }

                        // Lakukan permintaan AJAX
                        $.ajax({
                            url: urlMu, // Ganti URL sesuai kebutuhan
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                console.log(response); // Debug data yang diterima
                                table.empty(); // Hapus isi tabel sebelumnya

                                // Jika response ada tetapi kosong
                                if (response.status === 'empty') {
                                    table.append(`
                                        <tr>
                                            <td colspan="7" class="text-center text-danger fw-bold">Material Usage belum ada, hubungi Gbn</td>
                                        </tr>
                                    `);
                                    return;
                                }
                                // Jika response berupa array dan kita hanya ingin data dari elemen pertama
                                if (response && response.length > 0) {
                                    // Ambil data inisial dan gw dari elemen pertama
                                    let firstData = response[0];
                                    gw.val(firstData.gw);
                                }


                                // Iterasi data yang diterima dari API dan tambahkan ke tabel
                                response.forEach(function(item, index) {
                                    const uniqueKey = `[${row}][${index}]`;
                                    const total = (item.qty_cns * item.qty_berat_cns).toFixed(2);
                                    const jalanMc = parseFloat(jalanMcInput.val()) || 0; // Ganti dengan input jalanMc yang sesuai
                                    const totalCones = (item.qty_cns * jalanMc).toFixed(2);
                                    const totalBeratCones = (total * jalanMc).toFixed(2);
                                    const poTambahan = poTambahanChecked;

                                    table.append(`
                                            <tr>
                                                // kolom hide
                                                <input type="hidden" class="form-control text-center" name="items[${row}][${index}][tgl_pakai]" id="tgl_pakai" value="${tgl_pakai}" readonly>
                                                <input type="hidden" class="form-control text-center" name="items[${row}][${index}][po_tambahan]" id="po_tambahan" value="${poTambahan}" readonly>
                                                <input type="hidden" class="form-control text-center" name="items[${row}][${index}][no_model]" id="no_model" value="${noModel}" readonly>
                                                <input type="hidden" class="form-control text-center" name="items[${row}][${index}][style_size]" id="style_size" value="${selectedStyleSize}" readonly>
                                                <input type="hidden" class="form-control text-center" name="items[${row}][${index}][id_material]" id="id_material" value="${item.id_material}" readonly>
                                                <input type="hidden" class="form-control text-center" name="items[${row}][${index}][jenis]" id="jenis" value="${item.jenis}" readonly>
                                                <input type="hidden" class="form-control text-center jalan_mc" name="items[${row}][${index}][jalan_mc]" id="jalan_mc" value="${jalanMc}" readonly>
                                                // 
                                                <td width=20><input type="text" class="form-control text-center" name="items[${row}][${index}][no]" id="no" value="${index + 1}" readonly></td>
                                                <td width=50><input type="text" class="form-control text-center" name="items[${row}][${index}][komposisi]" id="komposisi" value="${item.composition}" readonly></td>
                                                <td width=50><input type="text" class="form-control text-center" name="items[${row}][${index}][loss]" id="loss" value="${item.loss}" readonly></td>
                                                <td width=120><input type="text" class="form-control text-center" name="items[${row}][${index}][ttl_keb]" id="ttl_keb" value="${parseFloat(item.ttl_keb || 0).toFixed(2)}" readonly></td>
                                                <td><input type="text" class="form-control text-center" name="items[${row}][${index}][item_type]" id="item_type" value="${item.item_type}" readonly></td>
                                                <td><input type="text" class="form-control text-center" name="items[${row}][${index}][kode_warna]" id="kode_warna" value="${item.kode_warna}" readonly></td>
                                                <td><input type="text" class="form-control text-center" name="items[${row}][${index}][warna]" id="warna" value="${item.color}" readonly></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td class="text-center">
                                                    Qty Cones:
                                                    <input type="number" class="form-control text-center qty_cns" name="items[${row}][${index}][qty_cns]" id="qty_cns" value="${item.qty_cns}">    
                                                </td>
                                                <td class="text-center">
                                                    Berat Cones:
                                                    <input type="number" step="0.01" class="form-control text-center qty_berat_cns" name="items[${row}][${index}][qty_berat_cns]" id="qty_berat_cns" value="${item.qty_berat_cns}">
                                                </td>
                                                <td class="text-center">
                                                    Total:
                                                    <input type="number" step="0.01" class="form-control text-center ttl" name="items[${row}][${index}][ttl]" id="ttl" value="${total}" readonly>
                                                </td>
                                                <td class="text-center">
                                                    Total Cones:
                                                    <input type="number" step="0.01" class="form-control text-center ttl_cns" name="items[${row}][${index}][ttl_cns]" id="ttl_cns" value="${totalCones}" readonly>
                                                </td>
                                                <td class="text-center">
                                                    Total Berat Cones:
                                                    <input type="number" step="0.01" class="form-control text-center ttl_berat_cns" name="items[${row}][${index}][ttl_berat_cns]" id="ttl_berat_cns" value="${totalBeratCones}" readonly>
                                                </td>
                                                <td></td>
                                            </tr>
                                        `);
                                });
                                // Tambahkan event listener untuk perhitungan otomatis
                                table.on('input', '.qty_cns, .qty_berat_cns, .ttl_berat_cns', function() {
                                    const row = $(this).closest('tr');
                                    const qty = parseFloat(row.find('.qty_cns').val());
                                    const berat = parseFloat(row.find('.qty_berat_cns').val());
                                    const total = qty * berat;
                                    row.find('.ttl').val(total.toFixed(2));

                                    //Total Cones
                                    const jalanMc = parseFloat(jalanMcInput.val());
                                    const ttlCns = qty * jalanMc;
                                    row.find('.ttl_cns').val(ttlCns);

                                    //Total Berat Cones
                                    const totalBeratCns = total * jalanMc;
                                    row.find('.ttl_berat_cns').val(totalBeratCns.toFixed(2));
                                });
                            },
                            error: function() {
                                table.empty().append(`
                                <tr>
                                    <td colspan="7" class="text-center text-danger fw-bold">Gagal mengambil data Material Usage. Silakan coba lagi.</td>
                                </tr>
                            `);
                            }
                        });
                    },
                    error: function() {
                        alert('Gagal mengambil data Jalan MC! Silakan coba lagi.');
                    }
                });
            } else {
                jalanMcInput.val(''); // Kosongkan input "Jalan MC" jika tidak ada yang dipilih
                table.empty(); // Hapus isi tabel sebelumnya
            }
        });
        // Submit form untuk menyimpan ke session
        $('#pemesananBbForm').submit(function(e) {
            e.preventDefault();
            $('.style-size').removeAttr('name');
            $('.jalan-mc').removeAttr('name');
            $('.qty').removeAttr('name');
            $('.gw').removeAttr('name');
            $('.inisial').removeAttr('name');
            // Periksa apakah tabel Material Usage kosong
            let isMaterialUsageEmpty = true;
            $('.material-usage').each(function() {
                if ($(this).find('tr').length > 0) {
                    isMaterialUsageEmpty = false;
                    return false; // Hentikan iterasi jika ada data
                }
            });

            if (isMaterialUsageEmpty) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Data Material Usage kosong. Tidak dapat menyimpan ke session.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return; // Hentikan proses submit jika tabel kosong
            }

            // 1) Validasi: Jalan MC tidak boleh kosong di tiap baris
            let invalidMachine = false;
            $('.material-usage').find('input.jalan_mc').each(function() {
                const jm = parseFloat($(this).val()) || 0;
                if (jm === 0) {
                    invalidMachine = true;
                    return false; // break .each
                }
            });
            if (invalidMachine) {
                Swal.fire('Peringatan', 'Nilai Jalan MC tidak boleh kosong atau 0', 'warning');
                return;
            }

            // Jika tabel tidak kosong, lanjutkan proses submit
            const formData = $(this).serializeArray();

            $.ajax({
                url: "<?= base_url($role . '/bahanBaku/simpanKeSession') ?>",
                method: "POST",
                data: formData,
                success: function(response) {
                    console.log(response);
                    // Cek status dari Proses 1
                    if (response.status === "success") {
                        // Proses 2: Mengirim ke URL kedua
                        $.ajax({
                            url: "http://172.23.44.14/MaterialSystem/public/api/insertQtyCns",
                            method: "POST",
                            data: formData,
                            success: function(secondResponse) {
                                // Log respons server
                                console.log("Response dari Proses 2:", secondResponse);

                                // Periksa status respons dari Proses update qty cns & berat cns
                                if (secondResponse.status === "success") {
                                    // Kedua proses berhasil
                                    Swal.fire({
                                        title: "Berhasil",
                                        text: "Data berhasil diupdate & disimpan ke list pemesanan.",
                                        icon: "success",
                                        // showConfirmButton: true,
                                    }).then(() => {
                                        location.reload(); // Refresh halaman setelah alert selesai
                                    });
                                } else if (secondResponse.status === "warning") {
                                    // Proses 2 gagal
                                    Swal.fire({
                                        title: secondResponse.title,
                                        text: secondResponse.message,
                                        icon: secondResponse.status,
                                        showConfirmButton: true,
                                    });
                                } else {
                                    // Proses 2 gagal
                                    Swal.fire({
                                        title: "Error",
                                        text: secondResponse.message || "Gagal update qty cns.",
                                        icon: "error",
                                        showConfirmButton: true,
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(`AJAX Error: ${xhr.status} ${xhr.statusText}`);
                                console.error("<?= $role ?>");
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Gagal menyimpan data",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    } else {
                        // Proses 1 gagal
                        Swal.fire({
                            title: response.title,
                            text: response.message || "Gagal menyimpan list pemesanan.",
                            icon: "error",
                            showConfirmButton: true,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + error);
                    console.error("<?= $role ?>");
                    Swal.fire({
                        title: "Gagal!",
                        text: "Gagal menyimpan data",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            });
        });
    });


    // save data yang dipilih pemesanan ke database
    // document.getElementById('submit-selected').addEventListener('click', function() {
    //     const selected = Array.from(document.querySelectorAll('.checkbox-pemesanan:checked')).map(checkbox => checkbox.value);

    //     if (selected.length === 0) {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: 'Peringatan',
    //             text: 'Pilih setidaknya satu data!',
    //             confirmButtonText: 'OK'
    //         });
    //         return;
    //     }

    //     // Kirim data yang dipilih melalui fetch
    //     const BASE_URL = "<?= base_url(); ?>";
    //     const payload = {
    //         selected
    //     };
    //     fetch('http://172.23.44.14/MaterialSystem/public/api/saveListPemesanan', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //             },
    //             body: JSON.stringify(payload),
    //         })
    //         .then(async (response) => {
    //             const resData = await response.json();
    //             if (response.ok) {
    //                 Swal.fire({
    //                     icon: 'success',
    //                     title: 'Sukses!',
    //                     text: 'Data berhasil dikirim!',
    //                 }).then(() => {
    //                     location.reload(); // Refresh halaman setelah sukses
    //                 });
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Error!',
    //                     text: resData.message || 'Gagal mengirim data',
    //                 });
    //                 console.error('Response Data:', resData);
    //             }
    //         })
    //         .catch((error) => {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Error!',
    //                 text: 'Terjadi kesalahan saat mengirim data',
    //             });
    //             console.error('Fetch Error:', error);
    //         });
    // });

    document.getElementById('formPemesanan').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const BASE_URL = "<?= base_url(); ?>";

        // Ambil data checkbox yang tercentang
        const selectedCheckboxes = Array.from(document.querySelectorAll('.checkbox-pemesanan:checked'));

        // Jika tidak ada yang tercentang, tampilkan alert
        if (selectedCheckboxes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih setidaknya satu data!',
            });
            return;
        }

        // Siapkan payload
        const payload = {};

        selectedCheckboxes.forEach((checkbox) => {
            // Cari elemen terkait di baris yang sama
            const row = checkbox.closest('tr');

            // Ambil semua input dalam baris tersebut
            const inputs = row.querySelectorAll('input');

            inputs.forEach((input) => {
                const key = input.name.replace(/\[\]$/, ''); // Hapus "[]"
                if (!payload[key]) payload[key] = [];
                payload[key].push(input.value);
            });
        });


        fetch('http://172.23.44.14/MaterialSystem/public/api/saveListPemesanan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
                // credentials: 'include', // Menyertakan cookie/session ID
            })
            .then(async (response) => {
                const resData = await response.json();
                const selected = Array.from(document.querySelectorAll('.checkbox-pemesanan:checked')).map(checkbox => checkbox.value);
                if (response.ok) {
                    fetch('bahanBaku/hapusSession', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json', // Penting untuk memastikan JSON diproses
                            },
                            body: JSON.stringify({
                                selected
                            }),
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.status === 'success') {
                                // Tampilkan SweetAlert setelah session berhasil dihapus
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: resData.message,
                                }).then(() => {
                                    // Redirect ke halaman yang diinginkan
                                    window.location.href = `${BASE_URL}user/bahanBaku`; // Halaman tujuan setelah sukses
                                });
                            } else {
                                console.error('Error saat menghapus session:', error);
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning!',
                                    text: 'Data berhasil disimpan, tetapi session gagal dihapus.',
                                }).then(() => {
                                    // Tetap redirect meskipun ada error saat menghapus session
                                    window.location.href = `${BASE_URL}user/bahanBaku`;
                                });
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Gagal menghapus session',
                            });
                        });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: resData.message || 'Gagal menyimpan data',
                    });
                    console.error('Response Data:', resData);
                }
            })
            .catch((error) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengirim data',
                });
                console.error('Fetch Error:', error);
            });
    });

    window.onload = function() {
        // Hitung tanggal 2 hari ke belakang dari hari ini
        let today = new Date();
        let twoDaysAgo = new Date(today);
        twoDaysAgo.setDate(today.getDate() - 2);
        // Format ke YYYY-MM-DD
        let dd = String(twoDaysAgo.getDate()).padStart(2, '0');
        let mm = String(twoDaysAgo.getMonth() + 1).padStart(2, '0'); // Januari = 0
        let yyyy = twoDaysAgo.getFullYear();
        let tgl_pakai = yyyy + '-' + mm + '-' + dd;

        // ambil are
        let area = document.getElementById('area').value; // Atau ambil dari variable lain

        $.ajax({
            url: 'http://172.23.44.14/MaterialSystem/public/api/hapusOldPemesanan',
            type: 'POST',
            data: JSON.stringify({
                area: area,
                tgl_pakai: tgl_pakai
            }),
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText || error);
            }
        });
    };

    // Checkbox "Select All" functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.checkbox-pemesanan');
        const isChecked = this.checked;

        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    });

    // hapus session list pemesanan
    document.getElementById('delete-selected').addEventListener('click', function() {
        const selected = Array.from(document.querySelectorAll('.checkbox-pemesanan:checked')).map(checkbox => checkbox.value);

        if (selected.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih setidaknya satu data!',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (confirm('Apakah Anda yakin ingin menghapus data yang dipilih?')) {
            $.ajax({
                url: '<?= base_url($role . "/bahanBaku/hapusSession") ?>',
                type: 'POST',
                contentType: 'application/json', // Tentukan format data sebagai JSON
                data: JSON.stringify({
                    selected
                }), // Ubah data menjadi string JSON
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil dihapus',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        location.reload(); // Refresh halaman setelah penghapusan
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menghapus data',
                        text: 'Terjadi kesalahan: ' + xhr.responseText,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
</script>

<?php $this->endSection(); ?>