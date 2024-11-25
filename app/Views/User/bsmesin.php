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
                                            <option value=""></option>
                                            <?php foreach ($karyawan as $kar): ?>
                                                <option value="<?= $kar['id_karyawan']; ?>"><?= $kar['nama_karyawan']; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="kode_kartu" class="form-control-label">Kode Kartu</label>
                                        <input class="form-control" type="text" hidden id="nama_kar" name="namakar" value="<?= $kar['nama_karyawan']; ?>">
                                        <input class="form-control" type="text" id="kode_kartu" name="kode_kartu">
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
                                            <label for="inisial_0" class="form-control-label">In</label>
                                            <input class="form-control" type="text" id="inisial_0" name="inisial[]" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="no_model_0" class="form-control-label">PDK</label>
                                            <input class="form-control" type="text" id="no_model_0" name="no_model[]" required>
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



</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
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
        <div class="col-lg-1 col-sm-12">
            <div class="form-group">
                <label for="inisial_${rowIndex}" class="form-control-label">In</label>
                <input class="form-control" type="text" id="inisial_${rowIndex}" name="inisial[]" required>
            </div>
        </div>
        <div class="col-lg-2 col-sm-12">
            <div class="form-group">
                <label for="no_model_${rowIndex}" class="form-control-label">PDK</label>
                <input class="form-control" type="text" id="no_model_${rowIndex}" name="no_model[]" required>
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
</script>

<?php $this->endSection(); ?>