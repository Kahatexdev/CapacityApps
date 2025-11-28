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
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Detail Data Area <?= $area ?>
                        </h5>
                        <div>
                            <a href="<?= base_url($role . '/datamesinperarea/' . $area) ?>" class="btn bg-gradient-dark">
                                <i class="fas fa-arrow-circle-left me-2 text-lg opacity-10"></i>
                                Back</a>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="card mt-2">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4>Mesin Detail</h4>

                </div>
            </div>
            <?php if ($mesinDetail): ?>
                <div class="card-body p-3">

                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable2" class="display compact striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No MC</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>

                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Brand</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Dram</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode</th>
                                        <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mesinDetail as $mc) : ?>
                                        <tr>
                                            <td class="text-sm">
                                                <?php if ($mc['status'] === 'idle') : ?>
                                                    <span class="badge bg-info">Idle</span>
                                                <?php elseif ($mc['status'] === 'running') : ?>
                                                    <span class="badge bg-success">Running</span>
                                                <?php elseif ($mc['status'] === 'breakdown') : ?>
                                                    <span class="badge bg-danger">Breakdown</span>
                                                <?php elseif ($mc['status'] === 'sample') : ?>
                                                    <span class="badge bg-secondary">Sample</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-sm"><?= $mc['no_mc']; ?></td>
                                            <td class="text-sm"><?= $mc['jarum']; ?></td>
                                            <td class="text-sm"><?= $mc['brand']; ?></td>
                                            <td class="text-sm"><?= $mc['dram']; ?> </td>
                                            <td class="text-sm"><?= $mc['kode']; ?> </td>


                                            <td class="text-sm">
                                                <button type="button" class="btn btn-success btn-sm edit-mc-btn" data-toggle="modal" data-target="#EditMcModal" data-id="<?= $mc['id']; ?>" data-area="<?= $mc['area']; ?>" data-jarum="<?= $mc['jarum']; ?>" data-brand="<?= $mc['brand']; ?>" data-dram="<?= $mc['dram']; ?>" data-status="<?= $mc['status']; ?>" data-no_mc="<?= $mc['no_mc']; ?>" data-kode="<?= $mc['kode']; ?>">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card-footer">

                    </div>

                </div>
            <?php endif; ?>
        </div>

        <div class="modal fade" id="EditMcModal" tabindex="-1" role="dialog" aria-labelledby="EditMcModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Data </h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editMcForm" method="post">
                            <div class="row">
                                <div class="col-lg-6 col-sm-6">

                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">No Mc</label>
                                        <input type="hidden" class="form-control" name="area" value="<?= $area ?>">
                                        <input type="hidden" name="id">
                                        <input type="text" class="form-control" name="no_mc">
                                    </div>
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">Jarum</label>
                                        <input type="text" class="form-control" name="jarum">
                                    </div>
                                    <div class="form-group">
                                        <label for="buyer" class="col-form-label">Brand</label>
                                        <input type="text" name="brand" class="form-control">
                                    </div>


                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">Dram</label>
                                        <input type="text" class="form-control" name="dram">
                                    </div>
                                    <div class="form-group">
                                        <label for="tgl-bk" class="col-form-label">kode</label>
                                        <input type="text" class="form-control" name="kode">
                                    </div>
                                    <div class="form-group">
                                        <label for="status" class="col-form-label">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="idle">Idle</option>
                                            <option value="running">Running</option>
                                            <option value="breakdown">Breakdown</option>
                                            <option value="sample">Sample</option>
                                        </select>
                                    </div>


                                </div>
                            </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Edit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade  bd-example-modal-lg" id="ModalDeleteMc" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteMc" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Machine</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post">
                            <input type="text" name="id" id="" hidden value="">
                            <input type="text" name="area" id="" hidden value="<?= $area ?>">
                            Are you sure you want to delete ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Delete</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- <script>
            function valildasi() {
                let qty = parseInt(document.getElementById("qty").value);
                let sisa = parseInt(document.getElementById("sisa").value);

                if (sisa > qty) {
                    alert("Qty tidak boleh melebihi sisa!");
                    document.getElementById("sisa").value = qty; // Reset nilai qty menjadi nilai sisa
                }
            }
        </script> -->
        <script>
            $(document).ready(function() {

                let table = $('#dataTable2').DataTable({
                    order: [
                        [1, 'asc']
                    ],
                    lengthMenu: [
                        [100, -1],
                        [100, "All"]
                    ]
                });

                function numberWithDots(x) {
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }

                $('.btn-add').click(function() {
                    $('#modalTambah').find('form').attr('action', '<?= base_url($role . '/tambahmesinperarea') ?>');

                    $('#modalTambah').modal('show'); // Show the modal
                });


                $(document).on('click', '.edit-mc-btn', function() {
                    var idMc = $(this).data('id');
                    var no_mc = $(this).data('no_mc');
                    var area = $(this).data('area');
                    var jarum = $(this).data('jarum');
                    var dram = $(this).data('dram');
                    var status = $(this).data('status');
                    var brand = $(this).data('brand');
                    var kode = $(this).data('kode');

                    $('#editMcForm').attr('action', '<?= base_url($role . "/updatemesinpernomor/") ?>' + idMc);
                    $('#editMcForm input[name="id"]').val(idMc);
                    $('#editMcForm input[name="no_mc"]').val(no_mc);
                    $('#editMcForm input[name="area"]').val(area);
                    $('#editMcForm input[name="jarum"]').val(jarum);
                    $('#editMcForm input[name="dram"]').val(dram);
                    $('#editMcForm input[name="brand"]').val(brand);
                    $('#editMcForm input[name="kode"]').val(kode);
                    $('#editMcForm select[name="status"]').val(status);

                    $('#EditMcModal').modal('show');
                });
                $('#editMcForm').on('submit', function(e) {
                    e.preventDefault();

                    var form = $(this);
                    var actionUrl = form.attr('action');

                    $.ajax({
                        type: "POST",
                        url: actionUrl,
                        data: form.serialize(),
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {


                                // cari row berdasarkan tombol edit yg punya data-id sama
                                let rowSelector = 'button[data-id="' + response.data.id + '"]';
                                let row = table.row($(rowSelector).closest('tr'));

                                // update row pakai datatables api
                                row.data([
                                    response.data.status_badge,
                                    response.data.no_mc,
                                    response.data.jarum,
                                    response.data.brand,
                                    response.data.dram,
                                    response.data.kode,
                                    `<button type="button" 
                        class="btn btn-success btn-sm edit-mc-btn" 
                        data-toggle="modal" 
                        data-target="#EditMcModal"
                        data-id="${response.data.id}" 
                        data-area="${response.data.area}" 
                        data-jarum="${response.data.jarum}" 
                        data-brand="${response.data.brand}" 
                        data-dram="${response.data.dram}" 
                        data-status="${response.data.status}" 
                        data-no_mc="${response.data.no_mc}" 
                        data-kode="${response.data.kode}">
                        Edit
                    </button>`
                                ]).draw(false);

                                $('#EditMcModal').modal('hide');
                                table.ajax.reload(null, false);
                            } else {
                                alert('Failed to update!');
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            alert('Error updating data.');
                        }
                    });
                });



                $(document).on('click', '.delete-mc-btn', function() {
                    var id = $(this).data('id');
                    $('#ModalDeleteMc').find('form').attr('action', '<?= base_url($role . '/deletemesinpernomor/') ?>' + id);
                    $('#ModalDeleteMc').find('input[name="id"]').val(id);
                    $('#ModalDeleteMc').modal('show'); // Show the modal
                });


            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>