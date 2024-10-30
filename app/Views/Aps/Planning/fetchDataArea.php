<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
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
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Remaining Qty</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Planned</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Target 100%</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Start</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Stop</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Machine</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Days</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Fill Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detailplan as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= htmlspecialchars($order['model']); ?></td>
                                            <td class="text-sm"><?= date('d-M-Y', strtotime($order['delivery'])); ?></td>
                                            <td class="text-sm"><?= number_format($order['qty'], 0, '.', ','); ?> Dz</td>
                                            <td class="text-sm"><?= number_format($order['sisa'], 0, '.', ','); ?> Dz</td>
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
                                            <td class="text-sm"><?= htmlspecialchars($order['hari']); ?> Days</td>
                                            <td class="text-sm">
                                                <?php if ($order['est_qty'] < $order['sisa']) : ?>
                                                    <a href="<?= base_url($role . '/planningpage/' . $order['id_detail_pln']) . '/' . $id_pln_mc ?>" class="btn btn-primary">Detail</a>

                                                <?php else : ?>
                                                    <a href="<?= base_url($role . '/planningpage/' . $order['id_detail_pln']) . '/' . $id_pln_mc ?>" class="btn btn-secondary">Detail</a>

                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php if (empty($detailplan)) : ?>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <p>No data available in the table.</p>
                                <button id="fetch-data-button" class="btn btn-info">Fetch Data</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "pageLength": 35,
                    "order": []
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
        </script>

        <?php $this->endSection(); ?>