<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?php if (session()->getFlashdata('success')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: '<?= session()->getFlashdata('success') ?>',
                confirmButtonColor: '#4a90e2'
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
                html: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#4a90e2'
            });
        });
    </script>
<?php endif; ?>
<style>
    .loading-spinner {
        background: rgba(255, 255, 255, 0.8);
        position: absolute;
        padding: 8px 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
    }

    .kebutuhan-item {
        position: relative;
        /* biar spinner-nya bisa absolute di dalam */
    }
</style>

<div class="container-fluid py-4">
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Form Buka PO Tambahan <?= $area ?></h5>
                <a href="<?= base_url($role . '/po_tambahan/' . $area) ?>" class="btn bg-gradient-info"> Kembali</a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mt-4">
        <div class="card-body">
            <form action="<?= base_url($role . '/savePoTambahan/' . $area) ?>" method="post">

                <div id="kebutuhan-container">
                    <label>Pilih Bahan Baku</label>
                    <div class="kebutuhan-item">
                        <div class=" row">
                            <div class="col-md-6">
                                <!-- No Model -->
                                <div class="form-group">
                                    <label>No Model</label>
                                    <select class="form-control select-no-model" name="no_model[0][no_model]" required>
                                        <option value="">Pilih No Model</option>
                                        <?php foreach ($noModel as $m): ?>
                                            <option value="<?= $m ?>" data-no-model="<?= $m ?>"><?= $m ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Item Type -->
                                <div class="form-group">
                                    <label>Item Type</label>
                                    <select class="form-control item-type" name="items[0][item_type]" required>
                                        <option value="">Pilih Item Type</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class=" row">

                            <div class="col-md-6">
                                <!-- Kode Warna -->
                                <div class="form-group">
                                    <label>Kode Warna</label>
                                    <select class="form-control kode-warna" name="items[0][kode_warna]" required>
                                        <option value="">Pilih Kode Warna</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Item Type -->
                                <div class="form-group">
                                    <div class="col"><label>Color</label>
                                        <input type="text" class="form-control color" name="items[0][color]" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="populate-size-wrapper">
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <div class="col"><label>Total Kg</label>
                        <input type="number" class="form-control total-kg" name="items[0][kg_po]" readonly required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlInput1">Keterangan</label>
                    <textarea class="form-control" name="keterangan" id="keterangan"></textarea>
                </div>

                <button type="button" class="btn btn-info w-100" id="btn-save">Save</button>
                <div class="d-none" id="populateSizeTemplate">
                    <div class="size-block mb-3 p-1 border rounded shadow-sm bg-white">

                        <!-- Judul Style Size -->
                        <div class="row">
                            <div class="col-12">
                                <hr class="mt-1 mb-2">
                                <h7 class="text-dark fw-bold label-style-size text-uppercase"></h7>
                                <hr class="mb-3">
                                <input type="hidden" class="form-control style-size-hidden" name="items[0][style_size]">
                                <input type="hidden" class="form-control color" name="items[0][color]">
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="row">
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Kg MU</label>
                                    <input type="text" class="form-control kg-mu" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>BS Mesin (Kg)</label>
                                    <input type="text" class="form-control bs-mesin" name="items[0][bs_mesin]" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>BS Setting (Pcs)</label>
                                    <input type="text" class="form-control bs-setting" name="items[0][bs_setting]" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>(+) Packing (Pcs)</label>
                                    <input type="number" class="form-control pcs-po">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>PO (+) Kg</label>
                                    <input type="number" class="form-control kg-po" name="items[0][kg_po]" readonly required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>PO (+) Cones</label>
                                    <input type="number" class="form-control cns-po">
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button class="btn btn-danger remove-size-row w-100"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Pastikan jQuery load pertama -->
    <!-- Tambahkan ini di layout HTML-mu -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            const base = '<?= base_url() ?>';
            const role = '<?= $role ?>';
            const area = '<?= esc($area) ?>';
            const materialDataCache = {};

            const noModelOptions = $('.select-no-model').first().html();

            // Inisialisasi Select2 pada konteks tertentu
            function initSelect2(ctx) {
                $(ctx).find('.select-no-model, .select-style-size, .item-type, .kode-warna')
                    .select2({
                        width: '100%',
                        allowClear: true
                    });
            }

            // Tambah tab baru

            initSelect2(document);

            // Handler saat No Model dipilih
            $(document).on('change', '.select-no-model', function() {
                const $row = $(this).closest('.kebutuhan-item');
                const modelCode = $(this).find('option:selected').data('no-model');

                const $ss = $row.find('.item-type').empty().append('<option value="">Pilih Kode Benang</option>').trigger('change');
                $row.find('.item-type, .kode-warna').empty().append('<option value="">Pilih Item Type</option>').trigger('change');
                $row.find('.color, .kg-mu, .kg-po, .pcs-po').val('');

                if (!modelCode) return;
                if (!$row.find('.loading-spinner').length) {
                    $row.append('<div class="loading-spinner">Sedang Mengambil material...</div>');
                }

                // Tampilkan spinner
                $row.find('.loading-spinner').show();
                fetch(`${base}/${role}/poTambahanDetail/${modelCode}/${area}`)
                    .then(r => r.ok ? r.json() : Promise.reject(r.statusText))
                    .then(json => {
                        const itemTypes = json.item_types;
                        const materialData = json.material;

                        itemTypes.forEach(row => {
                            $ss.append(`<option value="${row.item_type}">${row.item_type}</option>`);
                        });

                        $row.data('material', materialData);
                        $row.data('bsMesin', json.bs_mesin);
                        $row.data('bsSetting', json.bs_setting);
                        $ss.trigger('change');
                    })
                    .catch(err => console.error('Gagal load item_type:', err))
                    .finally(() => {
                        // Sembunyikan loading spinner
                        $row.find('.loading-spinner').hide();
                    });
            });
            // Event listener saat Item Type dipilih
            $(document).on('change', '.item-type', function() {
                const $row = $(this).closest('.kebutuhan-item');
                const modelCode = $row.find('.select-no-model option:selected').data('no-model');

                if (!modelCode) return;

                const materialData = $row.data('material'); // ambil dari data-attribute
                if (!materialData) return;

                populateKodeWarnas(materialData, modelCode, $row);
            });


            // Isi dropdown Kode Warna berdasarkan data item type
            function populateKodeWarnas(matData, modelCode, $row) {
                const selectedItemType = $row.find('.item-type').val();
                const $it = $row.find('.kode-warna').empty().append('<option value="">Pilih Kode Warna</option>');

                if (!selectedItemType || !matData[selectedItemType]) return;

                const kodeWarnas = matData[selectedItemType].kode_warna;

                Object.entries(kodeWarnas).forEach(([kode, detail]) => {
                    const styleSizes = detail.style_size || [];

                    const cocok = styleSizes.some(item => item.no_model === modelCode);
                    if (cocok) {
                        $it.append(`<option value="${kode}" data-color="${detail.color}">${kode}</option>`);
                    }
                });

                $it.trigger('change');
            }

            // Handler gabungan saat Kode Warna dipilih
            $(document).on('change', '.kode-warna', function() {
                const $opt = $(this).find(':selected');
                const $row = $(this).closest('.kebutuhan-item');

                const color = $opt.data('color') || '';
                $row.find('.color').val(color);

                const itemType = $row.find('.item-type').val();
                const kodeWarna = $(this).val();
                const modelCode = $row.find('.select-no-model option:selected').data('no-model');

                const materialData = $row.data('material');
                const bsMesinMap = $row.data('bsMesin') || {};
                const bsSettingMap = $row.data('bsSetting') || {};

                if (!materialData || !materialData[itemType] || !materialData[itemType].kode_warna[kodeWarna]) {
                    return;
                }

                const detail = materialData[itemType].kode_warna[kodeWarna];
                const allStyleSizes = (detail.style_size || []).filter(s => s.no_model === modelCode);

                const $wrapper = $row.find('.populate-size-wrapper').empty();

                allStyleSizes.forEach((style, i) => {
                    const $template = $('#populateSizeTemplate').clone().removeAttr('id').removeClass('d-none');

                    const size = style.style_size;

                    $template.find('.color').val(size || '');
                    $template.find('.kg-mu').val(parseFloat(style.kg_mu || 0).toFixed(2));
                    $template.find('.bs-mesin').val(((bsMesinMap[size] || 0) / 1000).toFixed(2)); // Convert gram to kg
                    $template.find('.bs-setting').val(bsSettingMap[size] || 0);
                    $template.find('.pcs-po').val(style.pcs_po || '');
                    $template.find('.kg-po').val(style.kg_po || '');
                    $template.find('.cns-po').val(style.cns_po || '');
                    $template.find('.label-style-size').text(size);
                    $template.find('.style-size-hidden').val(size);

                    $template.find('input').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace('[0]', `[${i}]`));
                        }
                    });

                    $wrapper.append($template);
                });
            });

            // Fungsi untuk hitung total KG PO

            function hitungTotalKg() {
                let total = 0;
                $('.kg-po').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    total += val;
                });

                $('.total-kg').val(total.toFixed(2));
            }

            // Saat user mengisi PO Pcs, hitung otomatis KG PO
            $(document).on('input', '.pcs-po', function() {
                const $row = $(this).closest('.size-block');
                const $wrapper = $row.closest('.kebutuhan-item');
                const pcs = parseFloat($(this).val()) || 0;
                const bsMesin = parseFloat($row.find('.bs-mesin').val()) || 0;
                const bsSetting = parseFloat($row.find('.bs-setting').val()) || 0;

                const itemType = $wrapper.find('.item-type').val();
                const kodeWarna = $wrapper.find('.kode-warna').val();
                const modelCode = $wrapper.find('.select-no-model option:selected').data('no-model');
                const styleSize = $row.find('.style-size-hidden').val();

                const materialData = $wrapper.data('material');

                let composition = 0,
                    gw = 0,
                    loss = 0;

                if (
                    materialData &&
                    materialData[itemType] &&
                    materialData[itemType].kode_warna[kodeWarna]
                ) {
                    const styleList = materialData[itemType].kode_warna[kodeWarna].style_size || [];

                    const match = styleList.find(item => item.no_model === modelCode && item.style_size === styleSize);
                    if (match) {
                        composition = parseFloat(match.composition) || 0;
                        gw = parseFloat(match.gw) || 0;
                        loss = parseFloat(match.loss) || 0;
                    }
                }

                const stKg = bsSetting * composition * gw / 100 / 1000;
                const pluspck = pcs * composition * gw / 100 / 1000;
                const kgPo = (pluspck * (1 + loss / 100)) + bsMesin + stKg;

                $row.find('.kg-po').val(kgPo.toFixed(2));

                // Setelah update, hitung total
                hitungTotalKg();
            });

            hitungTotalKg();

            //Delete Row Size
            $(document).on('click', '.remove-size-row', function(e) {
                e.preventDefault();

                const $row = $(this).closest('.size-block');

                // Konfirmasi dulu, boleh juga langsung hapus tanpa konfirmasi
                if (confirm('Yakin ingin menghapus baris ini?')) {
                    $row.remove();
                }
            });

            //Save data
            $('#btn-save').on('click', function() {
                let formData = [];

                $('.kebutuhan-item').each(function() {
                    const no_model = $(this).find('.select-no-model').val();
                    const item_type = $(this).find('.item-type').val();
                    const kode_warna = $(this).find('.kode-warna').val();
                    const color = $(this).find('.color').first().val(); // Ambil color utama
                    const keterangan = $('#keterangan').val();

                    $(this).find('.size-block').each(function() {
                        formData.push({
                            no_model: no_model,
                            item_type: item_type,
                            kode_warna: kode_warna,
                            color: color,
                            style_size: $(this).find('.style-size-hidden').val(),
                            bs_mesin: $(this).find('.bs-mesin').val(),
                            bs_setting: $(this).find('.bs-setting').val(),
                            pcs_po: $(this).find('.pcs-po').val(),
                            kg_po: $(this).find('.kg-po').val(),
                            cns_po: $(this).find('.cns-po').val(),
                            keterangan: keterangan
                        });
                    });
                });

                console.log(formData); // Debug sebelum submit

                $.ajax({
                    url: base + '/' + role + '/savePoTambahan/' + area,
                    method: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        if (response.status === 'ok' || response.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                icon: 'success',
                                html: `
                        <strong>Sukses:</strong> ${response.sukses || 0}<br>
                        <strong>Gagal:</strong> ${response.gagal || 0}
                    `,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                icon: 'error',
                                text: response.message || 'Gagal menyimpan data.',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            icon: 'error',
                            text: 'Terjadi kesalahan saat menyimpan data.',
                        });
                    }
                });
            });

        });
    </script>


    <?php $this->endSection(); ?>