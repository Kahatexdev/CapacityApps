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
                                    Analytical Dashboard
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
    <div class="row">

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"> Target Produksi </p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $targetProd ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-tasks text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Produksi Bulan ini</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $produksiBulan ?>
                                    <span class=" text-sm font-weight-bolder">/ <?= $produksiBulan ?> </span>

                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-settings text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Produksi Hari ini </p>
                                <h5 class="font-weight-bolder mb-0">
                                    <span class=" text-sm font-weight-bolder">This Month</span>
                                    <?= $produksiHari ?>

                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-book text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Order Finished</p>
                                <h5 class="font-weight-bolder mb-0">
                                    8
                                    <span class=" text-sm font-weight-bolder">This Month</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
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
                    <div class="card-header">
                        <h3>Form Pemesanan Bahan Baku</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url($role . '/outCelup/saveBon') ?>" method="post">
                            <div id="kebutuhan-container">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label>Tanggal Pakai</label>
                                        <input type="date" class="form-control" id="tgl_pakai" name="tgl_pakai" required>
                                    </div>
                                </div>
                                <!--  -->
                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">1</button>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                        <!-- Form Items -->
                                        <div class="kebutuhan-item">
                                            <div class="row g-3 mb-2">
                                                <div class="col-md-12">
                                                    <input type="hidden" value="<?= $area ?>">
                                                    <label for="itemType">No Model</label>
                                                    <select class="form-control add-item" id="no_model" name="items[0][no_model]" required>
                                                        <option value="">Pilih No Model</option>
                                                        <?php foreach ($noModel as $model): ?>
                                                            <option value="<?= $model['model'] ?>"><?= $model['model'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mt-5">
                                                <h3>Bahan Baku Per Style</h3>
                                            </div>
                                            <div id="tableSize">
                                                <!-- Bahan Baku Section -->
                                                <div class="row g-3 mt-3">
                                                    <div class="table-responsive">
                                                        <table id="bbTable" class="table table-bordered table-striped">
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
                                                                        <select class="form-control style-size" name="items[0][style_size]">
                                                                            <option value=""></option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" class="form-control" name="items[0][jalan_mc]" id="jalan_mc" readonly>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-info" id="addTable">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    </td>
                                                                    <!-- <td class="text-center">
                                                                    <button type="button" class="btn btn-danger removeRow">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td> -->
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!-- Bahan Baku Section -->
                                                <div class="row g-3 mt-3">
                                                    <div class="table-responsive">
                                                        <table id="poTable" class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th width=50 class="text-center">No</th>
                                                                    <th width=75 class="text-center">Komp(%)</th>
                                                                    <th width=75 class="text-center">Loss(%)</th>
                                                                    <th width=75 class="text-center">Qty PO</th>
                                                                    <th width=75 class="text-center">Item Type</th>
                                                                    <th width=75 class="text-center">Kode Warna</th>
                                                                    <th width=75 class="text-center">Warna</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody class="material-usage">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Buttons -->
                                            <div class="row mt-3">
                                                <div class="col-12 text-center mt-2">
                                                    <button class="btn btn-icon btn-3 btn-outline-info add-more" type="button">
                                                        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                    </button>
                                                    <button class="btn btn-icon btn-3 btn-outline-danger remove-tab" type="button">
                                                        <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-info w-100">Save</button>
                                </div>
                            </div>
                        </form>

                    </div>
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
        $('.add-item').select2();
    });
    $(document).ready(function() {
        // Event ketika No Model dipilih
        $('#no_model').on("select2:select", function() {
            // Pastikan #no_model berada di dalam sebuah <tr>
            let row = $(this).closest('tr');
            let noModel = $(this).val();

            // Cari dropdown style size di baris yang sama
            let styleSizeDropdown = row.find('[name^="items[][name$="[style_size]"]');

            // Bersihkan dropdown dan tambahkan opsi default
            styleSizeDropdown.empty();
            styleSizeDropdown.append('<option value="">Pilih Style Size</option>');

            // Jika noModel ada, ambil data style size lewat AJAX
            if (noModel) {
                $.ajax({
                    url: '<?= base_url($role . '/getStyleSizeByNoModel') ?>',
                    type: 'POST',
                    data: {
                        no_model: noModel
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Kosongkan ulang dropdown dan tambahkan opsi default
                        styleSizeDropdown.empty();
                        styleSizeDropdown.append('<option value="">Pilih Style Size</option>');
                        // Tambahkan opsi berdasarkan response
                        response.forEach(function(style) {
                            console.log(style);
                            console.log(styleSizeDropdown);
                            styleSizeDropdown.append('<option value="' + style.size + '">' + style.size + '</option>');
                        });
                    },
                    error: function() {
                        alert('Gagal mengambil data Style Size.');
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        // Ketika Style Size dipilih
        $('#style_size').change(function() {
            let styleSize = $(this).val(); // Ambil nilai Style Size
            let noModel = $('#no_model').val(); // Ambil nilai No Model
            let area = $('#area').val(); // Ambil nilai No Model

            if (styleSize && noModel) {
                // AJAX untuk mengambil Jalan MC
                $.ajax({
                    url: '<?= base_url($role . '/getJalanMc') ?>', // Ganti dengan URL endpoint Anda
                    type: 'POST',
                    data: {
                        style_size: styleSize,
                        no_model: noModel,
                        area: area
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        // Set the value of the #jalan_mc input field
                        $('#jalan_mc').val(response.jalan_mc);

                        // If you intend to append a new input element with the same value
                        $('#jalan_mc').append('<input value="' + response.jalan_mc.jalan_mc + '">');
                    },

                    error: function() {
                        alert('Gagal mengambil data Jalan MC');
                    }
                });
                // Ambil data MU dari API
                $.ajax({
                    url: '<?= base_url($role . '/getMU') ?>/' + noModel + '/' + styleSize,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        let table = $('#poTable tbody');
                        table.empty(); // Hapus isi tabel sebelumnya
                        // Iterasi data yang diterima dari API
                        response.forEach(function(item, index) {
                            table.append(`
                                <tr>
                                    <td width=50><input type="text" class="form-control text-center" name="items[${index}][no]" id="no" value="${index + 1}" readonly></td>
                                    <td width=75><input type="text" class="form-control text-center" name="items[${index}][komposisi]" id="komposisi" value="${item.composition}" readonly readonly></td>
                                    <td width=75><input type="text" class="form-control text-center" name="items[${index}][loss]" id="loss" value="${item.loss}" readonly readonly></td>
                                    <td width=75><input type="text" class="form-control text-center" name="items[${index}][ttl_keb]" id="ttl_keb" value="${item.gw}" readonly readonly></td>
                                    <td width=75><input type="text" class="form-control text-center" name="items[${index}][item_type]" id="item_type" value="${item.item_type}" readonly readonly></td>
                                    <td width=75><input type="text" class="form-control text-center" name="items[${index}][kode_warna]" id="kode_warna" value="${item.kode_warna}" readonly readonly></td>
                                    <td width=75><input type="text" class="form-control text-center" name="items[${index}][warna]" id="warna" value="${item.color}" readonly readonly></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="text-center">
                                        Qty Cones:
                                        <input type="text" class="form-control text-center" name="items[${index}][qty_cns]" id="qty_cns" value="">    
                                    </td>
                                    <td class="text-center">
                                        Berat Cones:
                                        <input type="text" class="form-control text-center" name="items[${index}][berat_cns]" id="berat_cns" value="">
                                    </td>
                                    <td class="text-center">
                                        Total:
                                        <input type="text" class="form-control text-center" name="items[${index}][ttl]" id="ttl" value="">
                                    </td>
                                    <td class="text-center">
                                        Total Cones:
                                        <input type="text" class="form-control text-center" name="items[${index}][ttl_cns]" id="ttl_cns" value="">
                                    </td>
                                    <td class="text-center">
                                        Total Berat Cones:
                                        <input type="text" class="form-control text-center" name="items[${index}][ttl_berat_cns]" id="ttl_berat_cns" value="">
                                    </td>
                                </tr>
                            `);
                        });



                    },
                    error: function() {
                        alert('Gagal mengambil data Material Usage.');
                    }
                });
            } else {
                // Reset input Jalan MC jika Style Size atau No Model kosong
                $('#jalan_mc').val('');
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const addbtn = document.getElementById("addTable");
        const poTable = document.getElementById("tableSize");

        let rowIndex = 0;

        addbtn.addEventListener("click", function() {
            rowIndex++;
            console.log("halow");
            console.log(rowIndex);

            // Buat elemen div baru
            let newLine = document.createElement("div");
            newLine.classList.add("row", "g-3", "mt-3"); // Menambahkan class agar sesuai dengan layout

            // Isi elemen baru dengan template tabel
            newLine.innerHTML = `
                <div class="row g-3 mt-3">
                    <div class="table-responsive">
                        <table id="bbTable" class="table table-bordered table-striped">
                            <thead>
                                <tr class="id-row">
                                    <th class="text-center">Style Size</th>
                                    <th class="text-center">Jalan MC</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control style-size" name="items[${rowIndex}][style_size]">
                                            <option value=""></option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="items[${rowIndex}][jalan_mc]" id="jalan_mc" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info" id="addTable">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                    <!-- <td class="text-center">
                                    <button type="button" class="btn btn-danger removeRow">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td> -->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="table-responsive">
                        <table id="poTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width=100 class="text-center">No</th>
                                    <th class="text-center">Komp(%)</th>
                                    <th class="text-center">Loss(%)</th>
                                    <th class="text-center">Qty PO</th>
                                    <th class="text-center">Item Type</th>
                                    <th class="text-center">Kode Warna</th>
                                    <th class="text-center">Warna</th>

                                </tr>
                            </thead>
                            <tbody class="material-usage">
                                <tr>
                                    <td><input type="text" class="form-control text-center" name="items[${rowIndex}][no]" id="no" value="1" readonly></td>
                                    <td><input type="text" class="form-control text-center" name="items[${rowIndex}][komposisi]" id="komposisi" value="" readonly></td>
                                    <td><input type="text" class="form-control text-center" name="items[${rowIndex}][loss]" id="loss" value="" readonly></td>
                                    <td><input type="text" class="form-control text-center" name="items[${rowIndex}][ttl_keb]" id="ttl_keb" value="" readonly></td>
                                    <td><input type="text" class="form-control text-center" name="items[${rowIndex}][item_type]" id="item_type" value="" readonly></td>
                                    <td><input type="text" class="form-control text-center" name="items[${rowIndex}][kode_warna]" id="kode_warna" value="" readonly></td>
                                    <td><input type="text" class="form-control text-center" name="items[${rowIndex}][warna]" id="warna" value="" readonly></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="text-center">
                                        Qty Cones:
                                        <input type="text" class="form-control text-center" name="items[${index}][qty_cns]" id="qty_cns" value="">    
                                    </td>
                                    <td class="text-center">
                                        Berat Cones:
                                        <input type="text" class="form-control text-center" name="items[${index}][berat_cns]" id="berat_cns" value="">
                                    </td>
                                    <td class="text-center">
                                        Total:
                                        <input type="text" class="form-control text-center" name="items[${index}][ttl]" id="ttl" value="">
                                    </td>
                                    <td class="text-center">
                                        Total Cones:
                                        <input type="text" class="form-control text-center" name="items[${index}][ttl_cns]" id="ttl_cns" value="">
                                    </td>
                                    <td class="text-center">
                                        Total Berat Cones:
                                        <input type="text" class="form-control text-center" name="items[${index}][ttl_berat_cns]" id="ttl_berat_cns" value="">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            // Tambahkan elemen ke dalam `poTable`
            if (poTable) {
                poTable.appendChild(newLine);
            } else {
                console.error("Element with id 'tableSize' not found");
            }

            // Tambahkan event listener untuk tombol hapus
            newLine.querySelector(".removeRow").addEventListener("click", function() {
                newLine.remove();
                rowIndex--; // Kurangi indeks ketika baris dihapus
            });
        });



        const navTab = document.getElementById("nav-tab");
        const navTabContent = document.getElementById("nav-tabContent");
        let tabIndex = 2;
        let valueLot = "";

        function updateTabNumbers() {
            // Update nomor pada setiap tab
            const tabButtons = navTab.querySelectorAll(".nav-link");
            const tabPanes = navTabContent.querySelectorAll(".tab-pane");

            tabButtons.forEach((button, index) => {
                const newNumber = index + 1;
                button.textContent = newNumber; // Update nomor tab
                button.dataset.bsTarget = `#nav-content-${newNumber}`;
                button.id = `nav-tab-${newNumber}-button`;

                const relatedPane = tabPanes[index];
                relatedPane.id = `nav-content-${newNumber}`;
                relatedPane.ariaLabelledby = `nav-tab-${newNumber}-button`;

                // Update nama atribut input agar sinkron
                relatedPane.querySelectorAll("[name]").forEach((input) => {
                    const name = input.name.replace(/\d+/, newNumber - 1);
                    input.name = name;
                });
            });

            // Perbarui indeks tab berikutnya
            tabIndex = tabButtons.length + 1;
        }

        function updateRowNumbers(table) {
            const rows = table.querySelectorAll("tbody tr");
            rows.forEach((row, index) => {
                row.querySelector("input[name^='no_karung']").value = index + 1;
            });
        }

        // Event delegation untuk menghapus baris
        document.addEventListener("click", function(event) {
            if (event.target.closest(".removeRow")) {
                const row = event.target.closest("tr");
                const table = row.closest("table");
                row.remove();
                updateRowNumbers(table);
                calculateTotals(table);
            }
        });

        // Fungsi untuk membuat tab baru
        function addNewTab() {
            // ID untuk tab dan konten baru
            const newTabId = `nav-tab-${tabIndex}`;
            const newContentId = `nav-content-${tabIndex}`;
            const newPoTableId = `poTable-${tabIndex}`;
            const totalKarungId = `total_karung_${tabIndex}`;
            const totalGwId = `total_gw_kirim_${tabIndex}`;
            const totalKgsId = `total_kgs_kirim_${tabIndex}`;
            const totalConesId = `total_cones_kirim_${tabIndex}`;
            const totalLotId = `total_lot_kirim_${tabIndex}`;

            const newInputId = `no_model_${tabIndex}`;
            const id_celup = `id_celup_${tabIndex}`;
            const itemTypeId = `item_type_${tabIndex}`;
            const kodeWarnaId = `kode_warna_${tabIndex}`;
            const warnaId = `warna_${tabIndex}`;
            const lotCelupId = `lot_celup_${tabIndex}`;

            // Tambahkan tab baru ke nav-tab
            const newTabButton = document.createElement("button");
            newTabButton.className = "nav-link";
            newTabButton.id = `${newTabId}-button`;
            newTabButton.dataset.bsToggle = "tab";
            newTabButton.dataset.bsTarget = `#${newContentId}`;
            newTabButton.type = "button";
            newTabButton.role = "tab";
            newTabButton.ariaControls = newContentId;
            newTabButton.ariaSelected = "false";
            newTabButton.textContent = tabIndex;

            // Tambahkan tab button ke nav-tab
            navTab.appendChild(newTabButton);

            // Tambahkan konten baru ke tab-content
            const newTabPane = document.createElement("div");
            newTabPane.className = "tab-pane fade";
            newTabPane.id = newContentId;
            newTabPane.role = "tabpanel";
            newTabPane.ariaLabelledby = `${newTabId}-button`;

            // Tambahkan elemen `input-group` ke tab baru
            newTabPane.innerHTML = `
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <!-- Form Items -->
                    <div class="kebutuhan-item">
                        <div class="row g-3 mb-2">
                            <div class="col-md-12">
                                <input type="hidden" value="<?= $area ?>">
                                <label for="itemType">No Model</label>
                                <select class="form-control add-item" id="no_model" name="items[0][no_model]" required>
                                    <option value="">Pilih No Model</option>
                                    <?php foreach ($noModel as $model): ?>
                                        <option value="<?= $model['model'] ?>"><?= $model['model'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h3>Bahan Baku Per Style</h3>
                        </div>
                        <div id="tableSize">
                            <!-- Bahan Baku Section -->
                            <div class="row g-3 mt-3">
                                <div class="table-responsive">
                                    <table id="bbTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="id-row">
                                                <th class="text-center">Style Size</th>
                                                <th class="text-center">Jalan MC</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="form-control style-size" name="items[0][style_size]">
                                                        <option value=""></option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="items[0][jalan_mc]" id="jalan_mc" readonly>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-info" id="addTable">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </td>
                                                <!-- <td class="text-center">
                                                <button type="button" class="btn btn-danger removeRow">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td> -->
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Bahan Baku Section -->
                            <div class="row g-3 mt-3">
                                <div class="table-responsive">
                                    <table id="poTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width=50 class="text-center">No</th>
                                                <th width=75 class="text-center">Komp(%)</th>
                                                <th width=75 class="text-center">Loss(%)</th>
                                                <th width=75 class="text-center">Qty PO</th>
                                                <th width=75 class="text-center">Item Type</th>
                                                <th width=75 class="text-center">Kode Warna</th>
                                                <th width=75 class="text-center">Warna</th>

                                            </tr>
                                        </thead>
                                        <tbody class="material-usage">
                                            <!-- <tr>
                                                <td><input type="text" class="form-control text-center" name="no" id="no" value="1" readonly></td>
                                                <td><input type="text" class="form-control text-center" name="komposisi" id="komposisi" value="" readonly></td>
                                                <td><input type="text" class="form-control text-center" name="loss" id="loss" value="" readonly></td>
                                                <td><input type="text" class="form-control text-center" name="ttl_keb" id="ttl_keb" value="" readonly></td>
                                                <td><input type="text" class="form-control text-center" name="item_type" id="item_type" value="" readonly></td>
                                                <td><input type="text" class="form-control text-center" name="kode_warna" id="kode_warna" value="" readonly></td>
                                                <td><input type="text" class="form-control text-center" name="warna" id="warna" value="" readonly></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    Qty Cones:
                                                    <input type="text" class="form-control text-center" name="qty_cns" id="qty_cns" value="">
                                                </td>
                                                <td>
                                                    Berat Cones:
                                                    <input type="text" class="form-control text-center" name="berat_cns" id="berat_cns" value="">
                                                </td>
                                                <td>
                                                    Total :
                                                    <input type="text" class="form-control text-center" name="ttl" id="ttl" value="">
                                                </td>
                                                <td>
                                                    Total Qty Cones:
                                                    <input type="text" class="form-control text-center" name="ttl_cns" id="ttl_cns" value="">
                                                </td>
                                                <td>
                                                    Total Berat Cones:
                                                    <input type="text" class="form-control text-center" name="ttl_berat_cns" id="ttl_berat_cns" value="">
                                                </td>
                                            </tr> -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="row mt-3">
                            <div class="col-12 text-center mt-2">
                                <button class="btn btn-icon btn-3 btn-outline-info add-more" type="button">
                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                </button>
                                <button class="btn btn-icon btn-3 btn-outline-danger remove-tab" type="button">
                                    <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;

            navTabContent.appendChild(newTabPane);
            document.getElementById(newContentId).querySelectorAll('.slc2').forEach(el => {
                $(el).select2({
                    width: '100%'
                });

                $(el).on("select2:select", function() {
                    let idcelup = $(this).val(); // Ambil value yang dipilih di select2

                    $.ajax({
                        url: "<?= base_url($role . '/createBon/getItem/') ?>" + idcelup,
                        type: "POST",
                        data: {
                            id: idcelup
                        }, // Kirim dalam format object
                        dataType: "json",
                        success: function(data) {
                            console.log(data);
                            // Update elemen input dan select di dalam tab baru
                            document.getElementById(id_celup).value = idcelup;
                            document.getElementById(newInputId).value = data.no_model;
                            document.getElementById(itemTypeId).innerHTML = `<option value="${data.item_type}" selected>${data.item_type}</option>`;
                            document.getElementById(kodeWarnaId).innerHTML = `<option value="${data.kode_warna}" selected>${data.kode_warna}</option>`;
                            document.getElementById(warnaId).innerHTML = `<option value="${data.warna}" selected>${data.warna}</option>`;
                            document.getElementById(lotCelupId).value = data.lot_celup;
                            valueLot = data.lot_celup;
                        }
                    });
                });
            });

            // Pindahkan ke tab baru
            const bootstrapTab = new bootstrap.Tab(newTabButton);
            bootstrapTab.show();

            // Event listener tombol
            newTabPane.querySelector(".add-more").addEventListener("click", addNewTab);

            newTabPane.querySelector(".remove-tab").addEventListener("click", function() {
                removeTab(newTabButton, newTabPane);
            });
            // Pasang event listener pada input baru
            newTabPane.querySelectorAll("input").forEach(input => {
                input.addEventListener("input", () => {
                    calculateTotals(newTabPane.querySelector(`#${newPoTableId}`));
                });
            });
            // Add row functionality
            const addRowButton = newTabPane.querySelector("#addRow");
            const removeRowButton = newTabPane.querySelector("#removeRow");
            const newPoTable = newTabPane.querySelector(`#${newPoTableId}`);
            const makan = tabIndex - 1;
            console.log(makan);
            addRowButton.addEventListener("click", function() {
                console.log('halow')
                const rowCount = newPoTable.querySelectorAll("tbody tr").length + 1;
                const newRow = document.createElement("tr");

                newRow.innerHTML = `
                    <td><input type="text" class="form-control text-center" name="no_karung[${tabIndex-2}][${rowCount-1}]" value="${rowCount}" readonly></td>
                    <td><input type="float" class="form-control gw_kirim_input" name="gw_kirim[${tabIndex-2}][${rowCount-1}]" required></td>
                    <td><input type="float" class="form-control kgs_kirim_input" name="kgs_kirim[${tabIndex-2}][${rowCount-1}]" required></td>
                    <td><input type="float" class="form-control cones_kirim_input" name="cones_kirim[${tabIndex-2}][${rowCount-1}]" required></td>
                    <td><input type="float" class="form-control lot_celup_input" name="lot_celup[${tabIndex-2}][${rowCount-1}]" value="${valueLot}" id="${lotCelupId}" required></td>
                    <td class="text-center">
                    <button type="button" class="btn btn-danger removeRow"><i class="fas fa-trash"></i></button>
                    </td>
                    `;

                newPoTable.querySelector("tbody").appendChild(newRow);

                // Tambahkan event listener untuk tombol hapus (removeRow) pada baris baru
                newRow.querySelector(".removeRow").addEventListener("click", function() {
                    newRow.remove();
                    updateRowNumbers(newPoTable);
                    calculateTotals(newPoTable); // Perbarui total setelah baris dihapus
                });
                // Recalculate totals when new row is added
                newRow.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', function() {
                        calculateTotals(newPoTable);
                    });
                });
                // calculateTotals(newPoTable);
                calculateTotals(newTabPane.querySelector(`#${newPoTableId}`));
            });

            // Event listeners for input changes
            newPoTable.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', function() {
                    calculateTotals(newPoTable);
                });
            });



            tabIndex++;
            calculateTotals(newPoTable);
        }


        function removeTab(tabButton, tabPane) {
            if (navTab.children.length > 1) {
                tabButton.remove();
                tabPane.remove();
                updateTabNumbers();
                // Pindahkan ke tab pertama jika tab aktif dihapus
                const firstTab = navTab.querySelector("button");
                if (firstTab) {
                    const bootstrapTab = new bootstrap.Tab(firstTab);
                    bootstrapTab.show();
                }
            } else {
                alert("Minimal harus ada satu tab.");
            }
        }

        // Event listener untuk tombol "Add More" di tab pertama
        const addMoreButton = document.querySelector(".add-more");
        addMoreButton.addEventListener("click", addNewTab);

        const removeButton = document.querySelector(".remove-tab");
        removeButton.addEventListener("click", function() {
            const firstTabButton = navTab.querySelector(".nav-link");
            const firstTabPane = navTabContent.querySelector(".tab-pane");
            removeTab(firstTabButton, firstTabPane);
        });
        updateTabNumbers();
    });
</script>



<?php $this->endSection(); ?>