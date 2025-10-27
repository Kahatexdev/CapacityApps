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

                            <button type="button" class="btn btn-sm btn-success bg-gradient-success shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#exportDataOrder"><i class="fas fa-file-export text-lg opacity-10" aria-hidden="true"></i> Excel</button>
                            <a href="<?= base_url($role . '/formatImportInisial') ?>" class="btn btn-success bg-gradient-success shadow text-center border-radius-md">Format Import Inisial</a>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalMessage" class="btn btn-success bg-gradient-success shadow text-center border-radius-md">
                                Import Inisial
                            </button>
                            <a href="<?= base_url($role . '/estimasispk/' . $area) ?>" class="btn btn-info">Estimasi SPK</a>
                            <a href="<?= base_url($role . '/statusorder/' . $area) ?>" class="btn btn-info">Status Order</a>


                        </div>
                    </div>
                </div>



            </div>
            <div class="modal fade" id="exportDataOrder" tabindex="-1" role="dialog" aria-labelledby="exportDataOrder" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Export Data Order</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <form id="exportDataOrderForm" action="<?= base_url($role . '/exportDataOrder'); ?>" method="POST">
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
                                        <?php foreach ($listArea as $ar) : ?>
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
                                    <label for="pdk" class="col-form-label">Seam</label>
                                    <input type="text" class="form-control" name="seam">
                                </div>
                                <div class="form-group">
                                    <label for="pdk" class="col-form-label">Process Routes</label>
                                    <input type="text" class="form-control" name="process_routes">
                                </div>
                                <div class="form-group">
                                    <label for="tgl_turun_order" class="col-form-label">Tgl Turun Order Dari</label>
                                    <input type="date" class="form-control" name="tgl_turun_order">
                                </div>
                                <div class="form-group">
                                    <label for="tgl_turun_order" class="col-form-label">Tgl Turun Orde Sampai</label>
                                    <input type="date" class="form-control" name="tgl_turun_order_akhir">
                                </div>
                                <div class="form-group">
                                    <label for="awal" class="col-form-label">Delivery Dari</label>
                                    <input type="date" class="form-control" name="awal">
                                </div>
                                <div class="form-group">
                                    <label for="akhir" class="col-form-label">Delivery Sampai</label>
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
            <div class="modal fade  bd-example-modal-lg" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Import Inisial</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body align-items-center">
                            <div class="row align-items-center">
                                <div id="drop-area" class="border rounded d-flex justify-content-center align-item-center mx-3" style="height:200px; width: 95%; cursor:pointer;">
                                    <div class="text-center mt-5">
                                        <i class="ni ni-cloud-upload-96" style="font-size: 48px;"></i>
                                        <p class="mt-3" style="font-size: 28px;">Upload file here</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-9 pl-0">
                                    <form action="<?= base_url($role . '/importinisial') ?>" id="modalForm" method="POST" enctype="multipart/form-data">
                                        <input type="text" class="form-control" name="id_model" hidden>
                                        <input type="text" class="form-control" name="no_model" hidden>
                                        <input type="file" id="fileInput" name="excel_file" multiple accept=".xls , .xlsx" class="form-control ">
                                </div>
                                <div class="col-3 pl-0">
                                    <form>
                                        <!-- Other form inputs go here -->
                                        <button type="submit" class="btn btn-info btn-block" onclick="this.disabled=true; this.form.submit();">Simpan</button>
                                    </form>
                                </div>
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Product Type</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Desc</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Seam</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Shipment</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Status Planning</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tampildata as $order) : ?>
                                        <tr>
                                            <td class="text-xs"><?= $order->created_at; ?></td>
                                            <td class="text-xs"><?= $order->kd_buyer_order; ?></td>
                                            <td class="text-xs"><?= $order->no_model; ?></td>
                                            <td class="text-xs"><?= $order->machinetypeid; ?></td>
                                            <td class="text-xs"><?= $order->product_type; ?></td>
                                            <td class="text-xs"><?= $order->description; ?></td>
                                            <td class="text-xs"><?= $order->seam; ?></td>
                                            <td class="text-xs"><?= $order->delivery; ?></td>
                                            <td class="text-xs"><?= $order->qty; ?></td>
                                            <td class="text-xs"><?= $order->sisa; ?></td>
                                            <td class="text-xs"><?= $order->status_plan; ?></td>
                                            <td class="text-xs">
                                                <?php if ($order->qty === null) : ?>
                                                    <!-- If qty is null, set action to Import -->
                                                    <button type="button" class="btn btn-success text-xs import-btn" data-toggle="modal" data-target="#importModal" data-id="<?= $order->id_model; ?>" data-no-model="<?= $order->no_model; ?>">
                                                        Import
                                                    </button>
                                                <?php else : ?>
                                                    <!-- If qty is not null, set action to Details -->
                                                    <a href="<?= base_url($role . '/detailPdk/' . $order->no_model . '/'  . $order->machinetypeid); ?>" <button type="button" class="btn btn-info btn-sm details-btn">
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
                                <span aria-hidden="true">×</span>
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

            <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#example').DataTable({
                        "order": [
                            [0, 'desc'] // Kolom pertama (indeks 0) diurutkan secara descending
                        ]
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