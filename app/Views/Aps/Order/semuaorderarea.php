<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Semua Order di Area <?= $area ?>
                                </h5>
                            </div>
                        </div>
                        <div>
                            <a href="<?= base_url($role . '/estimasispk/' . $area) ?>" class="btn btn-info">Estimasi SPK</a>
                            <a href="<?= base_url($role . '/statusorder/' . $area) ?>" class="btn btn-info">Status Order</a>
                            <a type="button" class="btn bg-gradient-success" data-bs-toggle="modal" data-bs-target="#exportModal"><i class="fas fa-file-excel"></i> Excel Data Order</a>
                        </div>

                    </div>
                </div>
            </div>



        </div>


        <div class="row mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="display compact " style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Turun PDK</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Buyer</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Product Type</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Desc</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Seam</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Leadtime</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Shipment</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Order</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Order</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tampildata as $order) :
                                    $isDouble = in_array(strtoupper($order->machinetypeid), ['240', '240-PL']);

                                    $qty = $isDouble ? $order->qty * 2 : $order->qty;
                                    $sisa = $isDouble ? $order->sisa * 2 : $order->sisa; ?>
                                    <tr>
                                        <td class="text-xs"><?= $order->created_at; ?></td>
                                        <td class="text-xs"><?= $order->kd_buyer_order; ?></td>
                                        <td class="text-xs"><?= $order->no_model; ?></td>
                                        <td class="text-xs"><?= $order->product_type; ?></td>
                                        <td class="text-xs"><?= $order->description; ?></td>
                                        <td class="text-xs"><?= $order->seam; ?></td>
                                        <td class="text-xs"><?= $order->leadtime; ?></td>
                                        <td class="text-xs"><?= $order->delivery; ?></td>
                                        <td class="text-xs"><?= $order->qty; ?> dz</td>
                                        <td class="text-xs"><?= $order->sisa; ?> dz</td>
                                        <td class="text-xs">
                                            <?php if ($order->qty === null) : ?>
                                                <!-- If qty is null, set action to Import -->
                                                <button type="button" class="btn btn-success text-xs import-btn" data-toggle="modal" data-target="#importModal" data-id="<?= $order->id_model; ?>" data-no-model="<?= $order->no_model; ?>">
                                                    Import
                                                </button>
                                            <?php else : ?>
                                                <!-- If qty is not null, set action to Details -->
                                                <a href="<?= base_url($role . '/detailPdkAps/' . $order->no_model . '/'   . $area); ?>" <button type="button" class="btn btn-info btn-sm details-btn">
                                                    Details
                                                    </button>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
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

        <!-- modal -->
        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModal" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Input Aps Report</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">Ã—</span>
                        </button>
                    </div>
                    <div class="modal-body align-items-center">
                        <div class="row align-items-center">
                            <div id="drop-area" class="border rounded d-flex justify-content-center align-item-center mx-3" style="height:200px; width: 95%; cursor:pointer;">
                                <div class="text-center mt-5">
                                    <i class="ni ni-cloud-upload-96" style="font-size: 48px;">

                                    </i>
                                    <p class="mt-3" style="font-size: 28px;">
                                        Upload file here
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-9 pl-0">

                                <form action="<?= base_url($role . '/importModel') ?>" id="modalForm" method="POST" enctype="multipart/form-data">
                                    <input type="text" class="form-control" name="id_model" hidden>
                                    <input type="text" class="form-control" name="no_model" hidden>
                                    <input type="file" id="fileInput" name="excel_file" multiple accept=".xls , .xlsx" class="form-control ">
                            </div>
                            <div class="col-3 pl-0">
                                <button type="submit" class="btn btn-info btn-block"> Simpan</button>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- export modal -->
        <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModal" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Export Data Order</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body align-items-center">
                        <form action="<?= base_url($role . '/exportDataOrderArea') ?>" method="POST">
                            <div class="row align-items-center">
                                <label for="searchModel">Pilih Model</label>
                                <input type="hidden" value="<?= $area ?>" name="area">
                                <select name="searchModel" id="searchModel" class="form-select">
                                    <?php foreach ($tampildata as $order) : ?>
                                        <option value="<?= $order->no_model ?>"><?= $order->no_model ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row mt-2">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-success btn-block">Generate</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#example').DataTable({
                    "order": [
                        [0, 'desc'] // Kolom pertama (indeks 0) diurutkan secara descending
                    ]
                });

                $('#searchModel').select2({
                    placeholder: 'Cari model...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#exportModal') // Ini penting
                });

                // Trigger import modal when import button is clicked
                $('.import-btn').click(function() {
                    var idModel = $(this).data('id');
                    var noModel = $(this).data('no-model');

                    $('#importModal').find('input[name="id_model"]').val(idModel);
                    $('#importModal').find('input[name="no_model"]').val(noModel);

                    $('#importModal').modal('show'); // Show the modal
                });
            });
        </script>

        <?php $this->endSection(); ?>