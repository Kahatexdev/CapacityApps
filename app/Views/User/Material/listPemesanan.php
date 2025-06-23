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
                    <div class="d-flex justify-content-between">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                List Pemesanan Bahan Baku <?= $area ?>
                            </h5>
                        </div>
                        <div>
                            <button class="btn btn-info requestTimeButton" data-bs-toggle="modal" data-bs-target="#requestTimeModal">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="15">
                                    <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com -->
                                    <path d="M432 304c0 114.9-93.1 208-208 208S16 418.9 16 304c0-104 76.3-190.2 176-205.5V64h-28c-6.6 0-12-5.4-12-12V12c0-6.6 5.4-12 12-12h120c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-28v34.5c37.5 5.8 71.7 21.6 99.7 44.6l27.5-27.5c4.7-4.7 12.3-4.7 17 0l28.3 28.3c4.7 4.7 4.7 12.3 0 17l-29.4 29.4-.6 .6C419.7 223.3 432 262.2 432 304z" fill="#ffffff" />

                                    <!-- Tanda plus putih besar dan sedikit turun -->
                                    <line x1="224" y1="244" x2="224" y2="364" stroke="#17c1e8" stroke-width="50" stroke-linecap="round" />
                                    <line x1="164" y1="304" x2="284" y2="304" stroke="#17c1e8" stroke-width="50" stroke-linecap="round" />
                                </svg> Waktu
                            </button>
                            <!-- Modal Additional Time -->
                            <div class="modal fade" id="requestTimeModal" tabindex="-1" aria-labelledby="requestTimeModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="requestTimeModalLabel">Pilih Jenis Benang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Konten modal, misalnya formulir -->
                                            <form action="<?= base_url($role . '/requestAdditionalTime') ?>" method="post">
                                                <input type="hidden" name="area" value="<?= $area ?>">
                                                <input type="hidden" name="tomorrow" id="tomorrow" value="<?= $tomorrow ?>">
                                                <input type="hidden" name="twoDays" id="twoDays" value="<?= $twoDays ?>">
                                                <input type="hidden" name="threeDays" id="threeDays" value="<?= $threeDays ?>">
                                                <input type="hidden" name="day" id="day" value="<?= $day ?>">
                                                <div class="mb-3">
                                                    <select name="jenis" id="jenisBenang" class="form-select" required>
                                                        <option value="">Pilih Jenis Benang</option>
                                                        <option value="BENANG">BENANG</option>
                                                        <option value="NYLON">NYLON</option>
                                                        <option value="KARET">KARET</option>
                                                        <option value="SPANDEX">SPANDEX</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3" id="tglPakai">

                                                </div>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-info">Pilih</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Modal Additional Time -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="display compact " style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Pkai</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Item Type</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode Warna</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Color</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kg Kebtuhan</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jl Mc</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kg Pesan</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Cns Pesan</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Lot</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">PO(+)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Terima</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Retur</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Jatah</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Status Jatah</th>
                                    <th class="text-uppercase text-dark text-xxs text-center font-weight-bolder opacity-7 ps-2">Edit</th>
                                    <th class="text-uppercase text-dark text-xxs text-center font-weight-bolder opacity-7 ps-2">Send</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($dataList as $key => $id) {
                                    $ttl_kg_pesan = number_format($id['qty_pesan'] - $id['qty_sisa'], 2);
                                    $ttl_cns_pesan = $id['cns_pesan'] - $id['cns_sisa'];
                                ?>
                                    <tr>
                                        <td class="text-xs text-start"><?= $no++; ?></td>
                                        <td class="text-xs text-start"><?= $id['tgl_pakai']; ?></td>
                                        <td class="text-xs text-start"><?= $id['no_model']; ?></td>
                                        <td class="text-xs text-start"><?= $id['item_type']; ?></td>
                                        <td class="text-xs text-start"><?= $id['kode_warna']; ?></td>
                                        <td class="text-xs text-start"><?= $id['color']; ?></td>
                                        <td class="text-xs text-start"><?= number_format($id['ttl_kebutuhan_bb'], 2); ?></td>
                                        <td class="text-xs text-start"><?= $id['jl_mc']; ?></td>
                                        <td class="text-xs text-start"><?= $ttl_kg_pesan; ?></td>
                                        <td class="text-xs text-start"><?= $ttl_cns_pesan; ?></td>
                                        <td class="text-xs text-start"><?= $id['lot']; ?></td>
                                        <td class="text-xs text-start"><?= $id['keterangan']; ?></td>
                                        <td class="text-xs text-start">
                                            <?php if ($id['po_tambahan'] == 1): ?>
                                                <span class="text-success fw-bold">✅</span>
                                            <?php else: ?>
                                                <!-- Biarkan kosong -->
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-xs text-start"><?= number_format($id['ttl_pengiriman'], 2); ?></td>
                                        <td class="text-xs text-start"></td>
                                        <td class="text-xs text-start" style="<?= $id['sisa_jatah'] < 0 ? 'color: red;' : ''; ?>"><?= number_format($id['sisa_jatah'], 2); ?></td>
                                        <td class="text-xs text-start" style="<?= $id['sisa_jatah'] < 0 ? 'color: red;' : ''; ?>">
                                            <?php if ($id['sisa_jatah'] > 0) {
                                                if ($ttl_kg_pesan >= $id['sisa_jatah']) { ?>
                                                    <span style="color: red;">Pemesanan Melebihi Jatah</span>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <span style="color: red;">Habis Jatah</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-xs text-start">
                                            <button type="button" class="btn btn-warning update-btn" data-toggle="modal" data-target="#updateListModal" data-area="<?= $area; ?>" data-tgl="<?= $id['tgl_pakai']; ?>" data-model="<?= $id['no_model']; ?>" data-item="<?= $id['item_type']; ?>" data-kode="<?= $id['kode_warna']; ?>" data-color="<?= $id['color']; ?>" data-po-tambahan="<?= $id['po_tambahan']; ?>">
                                                <i class="fa fa-edit fa-lg"></i>
                                            </button>
                                        </td>
                                        <td class="text-xs">
                                            <?php
                                            $show = "d-none";
                                            $batasWaktu = '08:30:00';

                                            if ($id['sisa_jatah'] > 0) {
                                                if ($ttl_kg_pesan <= $id['sisa_jatah']) {
                                                    // Aturan berdasarkan hari dan jenis produk:
                                                    $rules = [
                                                        'Thursday' => [
                                                            'BENANG'  => [$tomorrow => '08:30:00'],
                                                            'NYLON'   => [$tomorrow => '08:30:00'],
                                                            'SPANDEX' => [$twoDays  => '08:30:00', $threeDays => '09:00:00'],
                                                            'KARET'   => [$twoDays  => '08:30:00', $threeDays => '09:00:00']
                                                        ],
                                                        'Friday' => [
                                                            'BENANG'  => [$tomorrow => '08:30:00', $twoDays => '09:00:00'],
                                                            'NYLON'   => [$tomorrow => '08:30:00'],
                                                            'SPANDEX' => [$threeDays => '08:30:00'],
                                                            'KARET'   => [$threeDays => '08:30:00']
                                                        ],
                                                        'Saturday' => [
                                                            'BENANG'  => [$twoDays  => '08:30:00'],
                                                            'NYLON'   => [$tomorrow => '08:30:00', $twoDays  => '09:00:00'],
                                                            'SPANDEX' => [$threeDays => '08:30:00'],
                                                            'KARET'   => [$threeDays => '08:30:00']
                                                        ],
                                                        'default' => [
                                                            'BENANG'  => [$tomorrow => '08:30:00'],
                                                            'NYLON'   => [$tomorrow => '08:30:00'],
                                                            'SPANDEX' => [$twoDays  => '08:30:00'],
                                                            'KARET'   => [$twoDays  => '08:30:00']
                                                        ]
                                                    ];

                                                    $currentRules = isset($rules[$day]) ? $rules[$day] : $rules['default'];

                                                    if (isset($currentRules[$id['jenis']])) {
                                                        foreach ($currentRules[$id['jenis']] as $tgl => $waktu) {
                                                            if ($id['tgl_pakai'] == $tgl) {
                                                                $show = "";

                                                                if ($id['status_kirim'] === 'request accept') {
                                                                    $batasWaktu = $id['additional_time'];
                                                                } else {
                                                                    $batasWaktu = $waktu;
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    }
                                            ?>
                                                    <button type="button" id="sendBtn" class="btn btn-info text-xs <?= $show ?> send-btn" data-toggle="modal"
                                                        data-area="<?= $area; ?>"
                                                        data-tgl="<?= $id['tgl_pakai']; ?>"
                                                        data-model="<?= $id['no_model']; ?>"
                                                        data-item="<?= $id['item_type']; ?>"
                                                        data-kode="<?= $id['kode_warna']; ?>"
                                                        data-color="<?= $id['color']; ?>"
                                                        data-waktu="<?= $batasWaktu; ?>">
                                                        <i class="fa fa-paper-plane fa-lg"></i>
                                                    </button>
                                            <?php
                                                }
                                            }  ?>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal update list pemesanan -->
<div class="modal fade" id="updateListModal" tabindex="-1" role="dialog" aria-labelledby="importModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder" id="exampleModalLabel">Update List Pemesanan</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="updatePemesanan">
                <div class="modal-body align-items-center">
                    <div class="col-lg-12">

                        <div class="row">
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Area</label>
                                <input type="text" class="form-control" name="area" readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Tgl Pakai</label>
                                <input type="text" class="form-control" name="tgl_pakai" readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">No Model</label>
                                <input type="text" class="form-control" name="no_model" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <label for="recipient-name" class="col-form-label text-center">Item Type</label>
                                <input type="text" class="form-control" name="item_type" readonly>
                            </div>
                            <div class="col-lg-6">
                                <label for="recipient-name" class="col-form-label text-center">Kode Warna</label>
                                <input type="text" class="form-control" name="kode_warna" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Color</label>
                                <input type="text" class="form-control" name="color" readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Lot</label>
                                <input type="text" class="form-control" name="lot">
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Keterangan</label>
                                <textarea class="form-control" name="keterangan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer align-items-center">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">Style Size</label>
                            </div>
                            <div class="col-lg-1">
                                <label for="recipient-name" class="col-form-label text-center">Jl Mc</label>
                            </div>
                            <div class="col-lg-2">
                                <label for="recipient-name" class="col-form-label text-center">Qty Cones</label>
                            </div>
                            <div class="col-lg-2">
                                <label for="recipient-name" class="col-form-label text-center">Ttl Qty Cones</label>
                            </div>
                            <div class="col-lg-2">
                                <label for="recipient-name" class="col-form-label text-center">Berat Cones</label>
                            </div>
                            <div class="col-lg-2">
                                <label for="recipient-name" class="col-form-label text-center"> Ttl Berat Cones</label>
                            </div>
                        </div>
                        <div id="dataPerstyle">
                            <!-- data perstyle muncul di sini -->
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-info remove-row w-100">UPDATE</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal update list pemesanan end -->
<script src=" <?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    // SweetAlert untuk menampilkan pesan sukses/gagal
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil flashdata dari server dan pastikan nilainya berupa string
        const successMessage = "<?= htmlspecialchars(is_string(session()->getFlashdata('success')) ? session()->getFlashdata('success') : '') ?>";
        const errorMessage = "<?= htmlspecialchars(is_string(session()->getFlashdata('error')) ? session()->getFlashdata('error') : '') ?>";

        // Tampilkan SweetAlert jika ada pesan sukses
        if (successMessage && successMessage.trim() !== "") {
            Swal.fire({
                title: "Berhasil!",
                text: successMessage,
                icon: "success",
                confirmButtonText: "OK"
            });
        }

        // Tampilkan SweetAlert jika ada pesan gagal
        if (errorMessage && errorMessage.trim() !== "") {
            Swal.fire({
                title: "Gagal!",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "OK"
            });
        }
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        // const day = '<?= $day ?>'
        // if (day.toLowerCase() === 'sunday') {
        //     $('.requestTimeButton').hide();
        // }

        // GET TGL PAKAI ADDITIONAL TIME
        $('#jenisBenang').on('change', function() {
            var jenis = $(this).val(); // Dapatkan nilai pilihan
            var tomorrow = $('#tomorrow').val();
            var twoDays = $('#twoDays').val();
            var threeDays = $('#threeDays').val();
            var day = $('#day').val();
            if (jenis) {
                // Lakukan AJAX request
                $.ajax({
                    url: "<?= base_url($role . '/requestAdditionalTime/getTanggalPakai') ?>", // Endpoint untuk data
                    type: "POST",
                    data: {
                        jenis: jenis,
                        tomorrow: tomorrow,
                        twoDays: twoDays,
                        threeDays: threeDays,
                        day: day

                    },
                    success: function(response) {
                        // Tampilkan response di elemen #tglPakai
                        $('#tglPakai').html(response);
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat memuat data.');
                    }
                });
            } else {
                // Kosongkan elemen jika tidak ada pilihan
                $('#tglPakai').html('');
            }
        });
        // END GET TGL PAKAI ADDITIONAL TIME

        // Mendapatkan waktu saat ini
        // Iterasi melalui semua tombol dengan kelas .send-btn
        $('.send-btn').each(function() {
            let button = $(this); // Tombol saat ini dalam iterasi
            let currentTime = new Date();

            // Mendapatkan batas waktu dari atribut data-waktu
            let batasWaktuStr = button.data('waktu'); // Format: HH:mm:ss
            if (!batasWaktuStr) {
                console.warn('Batas waktu tidak ditemukan untuk tombol:', button);
                return; // Lewati tombol jika tidak ada data-waktu
            }

            let [hours, minutes, seconds] = batasWaktuStr.split(':').map(Number);
            let batasWaktu = new Date(
                currentTime.getFullYear(),
                currentTime.getMonth(),
                currentTime.getDate(),
                hours,
                minutes,
                seconds
            );

            // Hitung waktu tersisa hingga batas waktu dalam milidetik
            let waktuTersisa = batasWaktu - currentTime;

            // Jika waktu tersisa lebih dari 0, atur timer untuk menyembunyikan tombol
            if (waktuTersisa > 0) {
                setTimeout(function() {
                    button.addClass('d-none'); // Sembunyikan tombol
                    console.log('Tombol disembunyikan: Waktu sudah melewati batas untuk', button);
                }, waktuTersisa);
            } else {
                // Jika waktu sudah terlewati, sembunyikan tombol langsung
                button.addClass('d-none');
                console.log('Tombol sudah disembunyikan: Waktu sudah lewat saat halaman dimuat untuk', button);
            }
        });

        // FILTER TABLE
        $('#example').DataTable({
            "order": [
                [0, 'asc'] // Kolom pertama (indeks 0) diurutkan secara descending
            ]
        });
        // END FILTER TABLE

        // VIEW MODAL UPDATE PEMESANAN
        // Trigger import modal when import button is clicked
        $(document).on('click', '.update-btn', function() {
            var area = $(this).data('area');
            var tglPakai = $(this).data('tgl');
            var noModel = $(this).data('model');
            var itemType = $(this).data('item');
            var kode_warna = $(this).data('kode');
            var color = $(this).data('color');

            // Kirim data ke server untuk pencarian
            $.ajax({
                url: 'http://172.23.44.14/MaterialSystem/public/api/getUpdateListPemesanan', // Ganti dengan URL endpoint
                method: 'POST',
                data: {
                    area: area,
                    tgl_pakai: tglPakai,
                    no_model: noModel,
                    item_type: itemType,
                    kode_warna: kode_warna,
                    color: color
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response); // Debug response dari server
                    if (response.status === 'success') {
                        // Ambil semua nilai lot dari response.data
                        let lotValues = response.data.map(item => item.lot);
                        let uniqueLotValues = [...new Set(lotValues)]; // Ambil nilai yang unik menggunakan Set
                        let lotDisplay = uniqueLotValues[0]; // ambil data lot 
                        let keteranganValues = response.data.map(item => item.keterangan);
                        let uniqueKeteranganValues = [...new Set(keteranganValues)]; // Ambil nilai yang unik menggunakan Set
                        let keteranganDisplay = uniqueKeteranganValues[0]; // ambil data lot 

                        // Isi data ke dalam modal
                        $('#updateListModal').find('input[name="area"]').val(area);
                        $('#updateListModal').find('input[name="tgl_pakai"]').val(tglPakai);
                        $('#updateListModal').find('input[name="no_model"]').val(noModel);
                        $('#updateListModal').find('input[name="item_type"]').val(itemType);
                        $('#updateListModal').find('input[name="kode_warna"]').val(kode_warna);
                        $('#updateListModal').find('input[name="color"]').val(color);
                        $('#updateListModal').find('input[name="lot"]').val(lotDisplay);
                        $('#updateListModal').find('textarea[name="keterangan"]').val(keteranganDisplay);

                        // data perstyle
                        var dataPerstyle = '';
                        let ttl_jl_mc = 0;
                        let cns_pesan = 0;
                        let kg_pesan = 0;
                        let totalRows = 0; // Menyimpan jumlah data
                        let sisaKg = 0; // Total jalan_mc
                        let sisaCns = 0; // Total jalan_mc

                        response.data.forEach(function(item, index) {
                            const jalanMc = parseFloat(item.jl_mc) || 0;
                            const totalCones = jalanMc * item.qty_cns;
                            const totalBeratCones = totalCones * item.qty_berat_cns;

                            // Tambahkan nilai ke variabel akumulasi
                            ttl_jl_mc += parseFloat(jalanMc);
                            cns_pesan += parseFloat(totalCones);
                            kg_pesan += parseFloat(totalBeratCones);
                            sisaCns += parseFloat(item.sisa_cones_mc);
                            sisaKg += parseFloat(item.sisa_kgs_mc);

                            dataPerstyle += `
                            <div class="col-lg-12">
                                <div class="row mb-1">
                                <input type="hidden" class="form-control id_material" name="items[${index}][id_material]" value="${item.id_material}" readonly>
                                <input type="hidden" class="form-control id_pemesanan" name="items[${index}][id_pemesanan]" value="${item.id_pemesanan}" readonly>
                                <div class="col-lg-3">
                                    <input type="text" class="form-control style" name="items[${index}][style]" value="${item.style_size}" readonly>
                                </div>
                                <div class="col-lg-1">
                                    <input type="number" class="form-control jalan_mc" name="items[${index}][jalan_mc]" value="${item.jl_mc}">
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" class="form-control qty_cns" name="items[${index}][qty_cns]" value="${item.qty_cns}">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" class="form-control ttl_qty_cns" name="items[${index}][ttl_qty_cns]" value="${item.ttl_qty_cones}" readonly>
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" step="0.01" class="form-control qty_berat_cns" name="items[${index}][qty_berat_cns]" value="${parseFloat(item.qty_berat_cns).toFixed(2)}">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" class="form-control ttl_berat_cns" name="items[${index}][ttl_berat_cns]" value="${parseFloat(item.ttl_berat_cones).toFixed(2)}" readonly>
                                </div>
                            </div>
                            `;
                            totalRows++;
                        });

                        // Hitung rata-rata jalan_mc
                        // const sisaCnsMc = totalRows > 0 ? (sisaCns / totalRows) : 0;
                        // const sisaKgMc = totalRows > 0 ? (sisaKg / totalRows) : 0;
                        const ttl_cns_pesan = sisaCns > 0 ? cns_pesan - sisaCns : cns_pesan;
                        const ttl_kg_pesan = sisaKg > 0 ? kg_pesan - sisaKg : kg_pesan;
                        dataPerstyle += `
                            <div class="row mt-1">
                                <div class="col-lg-6">
                                    <label for="recipient-name" class="col-form-label text-center">Stock Area</label>
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" class="form-control sisa_cns" name="sisa_cns" value="${sisaCns}">
                                </div>
                                <div class="col-lg-2">
                                    
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" step="0.01" class="form-control sisa_kg" name="sisa_kg" value="${parseFloat(sisaKg).toFixed(2)}">
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-lg-3">
                                    <label for="recipient-name" class="col-form-label text-center">Total</label>
                                </div>
                                <div class="col-lg-1">
                                    <input type="text" class="form-control ttl_jl_mc" name="ttl_jl_mc" value="${ttl_jl_mc}" readonly>
                                </div>
                                <div class="col-lg-2">
                                </div>
                                <div class="col-lg-2">
                                <input type="text" class="form-control ttl_cns_pesan" name="ttl_cns_pesan" value="${ttl_cns_pesan}" readonly>
                                </div>
                                <div class="col-lg-2">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" class="form-control ttl_kg_pesan" name="ttl_kg_pesan" value="${parseFloat(ttl_kg_pesan).toFixed(2)}" readonly>
                                </div>
                            </div>
                        `;
                        $('#dataPerstyle').html(dataPerstyle); // Ganti isi elemen di modal

                        // Kalkulasi otomatis
                        function recalculateTotals() {
                            let totalJalanMc = 0;
                            let totalQtyCns = 0;
                            let totalBeratCns = 0;
                            let ttl_jl_mc = 0;
                            let ttl_cns_pesan = 0;
                            let ttl_kg_pesan = 0;

                            $('#dataPerstyle .row.mb-1').each(function() {
                                const jalan_mc = parseFloat($(this).find('.jalan_mc').val()) || 0;
                                const qty_cns = parseFloat($(this).find('.qty_cns').val()) || 0;
                                const qty_berat_cns = parseFloat($(this).find('.qty_berat_cns').val()) || 0;

                                const ttl_qty_cns = jalan_mc * qty_cns;
                                const ttl_berat_cns = ttl_qty_cns * qty_berat_cns;

                                totalJalanMc += jalan_mc;
                                totalQtyCns += ttl_qty_cns;
                                totalBeratCns += ttl_berat_cns;

                                $(this).find('.ttl_qty_cns').val(ttl_qty_cns);
                                $(this).find('.ttl_berat_cns').val(ttl_berat_cns.toFixed(2));
                            });

                            // Hitung nilai akhir dengan mengurangi sisa
                            const sisaCns = parseFloat($('.sisa_cns').val()) || 0;
                            const sisaKg = parseFloat($('.sisa_kg').val()) || 0;
                            if (sisaCns < 0 || sisaKg < 0) {
                                alert('Nilai tidak boleh negatif!');
                                $(this).val(0); // Reset nilai menjadi 0
                            }

                            // Update total dengan pengurangan sisa
                            ttl_jl_mc = totalJalanMc;
                            ttl_cns_pesan = totalQtyCns - sisaCns;
                            ttl_kg_pesan = totalBeratCns - sisaKg;

                            // Perbarui tampilan total
                            $('.ttl_jl_mc').val(ttl_jl_mc);
                            $('.ttl_cns_pesan').val(ttl_cns_pesan);
                            $('.ttl_kg_pesan').val(ttl_kg_pesan.toFixed(2));
                        }

                        $('#dataPerstyle').on('input', '.jalan_mc, .qty_cns, .qty_berat_cns, .sisa_cns, .sisa_kg', recalculateTotals); // Trigger recalculation on input change

                        $('#updateListModal').modal('show'); // Tampilkan modal
                    } else {
                        alert('Data tidak ditemukan');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Terjadi kesalahan saat mengambil data.');
                }
            });
        });
        // END VIEW MODAL UPDATE PEMESANAN

        // PROSES UPDATE PEMESANAN
        document.getElementById('updatePemesanan').addEventListener('submit', function(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const BASE_URL = "<?= base_url(); ?>";

            // Konversi FormData ke JSON tanpa "[]"
            const payload = {};
            formData.forEach((value, key) => {
                const cleanKey = key.replace(/\[\]$/, ""); // Hapus "[]"
                if (!payload[cleanKey]) payload[cleanKey] = [];
                payload[cleanKey].push(value);
            });
            console.log(payload);

            fetch('http://172.23.44.14/MaterialSystem/public/api/updateListPemesanan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                    // credentials: 'include', // Menyertakan cookie/session ID
                })
                .then(async (response) => {
                    const resData = await response.json();
                    // Ambil area dari payload untuk menentukan URL redirect
                    const area = payload.area?.[0] || ''; // Pastikan 'area' ada atau gunakan default
                    if (resData.status == "success") {
                        // Tampilkan SweetAlert setelah session berhasil dihapus
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: resData.message,
                            showConfirmButton: false,
                            timer: 1500, // 2 detik
                            timerProgressBar: true
                        }).then(() => {
                            // Redirect ke halaman yang diinginkan
                            window.location.href = `${BASE_URL}user/listPemesanan/${area}`; // Halaman tujuan setelah sukses
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: resData.message || 'Gagal menyimpan data',
                        }).then(() => {
                            // Redirect ke halaman yang diinginkan
                            window.location.href = `${BASE_URL}user/listPemesanan/${area}`; // Halaman tujuan setelah sukses
                        });
                        console.error('Response Data:', resData);
                    }
                })
                .catch((error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengirim data',
                    });
                    console.error('Fetch Error:', error);
                });

        });
        // END PROSES UPDATE PEMESANAN

        // PROSES KIRIM PEMESANAN
        document.addEventListener("click", function(e) {
            if (e.target.matches(".send-btn") || e.target.closest(".send-btn")) {
                const button = e.target.closest(".send-btn");
                if (!button) return; // Jika bukan tombol, keluar
                // Ambil waktu saat ini
                const now = new Date();
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();
                // Ambil batas waktu dari data attribute tombol
                const batasWaktu = button.getAttribute("data-waktu"); // Contoh: "08:30:00"

                // Ubah batasWaktu yang didapat dari PHP menjadi jam dan menit
                let [batasHour, batasMinute, batasSecond] = batasWaktu.split(':').map(Number);

                if (currentHour > batasHour || (currentHour === batasHour && currentMinute > batasMinute)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Melebihi batas waktu sesuai bahan baku! ',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    button
                    return;
                }

                // Ambil data dari tombol
                const data = {
                    area: button.getAttribute("data-area"),
                    tgl_pakai: button.getAttribute("data-tgl"),
                    no_model: button.getAttribute("data-model"),
                    item_type: button.getAttribute("data-item"),
                    kode_warna: button.getAttribute("data-kode"),
                    color: button.getAttribute("data-color"),
                    po_tambahan: button.getAttribute("data-po-tambahan"),
                    waktu: batasWaktu,
                };

                // Kirim data ke server menggunakan AJAX
                fetch("http://172.23.44.14/MaterialSystem/public/api/kirimPemesanan", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(data),
                    })
                    .then((response) => response.json())
                    .then((result) => {
                        if (result.status == "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.message,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Refresh halaman setelah tombol OK ditekan
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'result.message',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Refresh halaman setelah tombol OK ditekan
                                    location.reload();
                                }
                            });
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan saat mengirim data.");
                    });
            }
        });
        // END PROSES KIRIM PEMESANAN
    });
</script>
<?php $this->endSection(); ?>