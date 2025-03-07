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
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Pemesanan Bahan Baku <?= $area ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
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
                                <div class="col-md-12">
                                    <input type="hidden" name="area" id="area" value="<?= $area ?>">
                                    <label>Tanggal Pakai</label>
                                    <input type="date" class="form-control" id="tgl_pakai" name="tgl_pakai" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-2">
                                <div class="col-md-12">
                                    <label for="itemType">No Model</label>
                                    <select class="form-control" id="no_model" name="no_model" required>
                                        <option value="">Pilih No Model</option>
                                        <?php foreach ($noModel as $model): ?>
                                            <option value="<?= $model['model'] ?>"><?= $model['model'] ?></option>
                                        <?php endforeach; ?>
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
                            </div>
                            <button type="submit" class="btn btn-info w-100">Simpan Ke Tabel</button>
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
    $(document).ready(function() {
        // Inisialisasi Select2 di kolom no model
        $('#no_model').select2({
            width: '100%'
        });

        let row = 0;
        let globalData = []; // Variabel global untuk menyimpan data AJAX
        // Event ketika no model berubah
        $('#no_model').change(function() {
            let noModel = $(this).val(); // Ambil value yang dipilih di select2
            if (noModel) {
                // ambil style size by model
                $.ajax({
                    url: '<?= base_url($role . '/getStyleSizeByNoModel') ?>',
                    type: "POST",
                    data: {
                        no_model: noModel
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
                                            <th class="text-center">Style Size</th>
                                            <th class="text-center">Jalan MC</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="form-control style-size" name="items[${row}][style_size]">
                                                    <option value="">Pilih Style Size</option>
                                                    ${data.map(item => `<option value="${item.size}">${item.size}</option>`).join('')}
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control jalan-mc" name="items[${row}][jalan_mc]" readonly>
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
                                <th class="text-center">Style Size</th>
                                <th class="text-center">Jalan MC</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-control style-size" name="items[${row}][style_size]">
                                        <option value="">Pilih Style Size</option>
                                        ${globalData.map(item => `<option value="${item.size}">${item.size}</option>`).join('')}
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control jalan-mc" name="items[${row}][jalan_mc]" readonly>
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

        // Event untuk menghapus baris
        $('#detailBbCardsContainer').on('click', '.remove-row', function() {
            $(this).closest('.table-responsive').remove(); // Cari elemen ".table-responsive" terdekat dan hapus
        });

        // Event untuk mengambil "Jalan MC" ketika "Style Size" berubah
        $('#detailBbCardsContainer').on('change', '.style-size', function() {
            let selectedStyleSize = $(this).val(); // Ambil nilai Style Size yang dipilih
            let jalanMcInput = $(this).closest('tr').find('.jalan-mc'); // Cari input "Jalan MC" di baris yang sama
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
            console.log(isDuplicate)

            if (isDuplicate) {
                alert('Style Size sudah ada');
                $(this).val(''); // Kosongkan dropdown Style Size
                jalanMcInput.val(''); // Kosongkan input Jalan MC
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


                // Lakukan permintaan AJAX
                $.ajax({
                    url: '<?= base_url($role . '/getMU') ?>/' + noModel + '/' + selectedStyleSize, // Ganti URL sesuai kebutuhan
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response); // Debug data yang diterima

                        table.empty(); // Hapus isi tabel sebelumnya

                        // Iterasi data yang diterima dari API dan tambahkan ke tabel
                        response.forEach(function(item, index) {
                            const total = (item.qty_cns * item.berat_cns).toFixed(2);
                            const jalanMc = parseFloat(jalanMcInput.val()) || 0; // Ganti dengan input jalanMc yang sesuai
                            const totalCones = (item.qty_cns * jalanMc).toFixed(2);
                            const totalBeratCones = (total * jalanMc).toFixed(2);

                            table.append(`
                            <tr>
                            <input type="hidden" class="form-control text-center" name="items[${index}][no]" id="id_material" value="${item.id_material}" readonly>
                                <td width=20><input type="text" class="form-control text-center" name="items[${index}][no]" id="no" value="${index + 1}" readonly></td>
                                <td width=50><input type="text" class="form-control text-center" name="items[${index}][komposisi]" id="komposisi" value="${item.composition}" readonly></td>
                                <td width=50><input type="text" class="form-control text-center" name="items[${index}][loss]" id="loss" value="${item.loss}" readonly></td>
                                <td width=120><input type="text" class="form-control text-center" name="items[${index}][ttl_keb]" id="ttl_keb" value="${item.gw}" readonly></td>
                                <td><input type="text" class="form-control text-center" name="items[${index}][item_type]" id="item_type" value="${item.item_type}" readonly></td>
                                <td><input type="text" class="form-control text-center" name="items[${index}][kode_warna]" id="kode_warna" value="${item.kode_warna}" readonly></td>
                                <td><input type="text" class="form-control text-center" name="items[${index}][warna]" id="warna" value="${item.color}" readonly></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="text-center">
                                    Qty Cones:
                                    <input type="number" step="0.01" class="form-control text-center qty_cns" name="items[${index}][qty_cns]" id="qty_cns" value="${item.qty_cns}">    
                                </td>
                                <td class="text-center">
                                    Berat Cones:
                                    <input type="number" step="0.01" class="form-control text-center berat_cns" name="items[${index}][berat_cns]" id="berat_cns" value="${item.berat_cns}">
                                </td>
                                <td class="text-center">
                                    Total:
                                    <input type="number" step="0.01" class="form-control text-center ttl" name="items[${index}][ttl]" id="ttl" value="${total}" readonly>
                                </td>
                                <td class="text-center">
                                    Total Cones:
                                    <input type="number" step="0.01" class="form-control text-center ttl_cns" name="items[${index}][ttl_cns]" id="ttl_cns" value="${totalCones}" readonly>
                                </td>
                                <td class="text-center">
                                    Total Berat Cones:
                                    <input type="number" step="0.01" class="form-control text-center ttl_berat_cns" name="items[${index}][ttl_berat_cns]" id="ttl_berat_cns" value="${totalBeratCones}" readonly>
                                </td>
                                <td></td>
                            </tr>
                        `);
                        });
                        // Tambahkan event listener untuk perhitungan otomatis
                        table.on('input', '.qty_cns, .berat_cns, .ttl_berat_cns', function() {
                            const row = $(this).closest('tr');
                            const qty = parseFloat(row.find('.qty_cns').val());
                            const berat = parseFloat(row.find('.berat_cns').val());
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
                        alert('Gagal mengambil data Material Usage.');
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
            var formData = $(this).serializeArray();

            $.ajax({
                url: "<?= base_url($role . '/bahanBaku/simpanKeSession') ?>",
                method: "POST",
                data: formData,
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        title: "Sukses!",
                        text: response.message,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload(); // Refresh halaman setelah alert selesai
                    });
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
</script>



<?php $this->endSection(); ?>