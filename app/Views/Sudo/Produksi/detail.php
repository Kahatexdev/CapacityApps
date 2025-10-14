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
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-12">
            <div class="card">
                <div class="card-body">
                    <!-- Baris Pertama: H5 dan Tombol Kembali -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h5>
                                Data Produksi <?= $area ?> <?= $bulan ?>
                            </h5>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="<?= base_url($role . '/dataproduksi/') ?>" class="btn bg-gradient-info">Kembali</a>
                        </div>
                    </div>
                    <!-- Baris Kedua: Form -->
                    <div class="row">
                        <div class="col-12">
                            <form action="<?= base_url($role . '/detailproduksi/' . $area) ?>" method="get">
                                <div class="row">
                                    <div class="col-md-2 mb-2">
                                        <p>Tgl Produksi Dari</p>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <p>Tgl Produksi Sampai</p>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <p>No Model</p>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <p>No Box</p>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <p>No Label</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 mb-2">
                                        <input type="date" class="form-control text-secondary" name="tgl_produksi" id="tgl_produksi">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <input type="date" class="form-control text-secondary" name="tgl_produksi_sampai" id="tgl_produksi_sampai">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <input type="text" class="form-control" name="no_model" id="filter_no_model" placeholder="No Model">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <input type="text" class="form-control" name="no_box" id="filter_no_box" placeholder="No Box">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <input type="text" class="form-control" name="no_label" id="filter_no_label" placeholder="No Label">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="submit" class="btn bg-gradient-success">FILTER</button>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn bg-gradient-success" id="btnExport"><i class="fas fa-file-excel"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <?php if (empty($produksi)) : ?>
                                <p class="text-center">Silakan gunakan filter untuk menampilkan data produksi.</p>
                            <?php else : ?>
                                <table id="dataTable" class="display  striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Tgl Produksi</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">PDK</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Style</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Delivery</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">No MC</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">No Box</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">No Label</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Qty Produksi</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($produksi as $order) : ?>
                                            <tr>
                                                <td class="text-sm text-center"><?= $order['tgl_produksi']; ?></td>
                                                <td class="text-sm text-center"><?= $order['mastermodel']; ?></td>
                                                <td class="text-sm text-center"><?= $order['size']; ?></td>
                                                <td class="text-sm text-center"><?= $order['delivery']; ?></td>
                                                <td class="text-sm text-center"><?= $order['no_mesin']; ?></td>
                                                <td class="text-sm text-center"><?= $order['no_box']; ?></td>
                                                <td class="text-sm text-center"><?= $order['no_label']; ?></td>
                                                <td class="text-sm text-center"><?= $order['qty_produksi']; ?></td>
                                                <td class="text-sm text-center">
                                                    <button class="btn btn-warning edit-btn" data-id="<?= $order['id_produksi']; ?>" data-pdk="<?= $order['mastermodel']; ?>" data-style="<?= $order['size']; ?>" data-nomc="<?= $order['no_mesin']; ?>" data-nobox="<?= $order['no_box']; ?>" data-nolabel="<?= $order['no_label']; ?>" data-shift-a=<?= $order['shift_a'] ?> data-shift-b=<?= $order['shift_b'] ?> data-shift-c=<?= $order['shift_c'] ?> data-qty="<?= $order['qty_produksi']; ?>" data-tgl="<?= $order['tgl_produksi']; ?>" data-sisa="<?= $order['sisa']; ?>" data-idaps="<?= $order['idapsperstyle']; ?>">
                                                        Edit</button>
                                                    <!-- Button Delete -->
                                                    <button class="btn btn-danger delete-btn" data-id="<?= $order['id_produksi']; ?>" data-pdk="<?= $order['mastermodel']; ?>" data-style="<?= $order['size']; ?>" data-nomc="<?= $order['no_mesin']; ?>" data-nobox="<?= $order['no_box']; ?>" data-nolabel="<?= $order['no_label']; ?>" data-qty="<?= $order['qty_produksi']; ?>" data-tgl="<?= $order['tgl_produksi']; ?>" data-sisa="<?= $order['sisa']; ?>" data-idaps="<?= $order['idapsperstyle']; ?>">
                                                        Delete</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal fade bd-example-modal-lg" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Data Produksi</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/editproduksi') ?>" method="post">
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="text" name="id" id="" hidden class="form-control" value="">
                                    <input type="text" name="sisa" id="" hidden class="form-control" value="">
                                    <input type="text" name="idaps" id="" hidden class="form-control" value="">
                                    <input type="text" name="qtycurrent" id="" hidden class="form-control" value="">
                                    <input type="text" name="area" id="" hidden class="form-control" value="<?= $area ?>">
                                    <div class="form-group">
                                        <label for="mastermodel">No Model:</label>
                                        <input type="text" name="no_model" id="no_model" class="form-control" readonly value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="style">Style :</label>
                                        <input type="text" name="style" id="style" class="form-control" readonly value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="style">Tgl Input Produksi :</label>
                                        <input type="date" name="tgl_prod" id="tgl_prod" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="no_mc">No Mesin :</label>
                                        <input type="text" name="no_mc" id="no_mc" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="no_box">No Box :</label>
                                        <input type="text" name="no_box" id="no_box" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="no_label">No Label :</label>
                                        <input type="text" name="no_label" id="no_label" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="shift_a">Shift A :</label>
                                        <input type="text" name="shift_a" id="shift_a" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="shift_b">Shift B :</label>
                                        <input type="text" name="shift_b" id="shift_b" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="shift_c">Shift C :</label>
                                        <input type="text" name="shift_c" id="shift_c" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="qty_prod">Qty Produksi :</label>
                                        <input type="text" name="qty_prod" id="qty_prod" class="form-control" value="" readonly>
                                    </div>
                                </div>
                            </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Ubah</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // 1. Isi input filter dari query string agar saat reload, nilai tetap tampil
        const params = new URLSearchParams(window.location.search);
        $('#tgl_produksi').val(params.get('tgl_produksi') || '');
        $('#tgl_produksi_sampai').val(params.get('tgl_produksi_sampai') || '');
        $('#filter_no_model').val(params.get('no_model') || '');
        $('#filter_no_box').val(params.get('no_box') || '');
        $('#filter_no_label').val(params.get('no_label') || '');

        // 2. Tombol Export
        $('#btnExport').click(function() {
            let currentQuery = window.location.search; // ambil query string filter saat ini
            window.location.href = "<?= base_url('sudo/detailproduksi_export/' . $area) ?>" + currentQuery;
        });

        // 3. Inisialisasi DataTable
        $('#dataTable').DataTable({
            "order": [
                [0, "desc"]
            ]
        });
    });
    $('.edit-btn').click(function() {
        var noModel = $(this).data('pdk');
        var id_prod = $(this).data('id');
        var style = $(this).data('style');
        var nobox = $(this).data('nobox');
        var qty = $(this).data('qty');
        var nolabel = $(this).data('nolabel');
        var nomc = $(this).data('nomc');
        var tgl = $(this).data('tgl');
        var sisa = $(this).data('sisa');
        var idaps = $(this).data('idaps');
        var shiftA = $(this).data('shift-a');
        var shiftB = $(this).data('shift-b');
        var shiftC = $(this).data('shift-c');



        $('#editModal').modal('show'); // Show the modal
        $('#editModal').find('input[name="no_model"]').val(noModel);
        $('#editModal').find('input[name="id"]').val(id_prod);
        $('#editModal').find('input[name="style"]').val(style);
        $('#editModal').find('input[name="no_mc"]').val(nomc);
        $('#editModal').find('input[name="no_box"]').val(nobox);
        $('#editModal').find('input[name="no_label"]').val(nolabel);
        $('#editModal').find('input[name="qty_prod"]').val(qty);
        $('#editModal').find('input[name="qtycurrent"]').val(qty);
        $('#editModal').find('input[name="tgl_prod"]').val(tgl);
        $('#editModal').find('input[name="sisa"]').val(sisa);
        $('#editModal').find('input[name="idaps"]').val(idaps);
        $('#editModal').find('input[name="shift_a"]').val(shiftA);
        $('#editModal').find('input[name="shift_b"]').val(shiftB);
        $('#editModal').find('input[name="shift_c"]').val(shiftC);

        // ambil data dari button
        var $btn = $(this);
        var shiftA = +$btn.data('shift-a') || 0;
        var shiftB = +$btn.data('shift-b') || 0;
        var shiftC = +$btn.data('shift-c') || 0;
        var qty = +$btn.data('qty') || 0;
        // isi modal
        $('#editModal').modal('show');
        $('#shift_a').val(shiftA);
        $('#shift_b').val(shiftB);
        $('#shift_c').val(shiftC);
        $('#qty_prod').val(qty);
        // ... isi field lain seperti sebelumnya ...

        // fungsi untuk recalculate
        function recalc() {
            var a = +$('#shift_a').val() || 0;
            var b = +$('#shift_b').val() || 0;
            var c = +$('#shift_c').val() || 0;
            $('#qty_prod').val(a + b + c);
        }

        // bind listener (hapus dulu untuk menghindari ganda)
        $('#shift_a, #shift_b, #shift_c')
            .off('input.recalc') // namespace supaya safe
            .on('input.recalc', recalc);

        // jalankan sekali agar langsung terupdate
        recalc();

        // document.getElementById('confirmationMessage').innerHTML = "Apakah anda yakin memecah" + noModel + " dengan jarum " + selectedMachineTypeId + " ke " + selectedArea;
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll(".delete-btn");

        deleteButtons.forEach(button => {
            button.addEventListener("click", function(event) {
                event.preventDefault(); // Hindari submit langsung

                const id = this.getAttribute("data-id");
                const idaps = this.getAttribute("data-idaps");
                const qty = this.getAttribute("data-qty");
                const sisa = this.getAttribute("data-sisa");
                const area = "<?= $area ?>";

                Swal.fire({
                    title: "Yakin ingin menghapus data ini?",
                    text: "Data akan dihapus secara permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "<?= base_url($role . '/hapus-produksi/'); ?>" + id + "?idaps=" + idaps + "&area=" + area + "&qty=" + qty + "&sisa=" + sisa;
                    }
                });
            });
        });
    });
</script>

<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>