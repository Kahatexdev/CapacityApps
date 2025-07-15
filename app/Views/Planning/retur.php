<?php $this->extend($role . '/layout'); ?>
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
                                    <?= $title ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-center text-end gap-2">
                            <select name="area" id="area" class="form-control">
                                <option value="">Pilih Area</option>
                                <?php foreach ($areas as $ar) : ?>
                                    <option value="<?= $ar ?>"><?= $ar ?></option>
                                <?php endforeach ?>
                            </select>
                            <input type="text" class="form-control" id="no_model" value="" placeholder="No Model">
                            <button id="searchModel" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
                            <!-- <button type="button" class="btn btn-warning ms-2 btnRetur d-none" id="btn-retur">
                                <i class="fas fa-paper-plane"></i> Pengajuan Retur</button> -->
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
                <h3 class="model-title mb-0">List Returan</h3>
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
            url: "http://172.23.44.14/MaterialSystem/public/api/getPengirimanArea?noModel=" + model,
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