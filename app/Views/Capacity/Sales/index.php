<?php $this->extend('Capacity/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Sales Position
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">A</p>
                                    <!-- <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Machine : //$jr['total'] </p> -->
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">B</p>
                                    <!-- <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Machine : //$jr['total'] </p> -->
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">C</p>
                                    <!-- <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Machine : //$jr['total'] </p> -->
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">D</p>
                                    <!-- <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Machine : //$jr['total'] </p> -->
                                    <h5 class="font-weight-bolder mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
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
                                                <td class="text-xs" style="text-align: center;">0</td>
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
            <div class="row my-4">
                <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="card">
                                    <div class="card-body">
                                        <?php foreach ($weeklyRanges as $month => $data) : ?>
                                            <div class="col-12 border mb-4">
                                                <div class="row d-flex justify-content-between">
                                                    <div class="col-lg-12">
                                                        <div class="col-9">
                                                            <div class="numbers">
                                                                <h5 class="font-weight-bolder mb-0"><?= $month . ' - ' . date('Y') ?></h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <?php foreach ($data['holiday'] as $index => $range) : ?>
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
                                                                    <h6>: <?= ceil($data['monthlySummary']['totalCapacity']); ?> dz</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-5">
                                                                    <h6>Available</h6>
                                                                </div>
                                                                <div class="col-1">
                                                                    <h6>: <?= ceil($data['monthlySummary']['totalAvailable']); ?> dz</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-5">
                                                                    <h6>Total Machine</h6>
                                                                </div>
                                                                <div class="col-1">
                                                                    <h6>: 0</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-5">
                                                                    <h6>Order</h6>
                                                                </div>
                                                                <div class="col-1">
                                                                    <h6>: <?= ceil($data['monthlySummary']['totalOrder']); ?> dz</h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <h6>Total Max Capacity</h6>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <h6>: <?= ceil($data['monthlySummary']['totalCapacity']); ?> dz</h6>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <h6>Total Available</h6>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <h6>: <?= ceil($data['monthlySummary']['totalAvailable']); ?> dz</h6>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <h6>Total Order</h6>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <h6>: <?= ceil($data['monthlySummary']['totalOrder']); ?> dz</h6>
                                                            </div>
                                                        </div>
                                                        <?php foreach ($data['weeks'] as $week) : ?>
                                                            <div class="col-6 border mb-4">
                                                                <div class="row d-flex justify-content-between">
                                                                    <div class="col-lg-7">
                                                                        <h4 class="text-header">Week <?= $week['start_date'] ?> - <?= $week['end_date'] ?></h4>
                                                                        <h6 class="text-header"> <span class="badge bg-success"> Hari Kerja: <?= $week['number_of_days'] ?> </span></h6>
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
                                                                                    <?= ceil($week['maxCapacity']); ?> dz
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
                                                                                    <?= ceil(floatval($week['available'])); ?> dz
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
                                                                                    <?= ceil(floatval($week['sisaBooking'])); ?> dz
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
                                                                                <h6><?= ceil(floatval($week['confirmOrder'])); ?> dz</h6>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
</div>

<!-- Skrip JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>

<?php $this->endSection(); ?>