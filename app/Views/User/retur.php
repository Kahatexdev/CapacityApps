<?php $this->extend('user/layout'); ?>
<?php $this->section('content'); ?>
<style>
    #loading {
        display: none;
        /* Sembunyikan awalnya */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: rgba(255, 255, 255, 0.7);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .input-group-text {
        position: static !important;
        z-index: auto !important;
    }
</style>
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
                    <div class="row d-flex align-items-center">
                        <div class="col-9">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $title . ' ' . $area ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-3 d-flex align-items-center text-end gap-2">
                            <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                            <input type="text" class="form-control" id="no_model" value="" placeholder="No Model">
                            <button id="searchModel" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="resultContainer">
        <!-- Tampilkan Tabel Hanya Jika Data Tersedia -->
        <div class="row mt-3">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div class="row" id="HeaderRow">
                            <!-- Header dan tombol disusun secara dinamis -->
                        </div>
                    </div>
                    <div class="card-body" id="bodyData">
                        <!-- Tampilan tabel data akan digenerate di sini -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info text-center text-white" id="info" role="alert">
                    Silakan masukkan No Model untuk mencari data.
                </div>
            </div>
        </div>
        <div id="loading">
            <h3>Sedang memuat data...</h3>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    // Variabel global untuk menyimpan opsi secara dinamis
    let dynamicItemTypes = [];
    let dynamicKategoriRetur = [];
    let dynamicKodeWarna = [];
    let dynamicWarna = [];
    let dynamicLotRetur = [];

    // Flag untuk memastikan data hanya dimuat sekali
    let isDynamicItemTypesLoaded = false;
    let isKodeWarnaLoaded = false;
    let isKategoriReturLoaded = false;
    let isWarnaLoaded = false;
    let isLotReturLoaded = false;

    // Fungsi untuk mengambil opsi item type secara dinamis melalui AJAX
    function loadDynamicItemTypes() {
        return new Promise((resolve, reject) => {
            const area = $('#area').val();
            const model = $('#no_model').val();
            $.ajax({
                url: "<?= base_url($role . '/filterRetur/') ?>" + area,
                data: {
                    model
                },
                dataType: "json",
                success(response) {
                    // map response values ke array objek
                    dynamicItemTypes = Object.values(response)
                        .filter(o => o.item_type);
                    isDynamicItemTypesLoaded = true;
                    resolve();
                },
                error(err) {
                    reject(err);
                }
            });
        });
    }

    $(document).on('shown.bs.modal', '#modalPengajuanRetur', function() {
        if (!isDynamicItemTypesLoaded) {
            loadDynamicItemTypes();
        }
    });

    // 1) Saat Item Type berubah → populate Kode Warna saja
    $(document).on('change', '.select-item-type', function() {
        const $block = $(this).closest('.retur-item');
        const itemType = $(this).val();
        const $kode = $block.find('.select-kode-warna');
        const $warna = $block.find('.select-warna');

        // reset dropdown
        $kode.html('<option value="">Pilih Kode Warna…</option>');
        $warna.html('<option value="">Pilih Warna setelah pilih Kode</option>');

        if (!itemType || !isDynamicItemTypesLoaded) return;

        // filter array global berdasarkan item_type
        const list = dynamicItemTypes.filter(o => o.item_type === itemType);

        // unique kode_warna
        const seen = new Set();
        list.forEach(o => {
            if (o.kode_warna && !seen.has(o.kode_warna)) {
                $kode.append(`<option value="${o.kode_warna}">${o.kode_warna}</option>`);
                seen.add(o.kode_warna);
            }
        });
    });

    // 2) Saat Kode Warna berubah → populate Warna
    $(document).on('change', '.select-kode-warna', function() {
        const $block = $(this).closest('.retur-item');
        const itemType = $block.find('.select-item-type').val();
        const kodeWarna = $(this).val();
        const $warna = $block.find('.select-warna');

        // reset dropdown
        $warna.html('<option value="">Pilih Warna…</option>');

        if (!kodeWarna || !isDynamicItemTypesLoaded) return;

        // filter berdasarkan item_type + kode_warna
        const list = dynamicItemTypes.filter(o =>
            o.item_type === itemType && o.kode_warna === kodeWarna
        );

        // unique warna (property `color` dari API)
        const seen = new Set();
        list.forEach(o => {
            const c = o.warna;
            if (c && !seen.has(c)) {
                $warna.append(`<option value="${c}">${c}</option>`);
                seen.add(c);
            }
        });
    });

    // Fungsi untuk mengambil kategori retur melalui AJAX dan mengembalikan Promise
    function loadKategoriRetur(selectedValue = '') {
        return new Promise(function(resolve, reject) {
            let selectEl = document.querySelector('#listReturInputs .retur-item select[name="items[0][kategori_retur]"]');
            if (selectEl) {
                selectEl.innerHTML = '<option value="">Loading...</option>';
                selectEl.disabled = true;
            }
            // console.log("Memanggil loadKategoriRetur() dengan selectedValue:", selectedValue);
            $.ajax({
                url: "<?= base_url($role . '/getKategoriRetur') ?>",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    // console.log("Kategori Retur:", response);
                    dynamicKategoriRetur = response;
                    const html = getKategoriRetur(selectedValue);
                    if (selectEl) {
                        selectEl.innerHTML = html;
                        selectEl.disabled = false;
                    }
                    isKategoriReturLoaded = true;
                    resolve();
                },
                error: function(xhr, status, error) {
                    console.error("Error loadKategoriRetur:", error);
                    if (selectEl) {
                        selectEl.innerHTML = '<option value="">Gagal memuat data</option>';
                        selectEl.disabled = false;
                    }
                    reject(error);
                }
            });
        });
    }


    // Fungsi untuk mengambil kode warna secara dinamis melalui AJAX
    // function loadKodeWarna(selectedValue = '') {
    //     return new Promise(function(resolve, reject) {
    //         const area = document.getElementById('area').value;
    //         const model = document.getElementById('no_model').value;
    //         let selectEl = document.querySelector('#listReturInputs .retur-item select[name="items[0][kode_warna]"]');
    //         if (selectEl) {
    //             selectEl.innerHTML = '<option value="">Loading...</option>';
    //             selectEl.disabled = true;
    //         }
    //         // console.log("Memanggil loadKodeWarna() dengan model:", model, "dan area:", area);
    //         $.ajax({
    //             url: "<?= base_url($role . '/filterRetur/') ?>" + area,
    //             type: "GET",
    //             data: {
    //                 model: model
    //             },
    //             dataType: "json",
    //             success: function(response) {
    //                 dynamicKodeWarna = Object.keys(response)
    //                     .filter(key => typeof response[key] === 'object' && response[key].kode_warna)
    //                     .map(key => response[key]);
    //                 // console.log("Dynamic Kode Warna:", dynamicKodeWarna);
    //                 if (selectEl) {
    //                     selectEl.innerHTML = getKodeWarnaOptions(selectedValue);
    //                     selectEl.disabled = false;
    //                 }
    //                 isKodeWarnaLoaded = true;
    //                 resolve();
    //             },
    //             error: function(xhr, status, error) {
    //                 if (selectEl) {
    //                     selectEl.innerHTML = '<option value="">Gagal memuat data</option>';
    //                     selectEl.disabled = false;
    //                 }
    //                 reject(error);
    //             }
    //         });
    //     });
    // }

    // Fungsi untuk mengambil data warna secara dinamis melalui AJAX
    // function loadWarna(selectedValue = '') {
    //     return new Promise(function(resolve, reject) {
    //         const area = document.getElementById('area').value;
    //         const model = document.getElementById('no_model').value;
    //         let selectEl = document.querySelector('#listReturInputs .retur-item select[name="items[0][warna]"]');
    //         if (selectEl) {
    //             selectEl.innerHTML = '<option value="">Loading...</option>';
    //             selectEl.disabled = true;
    //         }
    //         // console.log("Memanggil loadWarna() dengan model:", model, "dan area:", area);
    //         $.ajax({
    //             url: "<?= base_url($role . '/filterRetur/') ?>" + area,
    //             type: "GET",
    //             data: {
    //                 model: model
    //             },
    //             dataType: "json",
    //             success: function(response) {
    //                 dynamicWarna = Object.keys(response)
    //                     .filter(key => typeof response[key] === 'object' && response[key].warna)
    //                     .map(key => response[key]);
    //                 // console.log("Dynamic Warna:", dynamicWarna);
    //                 if (selectEl) {
    //                     selectEl.innerHTML = getWarnaOptions(selectedValue);
    //                     selectEl.disabled = false;
    //                 }
    //                 isWarnaLoaded = true;
    //                 resolve();
    //             },
    //             error: function(xhr, status, error) {
    //                 console.error("Error loadWarna:", error);
    //                 if (selectEl) {
    //                     selectEl.innerHTML = '<option value="">Gagal memuat data</option>';
    //                     selectEl.disabled = false;
    //                 }
    //                 reject(error);
    //             }
    //         });
    //     });
    // }

    // Fungsi untuk mengambil data lot retur secara dinamis melalui AJAX
    function loadLotRetur(selectedValue = '') {
        return new Promise(function(resolve, reject) {
            const area = document.getElementById('area').value;
            const model = document.getElementById('no_model').value;
            let selectEl = document.querySelector('#listReturInputs .retur-item select[name="items[0][lot_retur]"]');
            if (selectEl) {
                selectEl.innerHTML = '<option value="">Loading...</option>';
                selectEl.disabled = true;
            }
            // console.log("Memanggil loadLotRetur() dengan model:", model, "dan area:", area);
            $.ajax({
                url: "<?= base_url($role . '/filterRetur/') ?>" + area,
                type: "GET",
                data: {
                    model: model
                },
                dataType: "json",
                success: function(response) {
                    dynamicLotRetur = Object.keys(response)
                        .filter(key => typeof response[key] === 'object' && response[key].lot_out)
                        .map(key => response[key]);
                    // console.log("Dynamic Lot Retur:", dynamicLotRetur);
                    if (selectEl) {
                        selectEl.innerHTML = getLotRetur(selectedValue);
                        selectEl.disabled = false;
                    }
                    isLotReturLoaded = true;
                    resolve();
                },
                error: function(xhr, status, error) {
                    console.error("Error loadLotRetur:", error);
                    if (selectEl) {
                        selectEl.innerHTML = '<option value="">Gagal memuat data</option>';
                        selectEl.disabled = false;
                    }
                    reject(error);
                }
            });
        });
    }


    // Fungsi helper untuk membangun select option berdasarkan data yang sudah dimuat
    function getKodeWarnaOptions(selectedValue = '') {
        let options = '<option value="">Pilih Kode Warna</option>';
        const seen = new Set();
        dynamicKodeWarna.forEach((kode) => {
            if (!seen.has(kode.kode_warna)) {
                seen.add(kode.kode_warna);
                options += `<option value="${kode.kode_warna}" ${selectedValue === kode.kode_warna ? 'selected' : ''}>${kode.kode_warna}</option>`;
            }
        });
        return options;
    }

    function getWarnaOptions(selectedValue = '') {
        let options = '<option value="">Pilih Warna</option>';
        const seen = new Set();
        dynamicWarna.forEach((warna) => {
            if (!seen.has(warna.warna)) {
                seen.add(warna.warna);
                options += `<option value="${warna.warna}" ${selectedValue === warna.warna ? 'selected' : ''}>${warna.warna}</option>`;
            }
        });
        return options;
    }

    function getLotRetur(selectedValue = '') {
        let options = '<option value="">Pilih Lot</option>';
        const seen = new Set();
        dynamicLotRetur.forEach((lot) => {
            if (!seen.has(lot.lot_out)) {
                seen.add(lot.lot_out);
                options += `<option value="${lot.lot_out}" ${selectedValue === lot.lot_out ? 'selected' : ''}>${lot.lot_out}</option>`;
            }
        });
        return options;
    }

    function getItemTypeOptions(selectedValue = '') {
        let options = '<option value="">Pilih Jenis</option>';
        const seen = new Set();
        dynamicItemTypes.forEach((type) => {
            if (!seen.has(type.item_type)) {
                seen.add(type.item_type);
                options += `<option value="${type.item_type}" ${selectedValue === type.item_type ? 'selected' : ''}>${type.item_type}</option>`;
            }
        });
        return options;
    }

    function getKategoriRetur(selectedValue = '') {
        if (!dynamicKategoriRetur || dynamicKategoriRetur.length === 0) {
            console.warn("Kategori Retur masih kosong");
            return '<option value="">Data kategori retur tidak tersedia</option>';
        }
        let options = '<option value="">Pilih Kategori</option>';
        dynamicKategoriRetur.forEach((kategori) => {
            const val = kategori.nama_kategori;
            const label = `${kategori.nama_kategori} (${kategori.tipe_kategori})`;
            options += `<option value="${val}" ${selectedValue === val ? 'selected' : ''}>${label}</option>`;
        });
        return options;
    }

    // function updateKodeWarnaDanWarna(selectElement) {
    //     const selectedItemType = selectElement.value;
    //     const returItem = selectElement.closest('.retur-item');
    //     const kodeWarnaSelect = returItem.querySelector('.select-kode-warna');
    //     const warnaSelect = returItem.querySelector('.select-warna');

    //     // Reset dropdown sementara
    //     kodeWarnaSelect.innerHTML = '<option value="">Loading...</option>';
    //     warnaSelect.innerHTML = '<option value="">Loading...</option>';

    //     // Ambil kode warna berdasarkan item_type
    //     $.ajax({
    //         url: "<?= base_url($role . '/getKodeWarnaWarnaByItemType') ?>",
    //         method: "GET",
    //         data: {
    //             item_type: selectedItemType
    //         },
    //         dataType: "json",
    //         success: function(response) {
    //             let kodeWarnaOptions = '<option value="">Pilih Kode Warna</option>';
    //             let warnaOptions = '<option value="">Pilih Warna</option>';

    //             response.forEach(item => {
    //                 kodeWarnaOptions += `<option value="${item.kode_warna}">${item.kode_warna}</option>`;
    //                 warnaOptions += `<option value="${item.warna}">${item.warna}</option>`;
    //             });

    //             kodeWarnaSelect.innerHTML = kodeWarnaOptions;
    //             warnaSelect.innerHTML = warnaOptions;
    //         },
    //         error: function() {
    //             kodeWarnaSelect.innerHTML = '<option value="">Gagal memuat</option>';
    //             warnaSelect.innerHTML = '<option value="">Gagal memuat</option>';
    //         }
    //     });
    // }


    // Panggil lazy loading ketika modal ditampilkan
    $(document).on('shown.bs.modal', '#modalPengajuanRetur', function() {
        if (!isDynamicItemTypesLoaded) {
            loadDynamicItemTypes();
        }
        if (!isKategoriReturLoaded) {
            loadKategoriRetur();
        }
        // if (!isKodeWarnaLoaded) {
        //     loadKodeWarna();
        // }
        // if (!isWarnaLoaded) {
        //     loadWarna();
        // }
        if (!isLotReturLoaded) {
            loadLotRetur();
        }
    });

    $('#searchModel').on('click', function() {
        // Misalnya panggil semua request AJAX di sini
        Promise.all([
            loadDynamicItemTypes(),
            loadKategoriRetur(),
            // loadKodeWarna(),
            // loadWarna(),
            loadLotRetur()
        ]).then(function() {
            // Buka modal setelah semua data selesai diproses
            $('#modalPengajuanRetur').modal('show');
        }).catch(function(error) {
            console.error("Error ketika mengambil data:", error);
        });
    });

    // Sisanya (seperti event untuk search, build table, dan add more item) tetap sama
    $(document).ready(function() {
        const btnSearch = document.getElementById('searchModel');
        btnSearch.addEventListener('click', function() {
            const area = document.getElementById('area').value;
            const model = document.getElementById('no_model').value;
            const role = <?= json_encode($role) ?>;
            const loading = document.getElementById('loading');
            const info = document.getElementById('info');

            loading.style.display = 'block';
            info.style.display = 'none';

            $.ajax({
                url: "<?= base_url($role . '/filterRetur/') ?>" + area,
                type: "GET",
                data: {
                    model: model
                },
                dataType: "json",
                success: function(response) {
                    fetchData(response, model, area);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                },
                complete: function() {
                    loading.style.display = 'none';
                }
            });
        });
    });




    /**
     * Fungsi untuk membuat baris tabel dari data yang didapat
     */
    function buildTableRows(data, aggregateKeys) {
        let rows = '';
        let index = 0;
        for (const key in data) {
            if (aggregateKeys.includes(key)) continue;
            const item = data[key];
            const kgsOutVal = parseFloat(item.kgs_out);
            const validKgsOut = isNaN(kgsOutVal) ? '0' : kgsOutVal.toFixed(2);
            const estimasi = parseFloat(kgsOutVal - parseFloat(item.pph)).toFixed(2);

            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.no_model}</td>
                    <td>${item.area}</td>
                    <td>${item.item_type}</td>
                    <td>${item.kode_warna}</td>
                    <td>${parseFloat(item.ttl_kebutuhan).toFixed(2)} kg</td>
                    <td>${parseFloat(item.pph).toFixed(2)} kg</td>
                    <td>${validKgsOut} kg</td>
                    <td>${estimasi} kg</td>
                </tr>
            `;
            index++;
        }
        return rows;
    }

    /**
     * Fungsi utama untuk merender data ke dalam tabel dan modal
     */
    function fetchData(data, model, area) {
        const aggregateKeys = ["qty", "sisa", "bruto", "bs_setting", "bs_mesin"];
        const today = new Date();
        const baseUrl = "<?= base_url($role . '/retur/') ?>";
        const headerContainer = document.getElementById('HeaderRow');

        headerContainer.innerHTML = `
            <div class="header-container w-100">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="model-title mb-0">${model}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <a href="${baseUrl}${area}/exportExcel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalPengajuanRetur">
                            <i class="fas fa-paper-plane"></i> Pengajuan Retur
                    </div>
                </div>
            </div>

            <!-- Modal Pengajuan Retur -->
            <div class="modal fade" id="modalPengajuanRetur" tabindex="-1" aria-labelledby="modalPengajuanReturLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-xl">
                    <form action="${baseUrl}${area}/pengajuanRetur" method="POST" id="formPengajuanRetur">
                        <input type="hidden" name="model" value="${model}">
                        <input type="hidden" name="area" value="${area}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalPengajuanReturLabel"><strong>Form Pengajuan Retur</strong> ${today.toLocaleDateString('id-ID', { year:'numeric', month:'2-digit', day:'2-digit' })}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3 row">
                                    <div class="col-sm-6">
                                        <label class="col-form-label">No Model</label>
                                        <input type="text" class="form-control" name="model" value="${model}" readonly>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-form-label">Area</label>
                                        <input type="text" class="form-control" name="area" value="${area}" readonly>
                                    </div>
                                </div>
                                <hr>
                                <div id="listReturInputs">
                                    <!-- Blok input retur pertama dengan nomor urut -->
                                    <div class="retur-item mb-4 p-3 border rounded shadow-sm bg-white">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 retur-item-header"><strong>Jenis Retur ke-1</strong></h6>
                                            <!-- Tombol Remove hanya tampil untuk blok tambahan -->
                                            <button type="button" class="btn btn-danger remove-item" style="display:none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Jenis</label>
                                               <select class="form-select select-item-type" name="items[0][item_type]" required>
                                                    ${ getItemTypeOptions() }
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Kode Warna</label>
                                                <select class="form-select select-kode-warna" name="items[0][kode_warna]" required>
                                                    ${ getKodeWarnaOptions() }
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Warna</label>
                                                <select class="form-select select-warna" name="items[0][warna]" required>
                                                    ${ getWarnaOptions() }
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Jml KGS</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control" name="items[0][kgs]" required>
                                                    <span class="input-group-text text-bold">KG</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Jml Cones</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="items[0][cones]" required>
                                                    <span class="input-group-text text-bold">CNS</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Jml Karung</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="items[0][karung]">
                                                    <span class="input-group-text text-bold">KRG</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Lot Retur</label>
                                                <select class="form-select select-lot-retur" name="items[0][lot_retur]" required>
                                                    ${ getLotRetur() }
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Kategori Retur</label>
                                                <select class="form-select select-kategori-retur" name="items[0][kategori_retur]" required>
                                                    ${ getKategoriRetur() }
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Alasan Retur</label>
                                                <textarea class="form-control" name="items[0][alasan_retur]" rows="2" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mt-2">
                                    <button type="button" id="addMoreItem" class="btn btn-info"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-info w-100"><i class="fas fa-paper-plane"></i> Ajukan Retur</button>
                                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            `;

        // Render Tabel Data
        const tableBody = document.getElementById('bodyData');
        tableBody.innerHTML = `
        <div class="table-responsive">
            <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">No Model</th>
                        <th class="text-center">Area</th>
                        <th class="text-center">Item Type</th>
                        <th class="text-center">Kode Warna</th>
                        <th class="text-center">PO (KGS)</th>
                        <th class="text-center">PPH</th>
                        <th class="text-center">Kirim</th>
                        <th class="text-center">Estimasi Retur</th>
                    </tr>
                </thead>
                <tbody>
                    ${ buildTableRows(data, aggregateKeys) }
                </tbody>
            </table>
        </div>
        `;

        // Inisialisasi DataTables (pastikan plugin DataTables sudah disertakan)
        $(document).ready(function() {
            $('#dataTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        });

        // Inisialisasi fungsi untuk tombol Add More di modal
        initAddMore();
    }

    /**
     * Fungsi untuk menambahkan blok input retur tambahan dan fungsi hapus
     */
    function initAddMore() {
        let itemIndex = 1; // indeks untuk blok input berikutnya

        document.getElementById('addMoreItem').addEventListener('click', function() {
            // Clone blok retur-item pertama sebagai template
            let template = document.querySelector('.retur-item');
            let newBlock = template.cloneNode(true);

            // Update header untuk menampilkan nomor urut (Jenis Retur ke-(itemIndex+1))
            newBlock.querySelector('.retur-item-header').innerHTML = `<strong>Jenis Retur ke-${itemIndex + 1}</strong>`;

            // Bersihkan nilai input pada cloned block dan perbarui atribut name
            newBlock.querySelectorAll('input').forEach(function(input) {
                input.value = '';
                let name = input.getAttribute('name');
                name = name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']');
                input.setAttribute('name', name);
            });
            newBlock.querySelectorAll('select').forEach(function(select) {
                let name = select.getAttribute('name');

                // Isi ulang select berdasarkan tipenya
                if (name.includes('[item_type]')) {
                    select.innerHTML = getItemTypeOptions();
                } else if (name.includes('[kategori_retur]')) {
                    select.innerHTML = getKategoriRetur();
                } else if (name.includes('[kode_warna]')) {
                    select.innerHTML = getKodeWarnaOptions();
                } else if (name.includes('[warna]')) {
                    select.innerHTML = getWarnaOptions();
                } else if (name.includes('[lot_retur]')) {
                    select.innerHTML = getLotRetur();
                }

                // Perbarui name dengan index yang baru
                name = name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']');
                select.setAttribute('name', name);
            });

            newBlock.querySelectorAll('textarea').forEach(function(textarea) {
                textarea.value = '';
                let name = textarea.getAttribute('name');
                name = name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']');
                textarea.setAttribute('name', name);
            });


            // Tampilkan tombol remove di blok tambahan dan tetapkan event handler untuk menghapus blok tersebut
            let removeBtn = newBlock.querySelector('.remove-item');
            removeBtn.style.display = 'block';
            removeBtn.addEventListener('click', function() {
                newBlock.remove();
                // Update ulang nomor urut pada blok yang tersisa
                updateReturItemNumbers();
            });

            document.getElementById('listReturInputs').appendChild(newBlock);
            itemIndex++;
            updateReturItemNumbers();
        });
    }

    /**
     * Fungsi untuk memperbarui nomor urut pada setiap blok retur-item
     */
    function updateReturItemNumbers() {
        const items = document.querySelectorAll('#listReturInputs .retur-item');
        items.forEach((item, index) => {
            item.querySelector('.retur-item-header').innerHTML = `<strong>Jenis Retur ke-${index + 1}</strong>`;
        });
    }
</script>
<?php $this->endSection(); ?>