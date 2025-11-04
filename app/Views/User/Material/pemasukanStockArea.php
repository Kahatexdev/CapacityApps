<?php $this->extend('user/layout'); ?>
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
                        <div class="col-7">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                            <h5 class="font-weight-bolder mb-0">
                                Penerimaan Bahan Baku <?= $area ?>
                            </h5>
                            <!-- <p style="color: red; font-size: 12px;">*Filter tgl po tambahan terlebih dahulu!</p> -->
                        </div>
                        <div class="col-5 d-flex align-items-center text-end gap-2">
                            <input type="hidden" class="form-control" id="area" value="<?= $area ?>">
                            <label for="no_model">No Model</label>
                            <input type="text" class="form-control" id="no_model" value="" placeholder="No Model">
                            <!-- <label for="tgl_po">Tgl Po Tambahan</label>
                            <input type="date" class="form-control" id="tgl_po" value="" required> -->
                            <button id="searchFilter" class="btn btn-info ms-2"><i class="fas fa-search"></i> Filter</button>
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
                        <div id="headerModel" class="table-header" style="display: none; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h4 style="margin: 0;"><span id="table-header"></span></h4>

                            <div class="d-flex align-items-center  gap-3">
                                <!-- <button id="exportPdfBtn" class="btn btn-danger">Export PDF</button> -->
                                <input type="hidden" id="tgl_buat" name="tgl_buat" value="">
                                <button id="generatePdfBtn" class="btn btn-danger">Export PDF</button>
                                <button id="generateExcelBtn" class="btn btn-success">Export Excel</button>
                            </div>
                        </div>

                        <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Item_Type</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode Warna</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Color</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kgs Terima</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Cns Terima</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Lot Terima</th>
                                    <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2" colspan=2>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal export pdf -->
<div class="modal fade" id="exportPdf" tabindex="-1" role="dialog" aria-labelledby="exportPdf" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export PDF</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body align-items-center">
                <div class="form-group">
                    <label for="area" class="col-form-label">Tanggal Buka PO(+)</label>
                    <input type="date" class="form-control" id="tgl_buat">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="generatePdfBtn" class="btn bg-gradient-info">Generate</button>
                <button type="button" id="generateExcelBtn" class="btn bg-gradient-success">Generate Excel</button>
            </div>
        </div>
    </div>
</div>

<script>
    let dataTable = $('#dataTable').DataTable();
    dataTable.clear(); // Hapus semua data sebelumnya
</script>

<?php $this->endSection(); ?>