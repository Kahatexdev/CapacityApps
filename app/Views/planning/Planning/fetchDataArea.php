<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<style>
    .badge {
        cursor: pointer;
        padding: 5px 10px;
        font-size: 12px;
    }
</style>
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
                    <div class="row">
                        <div class="col">
                            <h5>
                                Pick Data for Planning for Area <strong style="color: green;"><?= $area; ?></strong> by Needle <strong style="color: orange;"><?= $jarum; ?></strong>
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h6>
                                Machine Available is <strong style="color: green;"><?= $mesin; ?> Machine </strong>
                            </h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h6>
                                Judul : <?= $judul; ?>
                            </h6>
                        </div>
                        <div class="col-auto">
                            <a href="<?= base_url($role . '/kalenderMesin/' . $id_pln_mc) ?>" class="btn btn-success"> Jadwal Mesin <i class="fas fa-calendar-plus text-lg opacity-10" aria-hidden="true"></i> </a>
                            <button id="fetch-data-button" class="btn btn-info">Fetch Data</button>
                            <a href="<?= base_url($role . '/detailplanstop/' . $id_pln_mc) ?>" class="btn btn-warning">PDK Stop</a>
                            <button class="btn bg-danger text-white deleteAll-btn "
                                data-id="<?= $id_pln_mc ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteAll">
                                <i class="fas fa-trash"></i>
                                Hapus Semua
                            </button>
                            <a href="<?= base_url($role . '/planningmesin') ?>" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable" class="display compact striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Model</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery Awal</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Remaining Qty</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Planned</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Target 100%</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Start</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Stop</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Plan Machine</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Actual Running</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Days</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($detailplan as $order): ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['model']; ?></td>
                                            <td class="text-sm" data-order="<?= $order['delivery_raw'] ?>"> <?= $order['delivery'] ?></td>
                                            <td class="text-sm">
                                                <?= ($jarum === '240N')
                                                    ? number_format($order['qty'] * 2, 0, '.', ',')
                                                    : number_format($order['qty'], 0, '.', ','); ?> Dz
                                            </td>
                                            <td class="text-sm">
                                                <?= ($jarum === '240N')
                                                    ? number_format($order['sisa'] * 2, 0, '.', ',')
                                                    : number_format($order['sisa'], 0, '.', ','); ?> Dz
                                            </td>


                                            <td class="text-sm"><?= number_format($order['est_qty'], 0, '.', ','); ?> Dz</td>
                                            <td class="text-sm"><?= number_format(3600 / $order['smv'], 2, '.', ','); ?> Dz/Days</td>
                                            <td class="text-sm">
                                                <?= !empty($order['start_date']) ? date('d-M-Y', strtotime($order['start_date'])) : 'No Start Date'; ?>
                                            </td>
                                            <td class="text-sm">
                                                <?= !empty($order['stop_date']) ? date('d-M-Y', strtotime($order['stop_date'])) : 'No Stop Date'; ?>
                                            </td>
                                            <td class="text-sm">
                                                <?= htmlspecialchars($order['mesin']) ? htmlspecialchars($order['mesin']) . ' Mc' : ''; ?>
                                            </td>
                                            <td class="text-sm">
                                                <?= htmlspecialchars($order['actualMc']) ? htmlspecialchars($order['actualMc']) . ' Mc' : '0'; ?>
                                            </td>
                                            <td class="text-sm"><?= htmlspecialchars($order['hari']); ?> Days</td>
                                            <td class="text-sm">
                                                <?php if ($order['est_qty'] < $order['sisa']) : ?>
                                                    <a href="<?= base_url($role . '/planningpage/' . $order['id_detail_pln']) . '/' . $id_pln_mc ?>" class="badge bg-info mb-2"><i class="fas fa-eye"></i> </a>
                                                <?php else : ?>
                                                    <a href="<?= base_url($role . '/planningpage/' . $order['id_detail_pln']) . '/' . $id_pln_mc ?>" class="badge bg-success mb-2"><i class="fas fa-eye"></i> </a>
                                                <?php endif; ?>
                                                <span class="badge btn-sm text-white bg-secondary stop-btn  mr-2"
                                                    data-id="<?= $order['id_detail_pln']; ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmStopModal">
                                                    <i class="fas fa-ban"> </i>
                                                </span>
                                                <span class="badge bg-warning text-dark move-btn mr-2 mb-2"
                                                    data-id="<?= $order['id_detail_pln']; ?>"
                                                    data-area="<?= $area ?>"
                                                    data-pdk="<?= $order['model'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#moveJarum">
                                                    <i class="fas fa-sign-out-alt"></i>

                                                </span>
                                                <span class="badge bg-danger text-white delete-btn "
                                                    data-id="<?= $order['id_detail_pln']; ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmDelete">
                                                    <i class="fas fa-trash"></i>

                                                </span>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end text-sm fw-bold">TOTAL:</th>
                                        <th class="text-sm fw-bold" id="totalQty">0 Dz</th>
                                        <th class="text-sm fw-bold" id="totalSisa">0 Dz</th>
                                        <th class="text-sm fw-bold" id="totalEstQty">0 Dz</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-sm fw-bold" id="totalMesin">0 Mc</th>
                                        <th class="text-sm fw-bold" id="totalActualMc">0 Mc</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal fade" id="confirmStopModal" tabindex="-1" aria-labelledby="confirmStopModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmStopModalLabel">Confirm Stop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to stop this plan?</p>
                        <input type="hidden" id="stopPlanId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmStopButton" class="btn btn-danger">Yes, Stop</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmDelete" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmStopModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to Delete this plan?</p>
                        <form action="<?= base_url($role . '/deletePlanPdk') ?>" method="post">
                            <input type="hidden" id="idDelete" name="id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmDeleteAll" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmStopModalLabel">Confirm Delete All</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin menghapus semua planningan</p>
                        <form action="<?= base_url($role . '/deletePlanAll') ?>" method="post">
                            <input type="hidden" id="" name="id" value="<?= $id_pln_mc ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="moveJarum" tabindex="-1" aria-labelledby="moveJarum" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="moveJarumText">Pindah Jarum</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <input type="hidden" name="id_detail" id="id_detail">
                            <input type="hidden" name="pdk" id="pdk">
                            <input type="hidden" name="jarumOld" id="jarumOld">

                            <div class="form-group">
                                <label for="jarumname" class="form-control-label">Pilih Jarum</label>
                                <select name="jarumname" id="jarumname" class="form-control">
                                    <option value="">----</option>
                                    <?php foreach ($jarumList as $jrm): ?>
                                        <option value="<?= $jrm['id_pln_mc'] ?>" data-jarum="<?= $jrm['jarum'] ?>">
                                            <?= $jrm['jarum'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <input type="hidden" name="jarum" id="jarum-hidden">
                            </div>

                            <!-- Tempat checkbox inject -->
                            <div class="form-group mt-3" id="checkboxContainer">
                                <!-- Checkbox dari data.data akan muncul di sini -->
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-info">Pindah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.getElementById('jarumname').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const jarumValue = selectedOption.getAttribute('data-jarum');
                document.getElementById('jarum-hidden').value = jarumValue;
            });
        </script>

        <script>
            $(document).ready(function() {
                const table = $('#dataTable').DataTable({
                    order: [
                        [1, 'asc']
                    ],
                    lengthMenu: [
                        [100, -1],
                        [100, "All"]
                    ],
                    footerCallback: function(row, data, start, end, display) {
                        let totalQty = 0,
                            totalSisa = 0,
                            totalEstQty = 0,
                            totalMesin = 0,
                            totalActualMc = 0;

                        data.forEach(rowData => {
                            // Ambil dan parsing data dari kolom
                            const qty = parseFloat(rowData[2].replace(/[^\d.-]/g, '')) || 0;
                            const sisa = parseFloat(rowData[3].replace(/[^\d.-]/g, '')) || 0;
                            const est = parseFloat(rowData[4].replace(/[^\d.-]/g, '')) || 0;
                            const mesin = parseFloat(rowData[8].replace(/[^\d.-]/g, '')) || 0;
                            const actual = parseFloat(rowData[9].replace(/[^\d.-]/g, '')) || 0;

                            totalQty += qty;
                            totalSisa += sisa;
                            totalEstQty += est;
                            totalMesin += mesin;
                            totalActualMc += actual;
                        });

                        // Tampilkan ke footer
                        $('#totalQty').html(totalQty.toLocaleString() + ' Dz');
                        $('#totalSisa').html(totalSisa.toLocaleString() + ' Dz');
                        $('#totalEstQty').html(totalEstQty.toLocaleString() + ' Dz');
                        $('#totalMesin').html(totalMesin + ' Mc');
                        $('#totalActualMc').html(totalActualMc + ' Mc');
                    }
                });

                $('#fetch-data-button').click(function() {
                    var area = '<?= $area; ?>';
                    var jarum = '<?= $jarum; ?>';
                    var id_pln_mc = '<?= $id_pln_mc ?>'

                    $.ajax({
                        url: '<?= base_url($role . '/fetchdetailorderarea'); ?>',
                        type: 'GET',
                        data: {
                            area: area,
                            jarum: jarum,
                            id_pln_mc: id_pln_mc
                        },
                        success: function(response) {
                            // Handle success response\
                            console.log(response)
                            Swal.fire({
                                title: 'Success!',
                                text: 'Data inserted successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error inserting data: ' + error,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });


            });
            $(document).on('click', '.move-btn', function() {

                var id = $(this).data('id');
                var pdk = $(this).data('pdk');
                var jarumOld = <?= json_encode($jarum) ?>;
                var area = $(this).data('area');
                var idpage = <?= json_encode($id_pln_mc) ?>;
                $.ajax({
                    url: '<?= base_url('api/getPlanStyle'); ?>',
                    type: 'GET',
                    data: {
                        id: id,
                        area: area,
                        jarum: jarumOld,
                        pdk: pdk,
                        idpage: idpage
                    },
                    success: function(response) {
                        console.log(response);
                        PindahJarumModal(response, id, idpage, pdk, jarumOld); // kirim id & idpage ke fungsi modal
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });



            document.addEventListener('DOMContentLoaded', function() {
                const stopButtons = document.querySelectorAll('.stop-btn');
                const confirmStopButton = document.getElementById('confirmStopButton');
                const stopPlanIdInput = document.getElementById('stopPlanId');

                stopButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const planId = this.getAttribute('data-id');
                        stopPlanIdInput.value = planId; // Set ID ke input hidden
                    });
                });

                confirmStopButton.addEventListener('click', function() {
                    const planId = stopPlanIdInput.value;

                    // Lakukan aksi stop (misalnya, AJAX call atau redirect)
                    fetch(`<?= base_url($role . '/stopPlanning'); ?>/${planId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                '<?= csrf_token(); ?>': '<?= csrf_hash(); ?>'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Plan stopped successfully.');
                                location.reload(); // Refresh halaman
                            } else {
                                alert('Failed to stop plan.');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
                const deleteBtn = document.querySelectorAll('.delete-btn');
                const idInput = document.getElementById('idDelete');

                deleteBtn.forEach(button => {
                    button.addEventListener('click', function() {
                        const planId = this.getAttribute('data-id');
                        idInput.value = planId; // Set ID ke input hidden
                    });
                });



            });

            function PindahJarumModal(data, id, idpage, pdk, jarum) {
                // Set hidden input
                $('#moveJarum').find('input[name="id_detail"]').val(id);
                $('#moveJarum').find('input[name="pdk"]').val(pdk);
                $('#moveJarum').find('input[name="jarumOld"]').val(jarum);
                $('#moveJarum').find('#moveJarumText').text('Pindah Jarum ' + pdk);

                // Kosongkan container checkbox
                const container = $('#checkboxContainer');
                container.html('');

                // Cek apakah data valid array
                if (Array.isArray(data.data)) {
                    data.data.forEach(function(item) {
                        const checkbox = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           name="pilih_size[]" value="${item.style}"
                           id="check${item.style}">
                    <label class="form-check-label" for="check${item.idAps}">
                        ${item.inisial} - ${item.style} (Sisa: ${item.sisa})
                    </label>
                </div>`;
                        container.append(checkbox);
                    });
                }

                // Show modal dan set action URL
                $('#moveJarum').modal('show');
                $('#moveJarum').find('form').attr('action', '<?= base_url($role . '/pindahjarum/') ?>' + idpage);
            }
        </script>

        <?php $this->endSection(); ?>