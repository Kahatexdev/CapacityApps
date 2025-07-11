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
                                    Data Semua Order
                                </h5>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-success shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#exportDataOrder"><i class="fas fa-file-export text-lg opacity-10" aria-hidden="true"></i> Excel</button>
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
                    <table id="example" class="display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Created At</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Buyer</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Order</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Product Type</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Desc</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Seam</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Leadtime</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Order</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa Order</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <i class="ni ni-cloud-upload-96" style="font-size: 48px;"></i>
                                <p class="mt-3" style="font-size: 28px;">Upload file here</p>
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
    <!-- modal export data order -->
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
                                <?php foreach ($area as $ar) : ?>
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
                            <label for="tgl_turun_order" class="col-form-label">Tgl Turun Order</label>
                            <input type="date" class="form-control" name="tgl_turun_order">
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

    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Trigger import modal when import button is clicked
            $(document).on('click', '.import-btn', function() {
                var idModel = $(this).data('id');
                var noModel = $(this).data('no-model');

                $('#importModal').find('input[name="id_model"]').val(idModel);
                $('#importModal').find('input[name="no_model"]').val(noModel);

                $('#importModal').modal('show'); // Show the modal
            });

            $('#example').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '<?= base_url($role . '/tampilPerdelivery') ?>',
                    type: 'POST'
                },
                "columns": [{
                        "data": "created_at",
                        "render": function(data, type, row) {
                            if (data) {
                                // Parse the date and format it as d-M-Y
                                var date = new Date(data);
                                var day = ('0' + date.getDate()).slice(-2);
                                var month = date.toLocaleString('default', {
                                    month: 'short'
                                });
                                var year = date.getFullYear();
                                return `${day}-${month}-${year}`;
                            }
                            return data;
                        }
                    },
                    {
                        "data": "kd_buyer_order"
                    },
                    {
                        "data": "no_model"
                    },
                    {
                        "data": "no_order"
                    },
                    {
                        "data": "machinetypeid"
                    },
                    {
                        "data": "product_type"
                    },
                    {
                        "data": "description"
                    },
                    {
                        "data": "seam"
                    },
                    {
                        "data": "leadtime"
                    },
                    {
                        "data": "factory"
                    },
                    {
                        "data": "qty",
                        "render": function(data, type, row) {
                            if (data) {
                                return parseFloat(data).toLocaleString('en-US', {
                                    minimumFractionDigits: 0
                                });
                            }
                            return data;
                        }
                    },
                    {
                        "data": "sisa",
                        "render": function(data, type, row) {
                            if (data) {
                                return parseFloat(data).toLocaleString('en-US', {
                                    minimumFractionDigits: 0
                                });
                            }
                            return data;
                        }
                    },
                    {
                        "data": "delivery",
                        "render": function(data, type, row) {
                            if (data) {
                                // Parse the date and format it as d-M-Y
                                var date = new Date(data);
                                var day = ('0' + date.getDate()).slice(-2);
                                var month = date.toLocaleString('default', {
                                    month: 'short'
                                });
                                var year = date.getFullYear();
                                return `${day}-${month}-${year}`;
                            }
                            return data;
                        }
                    },
                    {
                        "data": null,
                        "render": customRender
                    }
                ],
                "order": []
            });
        });

        function customRender(data, type, row) {
            if (row.qty === null) {
                return `<button type="button" class="btn import-btn btn-success text-xs" data-toggle="modal" data-target="#importModal" data-id="${row.id_model}" data-no-model="${row.no_model}">
                            Import
                        </button>`;
            } else {
                return `<a href="<?= base_url($role . '/detailPdk') ?>/${row.no_model}/${row.machinetypeid}">
                            <button type="button" class="btn btn-info btn-sm details-btn">Details</button>
                        </a>`;
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('exportDataOrderForm');
            form.addEventListener('submit', function(e) {
                // ambil semua nilai field
                const buyer = form.buyer.value;
                const area = form.area.value;
                const jarum = form.jarum.value;
                const pdk = form.pdk.value.trim();
                const tglOrder = form.tgl_turun_order.value;
                const awal = form.awal.value;
                const akhir = form.akhir.value;

                // cek: ada minimal satu yang tidak kosong?
                const isAnyFilled = [buyer, area, jarum, pdk, tglOrder, awal, akhir]
                    .some(val => val !== '' && val !== null);

                if (!isAnyFilled) {
                    e.preventDefault(); // batalkan submit
                    alert('Harap isi minimal salah satu field sebelum Generate!');
                    // fokus ke modal agar user bisa lihat pesan
                    const modalEl = document.getElementById('exportDataOrder');
                    const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    bsModal.show();
                }
            });
        });
    </script>

    <?php $this->endSection(); ?>