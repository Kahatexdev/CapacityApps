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
                        <div class="col-6">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $title . ' ' . $area ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-center text-end gap-2">
                            <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                            <input type="text" class="form-control" id="no_model" value="" placeholder="No Model">
                            <button id="searchModel" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
                            <button type="button" class="btn btn-warning ms-2 btnRetur d-none" id="btn-retur">
                                <i class="fas fa-paper-plane"></i> Pengajuan Retur</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalPengajuanRetur" tabindex="-1" aria-labelledby="modalPengajuanReturLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <form id="formPengajuanRetur">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPengajuanReturLabel"><strong>Form Pengajuan Retur</strong> </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <div class="col-sm-6">
                                <label class="col-form-label">No Model</label>
                                <input type="text" class="form-control" name="model" value="" id="modelText" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="col-form-label">Area</label>
                                <input type="text" class="form-control" name="area" id="areaText" value="<?= $area ?>" readonly>
                            </div>
                        </div>
                        <hr>
                        <div id="listReturInputs">
                            <!-- Blok input retur pertama dengan nomor urut -->
                            <div class="retur-item mb-4 p-3 border rounded shadow-sm bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <div class="form-group">
                                        <label class="form-label">Kategori Retur</label>
                                        <select class="form-select select-kategori-retur" name="kategori_retur" required>
                                            <option> Pilih Kategori</option>
                                            <?php foreach ($kategori as $kt): ?>
                                                <option value="<?= $kt['nama_kategori'] ?>"> <?= $kt['nama_kategori'] ?> | <?= $kt['tipe_kategori'] ?></option>

                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <!-- Tombol Remove hanya tampil untuk blok tambahan -->
                                    <button type="button" class="btn btn-danger remove-item" style="display:none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label">Jenis | Kode Warna | Warna</label>
                                        <select class="form-select select-item-type" name="material" id="itemSelect" required>


                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-flex justify-content-between">

                                            <label class="form-label">Jml KGS</label>
                                            <label class="form-label" id="textMax">Max Retur</label>
                                        </div>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" name="kgs" id="kgs" required>
                                            <span class="input-group-text text-bold">KG</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Jml Cones</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="cones" required>
                                            <span class="input-group-text text-bold">CNS</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Jml Karung</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="karung">
                                            <span class="input-group-text text-bold">KRG</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Lot Retur</label>
                                        <select class="form-select select-lot-retur" name="lotRetur" id="lotRetur" required>

                                        </select>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Alasan Retur</label>
                                        <textarea class="form-control" name="keterangan" rows="2" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info w-100" id="submitRetur"><i class="fas fa-paper-plane"></i> Ajukan Retur</button>
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
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
        <div class="row mt-3 d-none" id="rowbawah">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div class="row" id="HeaderRow2">
                            <!-- Header dan tombol disusun secara dinamis -->
                        </div>
                    </div>
                    <div class="card-body" id="bodyData2">
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
        <div id="loading" style="display: none;">
            <h3>Sedang Menghitung...</h3>
            <img src="<?= base_url('assets/spinner.gif') ?>" alt="Loading...">
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">


            <div class="d-flex align-items-center justify-content-between">
                <h3 class="model-title mb-0">List Returan <?= $area ?></h3>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= base_url($role . '/exportExcelRetur/' . $area) ?>" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>

                </div>
            </div>
            <div class="table-responsive">
                <table class="table display text-center text-uppercase text-xs font-bolder table-bordered" id="dataTableRetur" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal Retur</th>
                            <th class="text-center">No Model</th>
                            <th class="text-center">Item Type</th>
                            <th class="text-center">Kode Warna</th>
                            <th class="text-center"> Warna</th>
                            <th class="text-center">Lot Retur</th>
                            <th class="text-center">KG Retur</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Keterangan GBN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $ls): ?>
                            <tr>
                                <td><?= $ls['tgl_retur'] ?></td>
                                <td><?= $ls['no_model'] ?></td>
                                <td><?= $ls['item_type'] ?></td>
                                <td><?= $ls['kode_warna'] ?></td>
                                <td><?= $ls['warna'] ?></td>
                                <td><?= $ls['lot_retur'] ?></td>
                                <td><?= $ls['kgs_retur'] ?></td>
                                <td><?= $ls['kategori'] ?></td>
                                <td><?= $ls['keterangan_gbn'] ?></td>

                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        const btnSearch = document.getElementById('searchModel');
        const returbtn = document.getElementById('btn-retur');
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
                    maxRetur(response)

                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                },
                complete: function() {
                    loading.style.display = 'none';
                    returbtn.classList.remove('d-none')

                }
            });
        });
    });
    $(document).on('click', '.btnRetur', function() {
        const area = document.getElementById('area').value;
        const model = document.getElementById('no_model').value;
        $('#modalPengajuanRetur').find('input[name="id"]').val(area);
        $('#modalPengajuanRetur').find('input[name="model"]').val(model);

        $.ajax({
            url: "http://172.23.39.114/MaterialSystem/public/api/cekBahanBaku/" + model,
            type: "GET",
            data: {
                model: model
            },
            dataType: "json",
            success: function(response) {
                updateItemSelect(response, model)
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            },
            complete: function() {}
        });

        $('#modalPengajuanRetur').modal('show'); // Show the modal
    });
    // Sisanya (seperti event untuk search, build table, dan add more item) tetap sama


    function buildTableRows(data, aggregateKeys) {
        let rows = '';
        let index = 0;
        for (const key in data) {
            if (aggregateKeys.includes(key)) continue;
            const item = data[key];
            const kgsOutVal = parseFloat(item.kgs_out);
            const validKgsOut = isNaN(kgsOutVal) ? 0 : kgsOutVal;

            const pphVal = parseFloat(item.pph);
            const estimasiRaw = validKgsOut - (isNaN(pphVal) ? 0 : pphVal);
            const estimasi = isNaN(estimasiRaw) ? 0 : estimasiRaw.toFixed(2);

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
                    
                </div>
            </div>

            <!-- Modal Pengajuan Retur -->
            
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
            $('#dataTableRetur').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        });

    }

    function updateItemSelect(data, model) {
        const option = document.getElementById('itemSelect');
        option.innerHTML = ''; // Kosongkan dulu

        // Tambahkan opsi default jika perlu
        option.innerHTML = '<option value="">-- Pilih Item --</option>';

        for (const key of data) {
            const opt = document.createElement('option');
            opt.value = key.id_material;
            opt.dataset.item = key.item_type;
            opt.dataset.kodeWarna = key.kode_warna;
            opt.dataset.warna = key.color;
            opt.dataset.model = model;
            opt.textContent = `${key.item_type} | ${key.kode_warna} | ${key.color}`;
            option.appendChild(opt);
        }
        $.ajax({
            url: "http://172.23.39.114/MaterialSystem/public/api/getPengirimanArea?noModel=" + model,
            type: "GET",
            data: {
                model: model
            },
            dataType: "json",
            success: function(response) {
                updateLotRetur(response)
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            },
            complete: function() {

                loading.style.display = 'none';
            }
        });
    }

    function updateLotRetur(data) {
        const option = document.getElementById('lotRetur');
        option.innerHTML = ''; // Kosongkan dulu

        // Tambahkan opsi default jika perlu
        option.innerHTML = '<option value="">-- Pilih Item --</option>';

        for (const key of data) {
            const opt = document.createElement('option');
            opt.value = key.lot_kirim;
            opt.textContent = `${key.lot_kirim}`;
            option.appendChild(opt);
        }

    }

    function maxRetur(data) {
        $(document).on('change', '#itemSelect', function() {
            const selectedOption = $(this).find(':selected');

            const item = selectedOption.data('item');
            const kodeWarna = selectedOption.data('kodeWarna'); // Hati-hati: `data('kodeWarna')` vs `data('kodewarna')`
            const key = `${item}-${kodeWarna}`;

            const textMax = document.getElementById('textMax');
            const kgsInput = document.getElementById('kgs');

            const selected = data[key];
            if (selected) {
                const pph = parseFloat(selected.pph) || 0;
                const kirim = parseFloat(selected.kgs_out) || 0;

                const retur = kirim - pph;

                textMax.innerHTML = `Max Retur: ${retur.toFixed(2)} kg`;


                console.log('Total retur:', retur);
            } else {
                textMax.innerHTML = 'Max Retur: -';
                kgsInput.removeAttribute('max');
                delete kgsInput.dataset.maxRetur;
            }
        });
        $(document).on('input', '#kgs', function() {
            const max = parseFloat(this.dataset.maxRetur || 0);
            const value = parseFloat(this.value || 0);

            if (value > max && max > 0) {
                textmax = max.toFixed(2)
                alert(`Maksimal retur hanya ${textmax} kg`);
                this.value = textmax;
            }
        });
    }

    document.getElementById('formPengajuanRetur').addEventListener('submit', function(e) {
        e.preventDefault(); // Stop form reload

        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitRetur');
        const model = document.getElementById('modelText').value
        const area = document.getElementById('areaText').value
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

        fetch('<?= site_url($role . "/pengajuanRetur/" . $area) ?>', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Ajukan Retur';

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: res.message || 'Retur berhasil dikirim.',
                    });

                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalPengajuanRetur'));
                    listRetur(model, area)
                    modal.hide();
                    form.reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message || 'Terjadi kesalahan saat mengirim.',
                    });
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Ajukan Retur';
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan jaringan!',
                });
                console.error(err);
            });
    });
</script>
<?php $this->endSection(); ?>