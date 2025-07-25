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
                            <form action="<?= base_url($role . '/detailproduksi') ?>" method="get">
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
                                        <input type="text" class="form-control" name="no_model" id="no_model" placeholder="No Model">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <input type="text" class="form-control" name="no_box" id="no_box" placeholder="No Box">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <input type="text" class="form-control" name="no_label" id="no_label" placeholder="No Label">
                                    </div>
                                    <div class="col-md-1 mb-2">
                                        <select id="filter-tahun" class="form-control d-inline w-auto" name="area">
                                            <option value="">Pilih Area</option>
                                            <?php foreach ($listArea as $ar): ?>
                                                <option value="<?= $ar ?>"><?= $ar ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="submit" class="btn bg-gradient-success">FILTER</button>
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
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2 text-center">Area</th>
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
                                                <td class="text-sm text-center"><?= $order['area']; ?></td>
                                                <td class="text-sm text-center"><?= $order['mastermodel']; ?></td>
                                                <td class="text-sm text-center"><?= $order['size']; ?></td>
                                                <td class="text-sm text-center"><?= $order['delivery']; ?></td>
                                                <td class="text-sm text-center"><?= $order['no_mesin']; ?></td>
                                                <td class="text-sm text-center"><?= $order['no_box']; ?></td>
                                                <td class="text-sm text-center"><?= $order['no_label']; ?></td>
                                                <td class="text-sm text-center"><?= $order['qty_produksi']; ?></td>
                                                <td class="text-sm text-center">
                                                    <button class="btn btn-warning edit-btn" data-id="<?= $order['id_produksi']; ?>" data-pdk="<?= $order['mastermodel']; ?>" data-style="<?= $order['size']; ?>" data-nomc="<?= $order['no_mesin']; ?>" data-nobox="<?= $order['no_box']; ?>" data-nolabel="<?= $order['no_label']; ?>" data-qty="<?= $order['qty_produksi']; ?>" data-tgl="<?= $order['tgl_produksi']; ?>" data-sisa="<?= $order['sisa']; ?>" data-idaps="<?= $order['idapsperstyle']; ?>">
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
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="no_box">No Box :</label>
                                        <input type="text" name="no_box" id="no_box" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="no_label">No Label :</label>
                                        <input type="text" name="no_label" id="no_label" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="qty_prod">Qty Produksi :</label>
                                        <input type="text" name="qty_prod" id="qty_prod" class="form-control" value="">
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


        document.getElementById('confirmationMessage').innerHTML = "Apakah anda yakin memecah" + noModel + " dengan jarum " + selectedMachineTypeId + " ke " + selectedArea;
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