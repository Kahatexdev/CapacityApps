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
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Analytical Dashboard
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Booking </p>
                                <h5 class="font-weight-bolder mb-0">
                                    <span class=" text-sm font-weight-bolder">This Month</span>
                                    <?= $TerimaBooking ?>

                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-book text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Order </p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $jalan ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-tasks text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Machine Running</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $mcJalan ?>
                                    <span class=" text-sm font-weight-bolder">/ <?= $totalMc ?> </span>

                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-settings text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Order Finished</p>
                                <h5 class="font-weight-bolder mb-0">
                                    8
                                    <span class=" text-sm font-weight-bolder">This Month</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <form action="<?= base_url($role . '/sales') ?>" method="get">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-9">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">
                                        Machine Availablelity
                                    </h5>
                                </div>
                            </div>
                            <div class="col-2">
                                <select name="jarum" class="form-control">
                                    <option value="">Pilih Jarum</option>
                                    <?php if (!empty($dataJarum)) : ?>
                                        <?php foreach ($dataJarum as $jarum) : ?>
                                            <option value="<?= $jarum ?>"><?= $jarum ?></option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="">Tidak ada data</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-1">
                                <button type="submit" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php if (!empty($_GET['jarum'])) : ?>
        <div class="card-body p-3">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <h4 class="text-center"><span><?= $_GET['jarum'] ?></span></h4>
                            <table id="example" class="table table-border" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Machine</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Jumlah</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Running</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Stock Cylinder</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">CJ</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">MJ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dataMesin)) : ?>
                                        <?php
                                        $total_mc = 0;
                                        $total_running = 0;
                                        $total_cj = 0;
                                        $total_mj = 0;
                                        foreach ($dataMesin as $mc) :
                                            // Hitung total untuk setiap kolom
                                            $total_mc += $mc['total_mc'];
                                            $total_running += $mc['mesin_jalan'];
                                            $total_cj += $mc['cj'];
                                            $total_mj += $mc['mj'];
                                        ?>
                                            <tr>
                                                <td class="text-xs" style="text-align: center;"><?= $mc['brand'] ?></td>
                                                <td class="text-xs" style="text-align: center;"><?= $mc['total_mc'] ?></td>
                                                <td class="text-xs" style="text-align: center;"><?= $mc['mesin_jalan'] ?></td>
                                                <td class="text-xs" style="text-align: center;"></td>
                                                <td class="text-xs" style="text-align: center;"><?= $mc['cj'] ?></td>
                                                <td class="text-xs" style="text-align: center;"><?= $mc['mj'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td class="text-xs" style="text-align: center; font-weight: bold;">Total</td>
                                            <td class="text-xs" style="text-align: center; font-weight: bold;"><?= $total_mc ?></td>
                                            <td class="text-xs" style="text-align: center; font-weight: bold;"><?= $total_running ?></td>
                                            <td class="text-xs" style="text-align: center;"></td>
                                            <td class="text-xs" style="text-align: center; font-weight: bold;"><?= $total_cj ?></td>
                                            <td class="text-xs" style="text-align: center; font-weight: bold;"><?= $total_mj ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($weeklyRanges)) : ?>
            <?php foreach ($weeklyRanges as $month => $ranges) : ?>
                <div class="row my-4">
                    <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="numbers">
                                            <h5 class="font-weight-bolder mb-0"><?= $month . ' - ' . date('Y') ?></h5>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <?php if (isset($range['holidays']) && !empty($range['holidays'])) : ?>
                                                <div class="row">
                                                    <ul class="">
                                                        <?php foreach ($range['holidays'] as $holiday) : ?>
                                                            <li class="text-danger"><span class="badge bg-danger">
                                                                    <?= $holiday['nama'] ?> (<?= $holiday['tanggal'] ?>)
                                                                </span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <div class="row">
                                            <div class="col-5">
                                                <h6>Max Capacity</h6>
                                            </div>
                                            <div class="col-7">
                                                <h6>:
                                                    <?php foreach ($ranges as $index => $range) : ?>
                                                        <?= number_format($range['totalMonthlyCapacity'], 2) ?>
                                                    <?php endforeach; ?>
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">
                                                <h6>Available</h6>
                                            </div>
                                            <div class="col-1">
                                                <h6>:</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">
                                                <h6>Total Machine</h6>
                                            </div>
                                            <div class="col-1">
                                                <h6>:</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-5">
                                                <h6>Order</h6>
                                            </div>
                                            <div class="col-1">
                                                <h6>:</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-6">
                                <div class="row">
                                    <?php foreach ($ranges as $range) : ?>
                                        <div class="col-6 border mb-4"> <!-- Tambahkan mb-4 untuk memberikan margin bawah -->
                                            <div class="row d-flex justify-content-between">
                                                <div class="col-lg-7">
                                                    <h4 class="text-header">Week <?= $range['week'] ?></h4>
                                                    <h6 class="text-header"> <span><?= $range['start_date'] ?> - <?= $range['end_date'] ?></span></h6>
                                                    <h6 class="text-header"> <span class="badge bg-success"> Hari Kerja : <?= $range['number_of_days'] ?> </span></h6>
                                                </div>
                                                <div class="col-lg-5">
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <h6>Max</h6>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <h6>:</h6>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <h6>
                                                                <?= ceil($range['maxCapacity']); ?> dz
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <h6>Available</h6>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <h6>:</h6>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <h6>

                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <h6>Used</h6>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <h6>:</h6>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <h6>
                                                                <?= ceil(floatval($range['dataSisaBooking'])); ?> dz
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <h6>Confirm</h6>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <h6>:</h6>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <h6><!-- Tambahkan nilai Confirm di sini --></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Skrip JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>




<?php $this->endSection(); ?>