<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
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

    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card pb-0">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Input BS Mesin</h5>
                    </div>
                    <div class="card-body">
                        <form id="bsMesinForm">
                            <div class="row">
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="nama" class="form-control-label">Nama</label>
                                        <select name="nama" id="nama" class="form-control" onchange="getInfo()">
                                            <option value="" selected>Pilih Nama</option>
                                            <?php if (!empty($karyawan) && is_array($karyawan)): ?>
                                                <?php foreach ($karyawan as $kar): ?>
                                                    <option value="<?= htmlspecialchars($kar['id_karyawan']); ?>">
                                                        <?= htmlspecialchars($kar['nama_karyawan']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">No employees found</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="kode_kartu" class="form-control-label">Kode Kartu</label>
                                        <input type="hidden" class="form-control" id="nama_kar" name="namakar" value="">
                                        <input type="text" class="form-control" id="kode_kartu" name="kode_kartu" placeholder="Masukkan Kode Kartu">
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="shift" class="form-control-label">Shift</label>
                                        <input class="form-control" type="text" id="shift" name="shift">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="tgl_prod" class="form-control-label">Tanggal Produksi</label>
                                        <input class="form-control" type="date" id="tgl_prod" name="tgl_prod" required>
                                    </div>
                                </div>
                            </div>

                            <div id="dynamicRowContainer">
                                <div class="row" id="row_0">
                                    <div class="col-lg-1 col-sm-12">
                                        <div class="form-group">
                                            <label for="no_mesin_0" class="form-control-label">No MC</label>
                                            <input class="form-control" type="text" id="no_mesin_0" name="no_mesin[]" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="no_model_0" class="form-control-label">PDK</label>
                                            <!-- <input class="form-control" type="text" id="no_model_0" name="no_model[]" required> -->
                                            <select name="no_model[]" id="no_model_0" class="select2 form-select" onchange="getIn(0)" required>
                                                <option value="" selected>Pilih No Model</option>
                                                <?php if (!empty($pdk) && is_array($pdk)): ?>
                                                    <?php foreach ($pdk as $pd): ?>
                                                        <option value="<?= htmlspecialchars($pd['mastermodel']); ?>">
                                                            <?= htmlspecialchars($pd['mastermodel']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">No employees found</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="inisial_0" class="form-control-label">In</label>
                                            <input type="hidden" class="form-control" name="inisialName[]" id="inisialName_0">
                                            <select class="form-control" id="inisial_0" name="inisial[]" onchange="getSize(0)" required>
                                                <option value="" selected>Pilih inisial</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="size_0" class="form-control-label">Size</label>
                                            <input class="form-control" type="text" id="size_0" name="size[]" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="gram_0" class="form-control-label">Qty (Gram)</label>
                                            <input class="form-control" type="number" id="gram_0" name="gram[]" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="pcs_0" class="form-control-label">Qty (pcs)</label>
                                            <input class="form-control" type="number" id="pcs_0" name="pcs[]" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-sm-12">
                                        <div class="form-group">
                                            <label for="" class="form-control-label"></label>
                                            <button type="button" class="btn btn-info" onclick="addRow()">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <button type="button" class="btn btn-info" id="submitForm">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class=" d-flex justify-content-between">

                        <h4>
                            BS Mesin Perbulan <?= $areas ?>

                        </h4>
                        <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#summaryBS">
                            <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Summary BS MC
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <?php foreach ($month as $bln): ?>
            <div class="col-lg-3 mt-2">
                <a href="<?= base_url($role . '/bsMesinPerbulan/' . $areas . '/' . $bln) ?>">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-12">
                                <h5 class="card-title">

                                    <i class="fas fa-calendar-alt text-lg opacity-10" aria-hidden="true"></i> <?= $bln ?>
                                </h5>

                            </div>

                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>

</div>
<div class="modal fade" id="summaryBS" tabindex="-1" role="dialog" aria-labelledby="summaryBS" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Summary BS MC</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?= base_url($role . '/exportSummaryBs'); ?>" method="POST">
                <div class="modal-body align-items-center">
                    <div class="form-group">
                        <label for="buyer" class="col-form-label">Buyer</label>
                        <select class="form-control" id="buyer" name="buyer">
                            <option></option>
                            <?php foreach ($buyer as $buy) : ?>
                                <option><?= $buy['kd_buyer_order'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="area" class="col-form-label">Area</label>
                        <select class="form-control" id="area" name="area">
                            <option></option>
                            <?php foreach ($area as $ar) : ?>
                                <option><?= $ar ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jarum" class="col-form-label">Jarum</label>
                        <select class="form-control" id="jarum" name="jarum">
                            <option></option>
                            <option value="13">13</option>
                            <option value="84">84</option>
                            <option value="92">92</option>
                            <option value="96">96</option>
                            <option value="106">106</option>
                            <option value="108">108</option>
                            <option value="116">116</option>
                            <option value="120">120</option>
                            <option value="124">124</option>
                            <option value="126">126</option>
                            <option value="144">144</option>
                            <option value="168">168</option>
                            <option value="240">240</option>
                            <option value="POM-POM">POM-POM</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pdk" class="col-form-label">No Model</label>
                        <input type="text" class="form-control" name="pdk">
                    </div>
                    <div class="form-group">
                        <label for="awal" class="col-form-label">Dari</label>
                        <input type="date" class="form-control" name="awal">
                    </div>
                    <div class="form-group">
                        <label for="akhir" class="col-form-label">Sampai</label>
                        <input type="date" class="form-control" name="akhir">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-info">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<!-- jQuery (Diperlukan oleh Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script type="text/javascript">
    let rowIndex = 1;

    // Add new row dynamically
    function addRow() {
        const container = document.getElementById('dynamicRowContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row';
        newRow.id = `row_${rowIndex}`;
        newRow.innerHTML = `
        <div class="col-lg-1 col-sm-12">
            <div class="form-group">
                <label for="no_mesin_${rowIndex}" class="form-control-label">No MC</label>
                <input class="form-control" type="text" id="no_mesin_${rowIndex}" name="no_mesin[]" required>
            </div>
        </div>
        <div class="col-lg-2 col-sm-12">
            <div class="form-group">
                <label for="no_model_${rowIndex}" class="form-control-label">PDK</label>
                <select name="no_model[]" id="no_model_${rowIndex}" class="form-control" onchange="getIn(${rowIndex})" required>
                    <option value="" selected>Pilih No Model</option>
                    <?php if (!empty($pdk) && is_array($pdk)): ?>
                        <?php foreach ($pdk as $pd): ?>
                            <option value="<?= $pd['mastermodel']; ?>">
                                <?= $pd['mastermodel']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No employees found</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="col-lg-1 col-sm-12">
            <div class="form-group">
                <label for="inisial_${rowIndex}" class="form-control-label">In</label>
                <input type="hidden" class="form-control" name="inisialName[]" id="inisialName_${rowIndex}">
                <select class="form-select" id="inisial_${rowIndex}" name="inisial[]" onchange="getSize(${rowIndex})" required>
                    <option value="">Pilih inisial</option>
                </select>
            </div>
        </div>
        <div class="col-lg-2 col-sm-12">
            <div class="form-group">
                <label for="size_${rowIndex}" class="form-control-label">Size</label>
                <input class="form-control" type="text" id="size_${rowIndex}" name="size[]" required>
            </div>
        </div>
        <div class="col-lg-2 col-sm-12">
            <div class="form-group">
                <label for="gram_${rowIndex}" class="form-control-label">Qty (Gram)</label>
                <input class="form-control" type="number" id="gram_${rowIndex}" name="gram[]" required>
            </div>
        </div>
        <div class="col-lg-2 col-sm-12">
            <div class="form-group">
                <label for="pcs_${rowIndex}" class="form-control-label">Qty (pcs)</label>
                <input class="form-control" type="number" id="pcs_${rowIndex}" name="pcs[]" required>
            </div>
        </div>
        <div class="col-lg-2 col-sm-12">
            <div class="form-group">
                <button type="button" class="btn btn-info" onclick="addRow()">+</button>
                <button type="button" class="btn btn-danger" onclick="removeRow(${rowIndex})">-</button>
            </div>
        </div>`;
        container.appendChild(newRow);
        rowIndex++;
    }

    // Remove a row
    function removeRow(index) {
        const row = document.getElementById(`row_${index}`);
        if (row) row.remove();
    }

    // AJAX form submission
    document.getElementById('submitForm').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('bsMesinForm'));

        fetch('<?= base_url("user/saveBsMesin") ?>', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Data berhasil disimpan.',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Reload setelah SweetAlert selesai
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal menyimpan data: ' + data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengirim data.',
                });
                console.error('Error:', error);
            });
    });

    function getInfo() {
        let id = document.getElementById('nama').value;

        if (id) {
            // Panggil API menggunakan fetch
            fetch(`http://172.23.44.14/SkillMapping/public/api/karyawan/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json(); // Parsing hasil ke JSON
                })
                .then(data => {
                    // Update input field dengan data dari API
                    document.getElementById('nama_kar').value = data.nama_karyawan || '';
                    document.getElementById('kode_kartu').value = data.kode_kartu || '';
                    document.getElementById('shift').value = data.shift || '';
                })
                .catch(error => {
                    console.error('Terjadi kesalahan:', error);
                    alert('Gagal mengambil data dari server.');
                });
        } else {
            // Jika id kosong, kosongkan input
            document.getElementById('kode_kartu').value = '';
            document.getElementById('shift').value = '';
        }
    }

    function getIn(rowIndex) {
        let id = document.getElementById(`no_model_${rowIndex}`).value;
        const inisialSelect = document.getElementById(`inisial_${rowIndex}`);
        inisialSelect.innerHTML = '<option value="">Pilih Inisial</option>';
        // let inisial = document.getElementById('inisialName');
        if (id) {
            const url = `<?= base_url('/user/userController/getInisial/') ?>${id}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil data inisial');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.inisial && typeof data.inisial === 'object') {
                        Object.values(data.inisial).forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.idapsperstyle;
                            option.text = item.inisial;
                            // inisial.value = item.inisial;
                            inisialSelect.appendChild(option);
                        });
                    } else {
                        alert('Data inisial tidak ditemukan atau format tidak sesuai.');
                    }
                })
                .catch(error => {
                    alert('Gagal mengambil data dari server.');
                });
        } else {
            inisialSelect.innerHTML = '<option value="">Pilih Inisial</option>';
        }
    }

    function getSize(rowIndex) {
        let idaps = document.getElementById(`inisial_${rowIndex}`).value;
        const sizeInput = document.getElementById(`size_${rowIndex}`);
        const inisial = document.getElementById(`inisialName_${rowIndex}`);

        if (idaps) {
            const url = `<?= base_url('/user/userController/getSize/') ?>${idaps}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil data size');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.size) {
                        sizeInput.value = data.size;
                        inisial.value = data.inisial;

                    } else {
                        sizeInput.value = '';
                        alert('Data size tidak ditemukan.');
                    }
                })
                .catch(error => {
                    sizeInput.value = '';
                    alert('Gagal mengambil data dari server.');
                });
        } else {
            sizeInput.value = '';
        }
    }

    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5', // Gunakan tema Bootstrap 5
            width: '100%',
            placeholder: "Pilih No Model",
            allowClear: true
        });
    });
</script>

<?php $this->endSection(); ?>