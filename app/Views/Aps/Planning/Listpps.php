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
                                List PPS
                            </h5>
                        </div>
                        <div class="col-2">
                            <a href="<?= base_url($role . '/planningmesin') ?>" class="btn btn-info w-100">Back</a>
                        </div>
                    </div>

                </div>

                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable" class="display compact striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Model</th>
                                        <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Start PPS</th>
                                        <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Stop PPS</th>
                                        <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">PPS Progress %</th>
                                        <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Material Progress %</th>

                                        <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pdk as $pps) :
                                        // tentuin warna badge progress
                                        $progress = round($pps['progress'] * 100); // pastikan persen 0-100
                                        if ($progress <= 25) {
                                            $badgeClass = 'bg-danger';
                                        } elseif ($progress <= 50) {
                                            $badgeClass = 'bg-warning';
                                        } elseif ($progress <= 75) {
                                            $badgeClass = 'bg-primary';
                                        } else {
                                            $badgeClass = 'bg-success';
                                        }

                                        // warna badge material juga bisa sama logikanya
                                        $material = round($pps['material']);
                                        if ($material <= 25) {
                                            $badgeMaterial = 'bg-danger';
                                        } elseif ($material <= 50) {
                                            $badgeMaterial = 'bg-warning';
                                        } elseif ($material <= 75) {
                                            $badgeMaterial = 'bg-primary';
                                        } else {
                                            $badgeMaterial = 'bg-success';
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $pps['pdk']; ?></td>
                                            <td class="text-center"><?= $pps['start']; ?></td>
                                            <td class="text-center"><?= $pps['stop']; ?></td>
                                            <td class="text-center">
                                                <span class="badge <?= $badgeClass ?>"><?= $progress; ?>%</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $badgeMaterial ?>"><?= $material; ?>%</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url($role . '/ppsDetail/' . $pps['pdk']) ?>" class="btn btn-primary">Detail</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal fade" id="confirmActiveModal" tabindex="-1" aria-labelledby="confirmActiveModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmActiveModalLabel">Confirm Active</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to active this plan?</p>
                        <input type="hidden" id="stopPlanId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmActiveButton" class="btn btn-danger">Yes, Active</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "pageLength": 35,
                    "order": []
                });


            });
        </script>

        <?php $this->endSection(); ?>