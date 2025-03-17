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
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kg Pesan</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Cns Pesan</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Lot</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Retur</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Jatah</th>
                                    <th class="text-uppercase text-dark text-xxs text-center font-weight-bolder opacity-7 ps-2" colspan="2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($dataList as $key => $id) {
                                ?>
                                    <td class="text-xs text-start"><?= $no++; ?></td>
                                    <td class="text-xs text-start"><?= $id['tgl_pakai']; ?></td>
                                    <td class="text-xs text-start"><?= $id['no_model']; ?></td>
                                    <td class="text-xs text-start"><?= $id['item_type']; ?></td>
                                    <td class="text-xs text-start"><?= $id['kode_warna']; ?></td>
                                    <td class="text-xs text-start"><?= $id['color']; ?></td>
                                    <td class="text-xs text-start"><?= number_format($id['kg_keb'], 2); ?></td>
                                    <td class="text-xs text-start"><?= number_format($id['qty_pesan'] - $id['qty_sisa'], 2); ?></td>
                                    <td class="text-xs text-start"><?= $id['cns_pesan'] - $id['cns_sisa']; ?></td>
                                    <td class="text-xs text-start"><?= $id['lot']; ?></td>
                                    <td class="text-xs text-start"><?= $id['keterangan']; ?></td>
                                    <td class="text-xs text-start"></td>
                                    <td class="text-xs text-start"><?= number_format($id['sisa_jatah'], 2); ?></td>
                                    <td class="text-xs text-start">
                                        <button type="button" class="btn btn-warning update-btn" data-toggle="modal" data-target="#updateListModal" data-area="<?= $area; ?>" data-tgl="<?= $id['tgl_pakai']; ?>" data-model="<?= $id['no_model']; ?>" data-item="<?= $id['item_type']; ?>" data-kode="<?= $id['kode_warna']; ?>">
                                            <i class="fa fa-edit fa-lg"></i>
                                        </button>
                                    </td>
                                    <td class="text-xs">
                                        <?php if ($id['sisa_jatah'] > 0) { ?>
                                            <button type="button" class="btn btn-info text-xs send-btn" data-toggle="modal">
                                                <i class="fa fa-paper-plane fa-lg"></i>
                                            </button>
                                        <?php } else { ?>
                                            <span style="color: red;">Habis Jatah</span>
                                        <?php } ?>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update List Pemesanan</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body align-items-center">
                <div class="col-lg-12">
                    <form action="<?= base_url($role . '/updateListPemesanan') ?>" id="modalForm" method="POST" enctype="multipart/form-data">

                        <div class="row">
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Area</label>
                                <input type="text" class="form-control" name="area">
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Tgl Pakai</label>
                                <input type="text" class="form-control" name="tgl_pakai">
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">No Model</label>
                                <input type="text" class="form-control" name="no_model">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Item Type</label>
                                <input type="text" class="form-control" name="item_type">
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Kode Warna</label>
                                <input type="text" class="form-control" name="kode_warna">
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Warna</label>
                                <input type="text" class="form-control" name="color">
                            </div>
                            <div class="col-lg-3">
                                <label for="message-text" class="col-form-label">Message:</label>
                                <textarea class="form-control" id="message-text"></textarea>
                            </div>
                        </div>
                        <div class="col-3 pl-0">
                            <button type="submit" class="btn btn-info btn-block"> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal update list pemesanan end -->
<script src=" <?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({
            "order": [
                [0, 'asc'] // Kolom pertama (indeks 0) diurutkan secara descending
            ]
        });

        // Trigger import modal when import button is clicked
        $('.update-btn').click(function() {
            var area = $(this).data('area');
            var tglPakai = $(this).data('tgl');
            var noModel = $(this).data('model');
            var itemType = $(this).data('item');
            var kode_warna = $(this).data('kode');

            $('#updateListModal').find('input[name="area"]').val(area);
            $('#updateListModal').find('input[name="tgl_pakai"]').val(tglPakai);
            $('#updateListModal').find('input[name="no_model"]').val(noModel);
            $('#updateListModal').find('input[name="item_type"]').val(itemType);
            $('#updateListModal').find('input[name="kode_warna"]').val(kode_warna);

            $('#updateListModal').modal('show'); // Show the modal
        });
    });
</script>
<?php $this->endSection(); ?>