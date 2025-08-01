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
    .loading-spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        /* semi-transparent */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        /* biar di atas semuanya */
    }

    .loading-spinner-content img {
        max-width: 200px;
        margin-top: 10px;
    }

    .kebutuhan-item {
        position: relative;
        /* biar spinner-nya bisa absolute di dalam */
    }
</style>
<div class="loading-spinner-overlay d-none" id="loading-spinner">
    <div class="loading-spinner-content text-center">
        <h4>loading...</h4>
        <img src="<?= base_url('assets/newspin.gif') ?>" alt="Loading...">
    </div>
</div>
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
                                <!-- Color -->
                                <div class="form-group">
                                    <div class="col"><label>Color</label>
                                        <input type="text" class="form-control color" name="items[0][color]" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Terima -->
                                <div class="form-group">
                                    <div class="col"><label>Terima (Kg)</label>
                                        <input type="text" class="form-control terima" name="items[0][terima]" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Sisa BB di Mesin (Kg) -->
                                <div class="form-group">
                                    <label>Sisa BB di Mesin (Kg)</label>
                                    <input type="number" class="form-control sisa-mc-kg" name="items[0][sisa_mc_kg]">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- (+) Mesin (Cns) -->
                                <div class="form-group">
                                    <label>(+) Mesin (Cns)</label>
                                    <input type="number" class="form-control poplus-mc-cns" name="items[0][poplus_mc_cns]">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- (+) Packing (Cns) -->
                                <div class="form-group">
                                    <label>(+) Packing (Cns)</label>
                                    <input type="number" class="form-control plus-pck-cns" name="items[0][plus_pck_cns]">
                                </div>
                            </div>
                        </div>
                        <div class="row populate-size-wrapper">
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <div class="col"><label>Total Kg</label>
                        <input type="number" class="form-control total-kg" name="items[0][total_kg_po]" readonly required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col"><label>Total Cones</label>
                        <input type="number" class="form-control total-cns" name="items[0][total_cns_po]" readonly required>
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
                            <div class="col-12 text-center">
                                <hr class="mt-1 mb-2">
                                <h5 class="text-dark fw-bold label-style-size text-uppercase"></h5>
                                <hr class="mb-3">
                                <input type="hidden" class="form-control style-size-hidden" name="items[0][style_size]">
                                <input type="hidden" class="form-control color" name="items[0][color]">
                            </div>
                        </div>
                        <hr class="mb-3" style="border-color: #6c757d;">

                        <!-- Form Fields -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Pesanan<br>Kgs</label>
                                    <input type="text" class="form-control kg-mu" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sisa<br>Order</label>
                                    <input type="text" class="form-control sisa-order" name="items[0][sisa_order]" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>BS Mesin<br>(Kg)</label>
                                    <input type="text" class="form-control bs-mesin" name="items[0][bs_mesin]" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>BS<br>Setting</label>
                                    <input type="text" class="form-control bs-setting" name="items[0][bs_setting]" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>(+) Mesin<br>(Kg)</label>
                                    <input type="text" class="form-control poplus-mc-kg" name="items[0][poplus_mc_kg]" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>(+) Pcs<br>Packing</label>
                                    <input type="number" class="form-control plus-pck-pcs">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>(+) Kg<br>Packing</label>
                                    <input type="text" class="form-control plus-pck-kg" name="items[0][plus_pck_kg]" readonly required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Lebih<br>Pakai(Kg)</label>
                                    <input type="text" class="form-control lebih-pakai" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex align-items-end">
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
                let loading = document.getElementById('loading-spinner');
                const $ss = $row.find('.item-type').empty().append('<option value="">Pilih Kode Benang</option>').trigger('change');
                $row.find('.item-type, .kode-warna').empty().append('<option value="">Pilih Item Type</option>').trigger('change');
                $row.find('.color, .kg-mu, .kg-po, .pcs-po').val('');

                if (!modelCode) return;

                loading.classList.remove('d-none');
                fetch(`${base}/${role}/poTambahanDetail/${modelCode}/${area}`)
                    .then(r => r.ok ? r.json() : Promise.reject(r.statusText))
                    .then(json => {
                        const itemTypes = json.item_types;
                        const materialData = json.material;

                        itemTypes.forEach(row => {
                            $ss.append(`<option value="${row.item_type}">${row.item_type}</option>`);
                        });

                        $row.data('material', materialData);
                        $row.data('sisaOrder', json.sisa_order);
                        $row.data('bsMesin', json.bs_mesin);
                        $row.data('bsSetting', json.bs_setting);
                        $row.data('bruto', json.bruto);
                        $ss.trigger('change');
                    })
                    .catch(err => console.error('Gagal load item_type:', err))
                    .finally(() => {
                        // Sembunyikan loading spinner
                        loading.classList.add('d-none')
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
                        $it.append(`<option value="${kode}" data-color="${detail.color}" data-terima="${detail.kgs_out}">${kode}</option>`);
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

                const terima = parseFloat($opt.data('terima')) || 0;
                $row.find('.terima').val(terima.toFixed(2));

                const itemType = $row.find('.item-type').val();
                const kodeWarna = $(this).val();
                const modelCode = $row.find('.select-no-model option:selected').data('no-model');

                const materialData = $row.data('material');
                const sisaOrderMap = $row.data('sisaOrder') || {};
                const bsMesinMap = $row.data('bsMesin') || {};
                const bsSettingMap = $row.data('bsSetting') || {};
                const brutoMap = $row.data('bruto') || {};


                if (!materialData || !materialData[itemType] || !materialData[itemType].kode_warna[kodeWarna]) {
                    return;
                }

                const detail = materialData[itemType].kode_warna[kodeWarna];
                const allStyleSizes = (detail.style_size || []).filter(s => s.no_model === modelCode);

                const $wrapper = $row.find('.populate-size-wrapper').empty();

                allStyleSizes.forEach((style, i) => {
                    const $template = $('#populateSizeTemplate').clone().removeAttr('id').removeClass('d-none');

                    const size = style.style_size;
                    const kgMu = parseFloat(style.kg_mu || 0);
                    const composition = parseFloat(style.composition || 0);
                    const gw = parseFloat(style.gw || 0);

                    $template.find('.color').val(size || '');
                    $template.find('.kg-mu').val(parseFloat(style.kg_mu || 0).toFixed(2));
                    $template.find('.sisa-order').val(sisaOrderMap[size] || 0);
                    $template.find('.bs-mesin').val(((bsMesinMap[size] || 0) / 1000).toFixed(2)); // Convert gram to kg
                    $template.find('.bs-setting').val(bsSettingMap[size] || 0);

                    const rawBruto = parseFloat(brutoMap[size] || 0);
                    const brutoKg = gw > 0 ?
                        rawBruto * composition * gw / 100 / 1000 :
                        0;

                    // 3) Hitung lebih-pakai = brutoKg - kgMu, minimal 0
                    const lebih = Math.max(0, brutoKg - kgMu);

                    $template.find('.lebih-pakai').val(lebih.toFixed(2));

                    $template.find('.plus-pck-pcs').val(style.pcs_po || '');
                    $template.find('.plus-pck-kg').val(style.kg_po || '');
                    $template.find('.po-pck-cns').val(style.cns_po || '');
                    $template.find('.label-style-size').text(size);
                    $template.find('.style-size-hidden').val(size);

                    $template.find('input').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace('[0]', `[${i}]`));
                        }
                    });

                    // Bungkus tiap card ke dalam .col-md-4
                    const $col = $('<div class="col-md-4 mb-3"></div>').append($template);
                    $wrapper.append($col);

                    hitungPoplusMc($template, $row);
                });
            });

            // Fungsi untuk hitung total Cns PO
            function hitungTotalCns() {
                let plusMcCns = 0;
                let plusPckCns = 0;

                $('.poplus-mc-cns').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    plusMcCns += val;
                });

                $('.plus-pck-cns').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    plusPckCns += val;
                });

                const total = plusMcCns + plusPckCns;

                $('.total-cns').val(total.toFixed(2));
            }

            // Saat nilai poplus-mc-cns berubah, hitung ulang total
            $(document).on('input', '.poplus-mc-cns', function() {
                hitungTotalCns();
            });

            // Saat nilai plus-pck-cns berubah, hitung ulang total
            $(document).on('input', '.plus-pck-cns', function() {
                hitungTotalCns();
            });

            // Fungsi untuk hitung total KG PO
            function hitungTotalKg() {
                let plusPckTotal = 0;
                let poplusMcTotal = 0;
                let sisaMcTotal = 0;

                $('.plus-pck-kg').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    plusPckTotal += val;
                });

                $('.poplus-mc-kg').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    poplusMcTotal += val;
                });

                $('.sisa-mc-kg').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    sisaMcTotal += val;
                });

                const total = (plusPckTotal + poplusMcTotal) - sisaMcTotal;

                $('.total-kg').val(total.toFixed(2));
            }

            // Saat nilai sisa-mc-kg berubah, hitung ulang total
            $(document).on('input', '.sisa-mc-kg', function() {
                hitungTotalKg();
            });

            // Saat user mengisi PO Pcs, hitung otomatis Plus Pck Kg
            $(document).on('input', '.plus-pck-pcs', function() {
                const $row = $(this).closest('.size-block');
                const $wrapper = $row.closest('.kebutuhan-item');
                const pcs = parseFloat($(this).val()) || 0;

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

                const pluspck = pcs * composition * gw / 100 / 1000;
                const kgPlusPck = pluspck * (1 + (loss / 100));

                $row.find('.plus-pck-kg').val(kgPlusPck.toFixed(2));

                // Setelah update, hitung total
                hitungTotalKg();
            });

            // Fungsi Untuk Menghitung Po Tambahan Mesin Kg
            function hitungPoplusMc($row, $wrapper) {
                const sisaOrder = parseFloat($row.find('.sisa-order').val()) || 0;
                const bsMesin = parseFloat($row.find('.bs-mesin').val()) || 0;
                const bsSetting = parseFloat($row.find('.bs-setting').val()) || 0;

                const itemType = $wrapper.find('.item-type').val();
                const kodeWarna = $wrapper.find('.kode-warna').val();
                const modelCode = $wrapper.find('.select-no-model option:selected').data('no-model');
                const styleSize = $row.find('.style-size-hidden').val();

                const mat = $wrapper.data('material') || {};
                const brutoMap = $wrapper.data('bruto') || {};

                // ambil composition, gw, loss
                let composition = 0,
                    gw = 0,
                    loss = 0;
                if (mat[itemType] && mat[itemType].kode_warna[kodeWarna]) {
                    const styleList = mat[itemType].kode_warna[kodeWarna].style_size || [];
                    const match = styleList.find(s => s.no_model === modelCode && s.style_size === styleSize);
                    if (match) {
                        composition = parseFloat(match.composition) || 0;
                        gw = parseFloat(match.gw) || 0;
                        loss = parseFloat(match.loss) || 0;
                    }
                }

                // 1) stKg & sisaKeb (kg terpakai untuk setting & order)
                const stKg = bsSetting * composition * gw / 100 / 1000;
                const sisaKg = sisaOrder * composition * gw / 100 / 1000;

                // 2) brutoKg: rawBruto * comp * gw /100/1000
                const rawBruto = parseFloat(brutoMap[styleSize]) || 0;
                const brutoKg = gw > 0 ?
                    rawBruto * composition * gw / 100 / 1000 :
                    0;

                // 3) efficiency: pastikan pembagi ≠ 0
                const denom = brutoKg + bsMesin;
                const eff = denom > 0 ?
                    ((brutoKg - stKg) / denom) * 100 :
                    0;

                // 4) newKeb & estPoPlusMc
                const newKeb = eff > 0 ? sisaKg / eff * 100 : 0;
                const estPoPlusMc = Math.max(0, newKeb - sisaKg);

                console.log({
                    sisaOrder,
                    bsMesin,
                    bsSetting,
                    composition,
                    gw,
                    loss,
                    stKg,
                    sisaKg,
                    brutoKg,
                    eff,
                    newKeb,
                    estPoPlusMc
                });

                $row.find('.poplus-mc-kg').val(estPoPlusMc.toFixed(2));
                hitungTotalKg();
            }


            hitungTotalKg();

            //Delete Row Size
            $(document).on('click', '.remove-size-row', function(e) {
                e.preventDefault();

                const $row = $(this).closest('.col-md-4');

                // Konfirmasi dulu, boleh juga langsung hapus tanpa konfirmasi
                if (confirm('Yakin ingin menghapus baris ini?')) {
                    $row.remove();
                    hitungTotalKg();
                }
            });

            //Save data
            $('#btn-save').on('click', function() {
                let formData = [];
                let loading = document.getElementById('loading-spinner');
                loading.classList.remove('d-none')

                $('.kebutuhan-item').each(function() {
                    const no_model = $(this).find('.select-no-model').val();
                    const item_type = $(this).find('.item-type').val();
                    const kode_warna = $(this).find('.kode-warna').val();
                    const color = $(this).find('.color').first().val(); // Ambil color utama
                    const sisa_bb_mc = $(this).find('.sisa-mc-kg').val();
                    const terima_kg = $(this).find('.terima').first().val(); // Ambil color utama
                    const poplus_mc_cns = $(this).find('.poplus-mc-cns').val();
                    const plus_pck_cns = $(this).find('.plus-pck-cns').val();
                    const keterangan = $('#keterangan').val();

                    $(this).find('.size-block').each(function() {
                        formData.push({
                            no_model: no_model,
                            item_type: item_type,
                            kode_warna: kode_warna,
                            color: color,
                            sisa_bb_mc: sisa_bb_mc,
                            terima_kg: terima_kg,
                            poplus_mc_cns: poplus_mc_cns,
                            plus_pck_cns: plus_pck_cns,
                            style_size: $(this).find('.style-size-hidden').val(),
                            sisa_order_pcs: $(this).find('.sisa-order').val(),
                            bs_mesin_kg: $(this).find('.bs-mesin').val(),
                            bs_st_pcs: $(this).find('.bs-setting').val(),
                            poplus_mc_kg: $(this).find('.poplus-mc-kg').val(),
                            plus_pck_pcs: $(this).find('.plus-pck-pcs').val(),
                            plus_pck_kg: $(this).find('.plus-pck-kg').val(),
                            lebih_pakai_kg: $(this).find('.lebih-pakai').val(),
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
                    },
                    complete: function() {
                        loading.classList.add('d-none'); // Sembunyikan loading setelah selesai
                    }
                });
            });

        });
    </script>


    <?php $this->endSection(); ?>