<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
<?php $this->extend('Planning/layout'); ?>
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
                            List Data Planning by Needle, Title <?= $judul; ?>
                        </h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable" class="display compact striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Planning Date Creation</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Machine Requirement</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Start</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Stop</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Days</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Order Status</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Machine Plan</th>
                                        <th class="text-uppercase text-center text-dark text-xxs font-weight-bolder opacity-7 ps-2">Choose Machine</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= date('d-M-y', strtotime($order['created_at'])); ?></td>
                                            <td class="text-sm"><?= $order['jarum']; ?></td>
                                            <td class="text-sm"><?= $order['mesin']; ?> Mc</td>
                                            <td class="text-sm"><?= date('d-M-y', strtotime($order['tanggal_awal'])); ?></td>
                                            <td class="text-sm"><?= date('d-M-y', strtotime($order['tanggal_akhir'])); ?></td>
                                            <td class="text-sm"><?= $order['jumlah_hari']; ?> Days</td>
                                            <td class="text-sm"><?= $order['deskripsi']; ?></td>
                                            <td class="text-sm"><?= $order['mc_nyala'] !== null ? $order['mc_nyala'] . ' Mc' : 'Mc not choosen yet'; ?></td>
                                            <td class="text-sm">
                                                <?php if ($order['mc_nyala'] === null): ?>
                                                    <form method="POST" action="<?= base_url('planning/pickmachine/'.$order['id'].'/'.$order['jarum']); ?>">
                                                        <input type="hidden" name="mesin" value="<?= $order['mesin']; ?>">
                                                        <input type="hidden" name="deskripsi" value="<?= $order['deskripsi']; ?>">
                                                        <button type="submit" class="btn btn-info btn-sm">
                                                            Pick Machine
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="<?= base_url('planning/viewdetail/'.$order['id']); ?>">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            View Machine Details
                                                        </button>
                                                    </form>
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
        </div>       


        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable();
            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>