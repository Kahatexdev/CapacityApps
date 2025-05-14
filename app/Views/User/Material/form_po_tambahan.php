<?php $this->extend('User/layout'); ?>
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

<div class="container-fluid py-4">
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Form Buka PO Tambahan <?= basename(current_url()) ?></h5>
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
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">1</button>
                        </div>
                    </nav>
                    <!-- Tab Konten Item Type -->
                    <!-- HTML Struktur (tab-content seperti di atas) -->
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel">
                            <div class="kebutuhan-item" data-index="0">
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
                                        <!-- Style Size -->
                                        <div class="form-group">
                                            <label>Style Size</label>
                                            <select class="form-control select-style-size" name="style_size[0][no_model]" required>
                                                <option value="">Pilih Style Size</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class=" row">
                                    <div class="col-md-6">
                                        <!-- Item Type -->
                                        <div class="form-group">
                                            <label>Item Type</label>
                                            <select class="form-control item-type" name="items[0][item_type]" required>
                                                <option value="">Pilih Item Type</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Kode Warna -->
                                        <div class="form-group">
                                            <label>Kode Warna</label>
                                            <select class="form-control kode-warna" name="items[0][kode_warna]" required>
                                                <option value="">Pilih Kode Warna</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Item Type -->
                                        <div class="form-group">
                                            <div class="col"><label>Color</label>
                                                <input type="text" class="form-control color" name="items[0][color]" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Kode Warna -->
                                        <div class="form-group">
                                            <div class="col"><label>Kg MU</label>
                                                <input type="text" class="form-control kg-mu" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Pcs Po(+) -->
                                        <div class="form-group">
                                            <div class="col"><label>PO (+) Pcs</label>
                                                <input type="number" class="form-control pcs-po">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Kg Po(+) -->
                                        <div class="form-group">
                                            <div class="col"><label>PO (+) Kg</label>
                                                <input type="number" class="form-control kg-po" name="items[0][kg_po]" readonly required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Cones -->
                                        <div class="form-group">
                                            <div class="col"><label>PO (+) Cones</label>
                                                <input type="number" class="form-control cns-po">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="text-center my-2">
                                    <button type="button" class="btn btn-outline-info add-more"><i class="fas fa-plus"></i></button>
                                    <button type="button" class="btn btn-outline-danger remove"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <label for="exampleFormControlInput1">Keterangan</label>
                    <textarea class="form-control" name="keterangan" id="keterangan"></textarea>
                </div>
                <button type="button" class="btn btn-info w-100" id="btn-save">Save</button>
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
        let tabIndex = 2;

        const $navTab = $('#nav-tab');
        const $navTabContent = $('#nav-tabContent');
        // simpan opsi no-model awal untuk template tab baru
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
        function addNewTab() {
            const idx = tabIndex - 1; // 0-based index untuk nama array
            // buat tombol tab
            const $btn = $(`
            <button class="nav-link" id="nav-tab-${tabIndex}-button"
                    data-bs-toggle="tab" data-bs-target="#nav-content-${tabIndex}"
                    type="button" role="tab" aria-selected="false">
                ${tabIndex}
            </button>
        `);
            $navTab.append($btn);

            // buat pane
            const paneHtml = `
            <div class="tab-pane fade" id="nav-content-${tabIndex}" role="tabpanel"
                 aria-labelledby="nav-tab-${tabIndex}-button">
                <div class="kebutuhan-item" data-index="${idx}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No Model</label>
                                <select class="form-control select-no-model" name="no_model[${idx}][no_model]" required>
                                    ${noModelOptions}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Style Size</label>
                                <select class="form-control select-style-size" name="style_size[${idx}][style_size]" required>
                                    ${noModelOptions}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Type</label>
                                <select class="form-control item-type" name="items[${idx}][item_type]" required>
                                    <option value="">Pilih Item Type</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kode Warna</label>
                                <select class="form-control kode-warna" name="items[${idx}][kode_warna]" required>
                                    <option value="">Pilih Kode Warna</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Color</label>
                            <input type="text" class="form-control color" name="items[${idx}][color]" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Kg MU</label>
                            <input type="text" class="form-control kg-mu" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>PO (+) Pcs</label>
                            <input type="number" class="form-control pcs-po">
                        </div>
                        <div class="col-md-6">
                            <label>PO (+) Kg</label>
                            <input type="number" class="form-control kg-po" name="items[${idx}][kg_po]" readonly required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>PO (+) Cones</label>
                            <input type="number" class="form-control cns-po">
                        </div>
                    </div>
                    <div class="text-center my-2">
                        <button type="button" class="btn btn-outline-info add-more"><i class="fas fa-plus"></i></button>
                        <button type="button" class="btn btn-outline-danger remove"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `;
            const $pane = $(paneHtml);
            $navTabContent.append($pane);

            // re-init Select2 di tab baru
            initSelect2($pane);

            // tunjukkan tab baru
            new bootstrap.Tab($btn[0]).show();

            tabIndex++;
        }

        // Hapus tab (tombol Remove baik di tab lama maupun baru)
        function removeTab($btn, $pane) {
            if ($navTab.children().length <= 1) {
                return alert('Minimal harus ada satu tab.');
            }
            $btn.remove();
            $pane.remove();
            // setelah hapus, selalu aktifkan tab pertama
            new bootstrap.Tab($navTab.find('button').first()[0]).show();
        }

        // -----------------------
        // Binding awal
        // -----------------------
        initSelect2(document);
        $(document).on('click', '.add-more', addNewTab);
        $(document).on('click', '.remove', function() {
            const $pane = $(this).closest('.tab-pane');
            const target = '#' + $pane.attr('id');
            const $btn = $navTab.find(`[data-bs-target="${target}"]`);
            removeTab($btn, $pane);
        });

        // Handler saat No Model dipilih
        $(document).on('change', '.select-no-model', function() {
            const $row = $(this).closest('.kebutuhan-item');
            const modelCode = $(this).find('option:selected').data('no-model');

            const $ss = $row.find('.select-style-size').empty().append('<option value="">Pilih Style Size</option>').trigger('change');
            $row.find('.item-type, .kode-warna').empty().append('<option value="">Pilih</option>').trigger('change');
            $row.find('.color, .kg-mu, .kg-po, .pcs-po').val('');

            if (!modelCode) return;

            fetch(`${base}/${role}/getStyleSize/${area}/${modelCode}`)
                .then(r => r.ok ? r.json() : Promise.reject(r.statusText))
                .then(json => {
                    json.forEach(row => {
                        $ss.append(`<option value="${row.size}">${row.size}</option>`);
                    });
                    $ss.trigger('change');
                })
                .catch(err => console.error('Gagal load style_size:', err));
        });

        // Handler saat Style Size dipilih
        $(document).on('change', '.select-style-size', function() {
            const $row = $(this).closest('.kebutuhan-item');
            const modelCode = $row.find('.select-no-model option:selected').data('no-model');
            const styleSize = $(this).val();

            $row.find('.item-type, .kode-warna').empty().append('<option value="">Pilih</option>').trigger('change');
            $row.find('.color, .kg-mu, .kg-po, .pcs-po').val('');

            if (!modelCode || !styleSize) return;

            if (materialDataCache[modelCode]) {
                populateItemTypes(materialDataCache[modelCode], modelCode, $row);
            } else {
                fetch(`${base}/${role}/poTambahanDetail/${modelCode}/${styleSize}`)
                    .then(res => res.ok ? res.json() : Promise.reject('Error response'))
                    .then(json => {
                        if (!json.material) throw 'Material kosong';
                        materialDataCache[modelCode] = json.material;
                        populateItemTypes(json.material, modelCode, $row);
                    })
                    .catch(err => console.error('Fetch error:', err));
            }
        });

        // Isi dropdown Item Type berdasarkan data material
        function populateItemTypes(matData, modelCode, $row) {
            const $it = $row.find('.item-type').empty().append('<option value="">Pilih Item Type</option>');
            Object.entries(matData).forEach(([type, info]) => {
                if (info.no_model === modelCode) {
                    $it.append(`<option value="${type}">${type}</option>`);
                }
            });
            $it.trigger('change');
        }

        // Handler saat Item Type dipilih → isi Kode Warna
        $(document).on('change', '.item-type', function() {
            const $row = $(this).closest('.kebutuhan-item');
            const type = $(this).val();
            const modelCode = $row.find('.select-no-model option:selected').data('no-model');
            const matData = materialDataCache[modelCode] || {};

            const $kw = $row.find('.kode-warna').empty().append('<option value="">Pilih Kode Warna</option>');
            $row.find('.color, .kg-mu, .kg-po, .pcs-po').val('');

            if (matData[type]) {
                matData[type].kode_warna.forEach(w => {
                    $kw.append(`
                <option value="${w.kode_warna}"
                        data-color="${w.color}"
                        data-kg-mu="${w.total_kg}"
                        data-composition="${w.composition}"
                        data-gw="${w.gw}"
                        data-loss="${w.loss}">
                    ${w.kode_warna}
                </option>`);
                });
            }
            $kw.trigger('change');
        });

        // Handler gabungan saat Kode Warna dipilih
        $(document).on('change', '.kode-warna', function() {
            const $opt = $(this).find(':selected');
            const $row = $(this).closest('.kebutuhan-item');

            $row.find('.color').val($opt.data('color') || '');
            $row.find('.kg-mu').val(parseFloat($opt.data('kg-mu') || 0).toFixed(2));
            $row.find('.kg-po, .pcs-po').val('');
        });

        // Saat user mengisi PO Pcs, hitung otomatis KG PO
        $(document).on('input', '.pcs-po', function() {
            const $row = $(this).closest('.kebutuhan-item');
            const pcs = parseFloat($(this).val()) || 0;
            const $opt = $row.find('.kode-warna option:selected');

            const composition = parseFloat($opt.data('composition')) || 0;
            const gw = parseFloat($opt.data('gw')) || 0;
            const loss = parseFloat($opt.data('loss')) || 0;

            const base = pcs * composition * gw / 100 / 1000;
            const kgPo = base * (1 + loss / 100);

            $row.find('.kg-po').val(kgPo.toFixed(2));
        });

        //Save data
        $('#btn-save').on('click', function() {
            let formData = [];

            $('.kebutuhan-item').each(function() {
                formData.push({
                    no_model: $(this).find('.select-no-model').val(),
                    style_size: $(this).find('.select-style-size').val(),
                    item_type: $(this).find('.item-type').val(),
                    kode_warna: $(this).find('.kode-warna').val(),
                    color: $(this).find('.color').val(),
                    pcs_po: $(this).find('.pcs-po').val(),
                    kg_po: $(this).find('.kg-po').val(),
                    cns_po: $(this).find('.cns-po').val(),
                    keterangan: $('#keterangan').val()
                });
            });

            console.log(formData); // Lihat data sebelum dikirim

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
                            location.reload(); // reload setelah OK ditekan
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
                    aSwal.fire({
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