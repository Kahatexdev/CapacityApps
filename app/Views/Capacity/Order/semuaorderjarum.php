<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
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
                                    Data Order <?= $jarum ?>
                                </h5>
                            </div>
                        </div>
                        <div>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalMessage" class="btn btn-success bg-gradient-success shadow text-center border-radius-md">
                                Input Data Order
                            </button>
                            <a href="<?= base_url($role . '/orderPerjarum/') ?>" class="btn bg-gradient-info"> Back</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade  bd-example-modal-lg" id="exampleModalMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalMessageTitle" aria-hidden="true">
                <div class="modal-dialog  modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Input Data Order</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= base_url($role . '/inputOrderManual') ?>" method="post">
                                <input type="hidden" class="form-control" name="id" value=<?= $jarum ?>>
                                <div class="form-group">
                                    <label for="tgl-bk-form-label">Tanggal Turun Order</label>
                                    <input type="date" class="form-control" name="tgl_turun">
                                </div>
                                <div class="form-group">
                                    <label for="No Model" class="col-form-label">No Model</label>
                                    <input type="text" name="no_model" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="jarum" class="col-form-label">Needle</label>
                                    <select class="form-control" id="jarum" name="jarum">
                                        <option value="">Choose</option>
                                        <?php foreach ($jenisJarum as $jj) : ?>
                                            <option value="<?= $jj['jarum'] ?>"><?= $jj['jarum'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="productType" class="col-form-label">Product Type</label>
                                    <select class="form-control" id="productType" name="productType">
                                        <option value="">Choose</option>
                                    </select>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                        </div>
                        </form>
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
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Order</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
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
                            <?php foreach ($tampildata as $order) : ?>
                                <tr>
                                    <td class="text-xs"><?= date('d-M-y', strtotime($order->created_at)); ?></td>
                                    <td class="text-xs"><?= $order->kd_buyer_order; ?></td>
                                    <td class="text-xs"><?= $order->no_model; ?></td>
                                    <td class="text-xs"><?= $order->no_order; ?></td>
                                    <td class="text-xs"><?= $order->machinetypeid; ?></td>
                                    <td class="text-xs"><?= $order->product_type; ?></td>
                                    <td class="text-xs"><?= $order->description; ?></td>
                                    <td class="text-xs"><?= $order->seam; ?></td>
                                    <td class="text-xs"><?= $order->leadtime; ?> Days</td>
                                    <td class="text-xs"><?= date('d-M-y', strtotime($order->delivery)); ?></td>
                                    <td class="text-xs"><?= number_format(round($order->qty / 24), 0, ',', '.'); ?> Dz</td>
                                    <td class="text-xs"><?= number_format(round($order->sisa / 24), 0, ',', '.'); ?> Dz</td>
                                    <td class="text-xs">
                                        <?php if ($order->qty === null) : ?>
                                            <!-- If qty is null, set action to Import -->
                                            <button type="button" class="btn btn-success text-xs import-btn" data-toggle="modal" data-target="#importModal" data-id="<?= $order->id_model; ?>" data-no-model="<?= $order->no_model; ?>">
                                                Import
                                            </button>
                                        <?php else : ?>
                                            <!-- If qty is not null, set action to Details -->
                                            <a href="<?= base_url($role . '/detailmodeljarum/' . $order->no_model . '/' . $order->delivery . '/' . $order->machinetypeid); ?>" <button type="button" class="btn btn-info btn-sm details-btn">
                                                Details
                                                </button>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <th colspan=9></th>
                            <th> Total : </th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>


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
                "order": [],
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api();

                    var qty = api.column(10, {
                        page: 'current'
                    }).data().reduce(function(a, b) {
                        return parseInt(a) + parseInt(b);
                    }, 0);

                    // Calculate the total of the 5th column (Remaining Qty in dozens) - index 4
                    var sisa = api.column(11, {
                        page: 'current'
                    }).data().reduce(function(a, b) {
                        return parseInt(a) + parseInt(b);
                    }, 0);

                    // Format totalqty and totalsisa with " Dz" suffix and dots for thousands
                    var totalqty = numberWithDots(qty) + " Dz";
                    var totalsisa = numberWithDots(sisa) + " Dz";

                    // Update the footer cell for the total Qty
                    $(api.column(10).footer()).html(totalqty);

                    // Update the footer cell for the total Sisa
                    $(api.column(11).footer()).html(totalsisa);

                }
            });

            // Function to add dots for thousands
            function numberWithDots(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Trigger import modal when import button is clicked
            $('.import-btn').click(function() {
                var idModel = $(this).data('id');
                var noModel = $(this).data('no-model');

                $('#importModal').find('input[name="id_model"]').val(idModel);
                $('#importModal').find('input[name="no_model"]').val(noModel);

                $('#importModal').modal('show'); // Show the modal
            });
            $('#jarum').change(function() {
                var selectedJarum = $(this).val();
                if (selectedJarum) {
                    $.ajax({
                        url: '<?= base_url($role . '/getTypebyJarum') ?>', // Ubah dengan URL controller Anda
                        type: 'POST',
                        data: {
                            jarum: selectedJarum
                        },
                        dataType: 'json',
                        success: function(response) {

                            var productTypeSelect = $('#productType');
                            productTypeSelect.empty();
                            productTypeSelect.append('<option value="">Choose</option>'); // Tambahkan opsi default

                            // Iterasi melalui data yang diterima dan tambahkan ke select option
                            $.each(response, function(index, productType) {
                                productTypeSelect.append('<option value="' + productType + '">' + productType + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching data:', error);
                        }
                    });
                } else {
                    $('#productType').empty();
                    $('#productType').append('<option value="">Choose</option>');
                }
            });
        });
    </script>
    <?php $this->endSection(); ?>