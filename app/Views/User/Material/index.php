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
                                        <label>Tanggal Kirim</label>
                                        <input type="date" class="form-control" id="tgl_datang" name="tgl_datang" required>
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
                                                    <label for="itemType">No Model</label>
                                                    <select class="form-control add-item" id="no_model" name="no_model" required>
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
                                                                    <select class="form-control" name="style_size" id="style_size">
                                                                        <option value="">Pilih Style Size</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control" name="jalan_mc" id="jalan_mc">
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-info" id="addRow">
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
                                                                <th width=100 class="text-center">No</th>
                                                                <th class="text-center">Komposisi(%)</th>
                                                                <th class="text-center">Loss(%)</th>
                                                                <th class="text-center">Total Kebutuhan</th>
                                                                <th class="text-center">Item Type</th>
                                                                <th class="text-center">Note</th>
                                                                <th class="text-center">Kode Warna</th>
                                                                <th class="text-center">Warna</th>
                                                                <th class="text-center">Qty Cones</th>
                                                                <th class="text-center">Berat Cones</th>
                                                                <th class="text-center">Total</th>
                                                                <th class="text-center">Total Qty Cones</th>
                                                                <th class="text-center">Total Berat Cones(Kg)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="material-usage">

                                                        </tbody>
                                                    </table>
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
<script type="text/javascript">
    $(document).ready(function() {
        $('.add-item').select2();
    });

    $(document).ready(function() {
        $('#example').DataTable({});

        // Trigger import modal when import button is clicked
        $('.import-btn').click(function() {
            console.log("a");
            var idModel = $(this).data('id');
            var noModel = $(this).data('no-model');

            $('#importModal').find('input[name="id_model"]').val(idModel);
            $('#importModal').find('input[name="no_model"]').val(noModel);

            $('#importModal').modal('show'); // Show the modal
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
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
            <div class="kebutuhan-item">
                                        <div class="row g-3 mb-2">
                                                <div class="col-md-12">
                                                    <label for="itemType">Done Celup</label>
                                                    <select class="form-control slc2" id="add_item_${tabIndex}" name="add_item" required>
                                                        <option value="">Pilih Item </option>
                                                        <?php foreach ($noModel as $item): ?>
                                                            <option value="<?= $item['model'] ?>"><?= $item['model'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                           <div class="row g-3">
                                            <div class="col-md-4">
                                                <label>No Model</label>
                                                <input type="text" class="form-control no-model" name="items[${tabIndex - 1}][id_celup]" id="${id_celup}" required placeholder="Pilih No Model" hidden>
                                                <input type="text" class="form-control no-model" name="items[${tabIndex - 1}][no_model]" id="${newInputId}" required placeholder="Pilih No Model">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Item Type</label>
                                                <select class="form-control item-type" name="items[${tabIndex - 1}][item_type]" id="${itemTypeId}" required>
                                                    <option value="">Pilih Item Type</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Kode Warna</label>
                                                <select class="form-control kode-warna" name="items[${tabIndex - 1}][kode_warna]" id="${kodeWarnaId}" required>
                                                    <option value="">Pilih Kode Warna</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Surat Jalan Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-4">
                                                <label>Warna</label>
                                                   <select class="form-control kode-warna" name="items[${tabIndex - 1}][kode_warna]" id="${warnaId}" required>
                                                    <option value="">Pilih Kode Warna</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>LMD</label>
                                                <select class="form-control" name="l_m_d[${tabIndex - 1}]" id="l_m_d" required>
                                                    <option value="">Pilih LMD</option>
                                                    <option value="L">L</option>
                                                    <option value="M">M</option>
                                                    <option value="D">D</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Harga</label>
                                                <input type="float" class="form-control" name="harga[${tabIndex - 1}]" id="harga" required>
                                            </div>
                                            <div class="col-md-1">
                                                <label for="ganti-retur" class="text-center">Ganti Retur</label>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>
                                                            <input type="hidden" name="ganti_retur[${tabIndex - 1}]" value="0">
                                                            <input type="checkbox" name="ganti_retur[${tabIndex - 1}]" value="1">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="">Ya</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-5">
                                            <h3>Form Input Data Karung</h3>
                                        </div>

                                        <!-- Out Celup Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="table-responsive">
                                                <table id="${newPoTableId}" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width=100 class="text-center">No</th>
                                                            <th class="text-center">GW Kirim</th>
                                                            <th class="text-center">Kgs Kirim</th>
                                                            <th class="text-center">Cones Kirim</th>
                                                            <th class="text-center">Lot Kirim</th>
                                                            <th class="text-center">
                                                                <button type="button" class="btn btn-info" id="addRow">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control text-center" name="no_karung[${tabIndex - 1}][0]" value="1" readonly></td>
                                                            <td><input type="float" class="form-control gw_kirim_input" name="gw_kirim[${tabIndex - 1}][0]" required></td>
                                                            <td><input type="float" class="form-control kgs_kirim_input" name="kgs_kirim[${tabIndex - 1}][0]" required></td>
                                                            <td><input type="float" class="form-control cones_kirim_input" name="cones_kirim[${tabIndex - 1}][0]" required></td>
                                                            <td><input type="text" class="form-control lot_celup_input" name="items[${tabIndex - 1}][lot_celup]" id="${lotCelupId}" required></td>

                                                            <td class="text-center">
                                                                <!-- <button type="button" class="btn btn-danger removeRow">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button> -->
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <!-- Baris Total -->
                                                    <tfoot>
                                                        <tr>
                                                            <th class="text-center">Total Karung</th>
                                                            <th class="text-center">Total GW</th>
                                                            <th class="text-center">Total NW</th>
                                                            <th class="text-center">Total Cones</th>
                                                            <th class="text-center">Total Lot</th>
                                                            <th></th>
                                                        </tr>
                                                         <tr>
                                                            <td><input type="number" class="form-control" id="${totalKarungId}" readonly></td>
                                                            <td><input type="float" class="form-control" id="${totalGwId}" readonly></td>
                                                            <td><input type="float" class="form-control" id="${totalKgsId}" readonly></td>
                                                            <td><input type="float" class="form-control" id="${totalConesId}" readonly></td>
                                                            <td><input type="text" class="form-control" id="${totalLotId}" readonly></td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
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
<!-- <script>
    let data =;
    console.log(data)
    // Ekstraksi tanggal dan jumlah produksi dari data
    let labels = data.map(item => item.created_at);
    let values = data.map(item => item.total_produksi);


    var ctx2 = document.getElementById("mixed-chart").getContext("2d");

    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

    var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
    gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

    new Chart(ctx2, {

        data: {
            labels: labels,
            datasets: [{
                    type: "bar",
                    label: "Data Turun Order",
                    borderWidth: 0,
                    pointRadius: 30,

                    backgroundColor: "#3A416F",
                    fill: true,
                    data: values,
                    maxBarThickness: 20

                },
                {
                    type: "line",

                    tension: 0.1,
                    borderWidth: 0,
                    pointRadius: 0,
                    borderColor: "#3A416F",
                    borderWidth: 2,
                    backgroundColor: gradientStroke1,
                    fill: true,
                    data: values,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#b2b9bf',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#b2b9bf',
                        padding: 20,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });
</script> -->
<script>
    $(document).ready(function() {
        // Ketika No Model dipilih
        $('#no_model').change(function() {
            let noModel = $(this).val(); // Ambil nilai No Model

            if (noModel) {
                // AJAX untuk mengambil Style Size
                $.ajax({
                    url: '<?= base_url($role . '/getStyleSizeByNoModel') ?>', // Ganti dengan URL endpoint Anda
                    type: 'POST',
                    data: {
                        no_model: noModel
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        // Hapus opsi sebelumnya di dropdown Style Size
                        $('#style_size').empty();
                        $('#style_size').append('<option value="">Pilih Style Size</option>');

                        // Tambahkan opsi baru berdasarkan data yang diterima
                        response.forEach(function(style) {
                            $('#style_size').append('<option value="' + style.size + '">' + style.size + '</option>');
                        });
                    },
                    error: function() {
                        alert('Gagal mengambil data Style Size.');
                    }
                });
            } else {
                // Reset dropdown Style Size jika No Model dikosongkan
                $('#style_size').empty();
                $('#style_size').append('<option value="">Pilih Style Size</option>');
            }
        });
    });

    $(document).ready(function() {
        // Ketika Style Size dipilih
        $('#style_size').change(function() {
            let styleSize = $(this).val(); // Ambil nilai Style Size
            let noModel = $('#no_model').val(); // Ambil nilai No Model

            if (styleSize && noModel) {
                // AJAX untuk mengambil Jalan MC
                $.ajax({
                    url: '<?= base_url($role . '/getJalanMc') ?>', // Ganti dengan URL endpoint Anda
                    type: 'POST',
                    data: {
                        style_size: styleSize,
                        no_model: noModel
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
                    url: '<?= base_url($role . 'getMU') ?>/' + noModel + '/' + styleSize,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            console.log(response.data);

                            // Lakukan sesuatu dengan data, contoh: tampilkan di tabel
                            let table = $('#poTable tbody');
                            table.empty(); // Hapus isi tabel sebelumnya

                            response.data.forEach(function(item, index) {
                                table.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.composition}</td>
                                    <td>${item.loss}</td>
                                    <td>${item.gw}</td>
                                    <td>${item.item_type}</td>
                                    <td>${item.kode_warna}</td>
                                    <td>${item.warna}</td>
                                </tr>
                            `);
                            });
                        } else {
                            alert('Data Material Usage tidak ditemukan.');
                        }
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

<?php $this->endSection(); ?>