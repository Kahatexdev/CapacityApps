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
                        <h5>Input Penggunaan Jarum</h5>
                    </div>
                    <div class="card-body">
                        <form id="penggunaanJarum" action="<?= base_url($role . '/savePenggunaanJarum'); ?>" method="post">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
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
                                <div class="col-lg-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="kode_kartu" class="form-control-label">Kode Kartu</label>
                                        <input type="text" class="form-control" id="nama_kar" name="namakar" value="" hidden>
                                        <input type="text" class="form-control" id="id_kar" name="idkary" value="" hidden>
                                        <input type="text" class="form-control" id="kode_kartu" name="kode_kartu" placeholder="Masukkan Kode Kartu">
                                    </div>
                                </div>

                                <div class="col-lg-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="tgl_prod" class="form-control-label">Tanggal</label>
                                        <input class="form-control" type="date" id="tgl_prod" name="tgl" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="pcs" class="form-control-label">Qty (pcs)</label>
                                        <input class="form-control" type="number" id="pcs" name="pcs" required>
                                    </div>
                                </div>

                            </div>



                            <div class="row mt-3">
                                <button type="submit" class="btn btn-info" id="submitForm">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <h4>
                            Penggunaan Jarum Perbulan <?= $area ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <?php foreach ($month as $bln): ?>
                <div class="col-lg-3 mt-2">
                    <a href="<?= base_url($role . '/penggunaanPerbulan/' . $area . '/' . $bln) ?>">
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



</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    // AJAX form submission



    function getInfo() {
        let id = document.getElementById('nama').value;

        if (id) {
            // Panggil API menggunakan fetch
            fetch(HrislUrl + `karyawan/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json(); // Parsing hasil ke JSON
                })
                .then(data => {
                    // Update input field dengan data dari API
                    document.getElementById('nama_kar').value = data.nama_karyawan || '';
                    document.getElementById('id_kar').value = id || '';
                    document.getElementById('kode_kartu').value = data.kode_kartu || '';
                })
                .catch(error => {
                    console.error('Terjadi kesalahan:', error);
                    alert('Gagal mengambil data dari server.');
                });
        } else {
            // Jika id kosong, kosongkan input
            document.getElementById('kode_kartu').value = '';
            document.getElementById('id_kar').value = '';
            document.getElementById('nama_kar').value = '';
        }
    }
</script>

<?php $this->endSection(); ?>