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

    <div class="row my-4">
        <form action="<?= base_url($role . '/sales') ?>" method="get">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-5">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">
                                        Sales Position
                                    </h5>
                                </div>
                            </div>
                            <div class="col-3 d-flex flex-column align-items-end">
                                <select name=" aliasjarum" class="form-control">
                                    <option value="">Pilih Jarum</option>
                                    <?php if (!empty($dataJarum)) : ?>
                                        <?php foreach ($dataJarum as $jrm) : ?>
                                            <option value="<?= $jrm['aliasjarum'] ?>"><?= $jrm['aliasjarum'] ?></option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="">Tidak ada data</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <button type=" submit" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md">OK</button>
                                <a href=" #" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md">Generate Excel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if (!empty($_GET['aliasjarum'])) : ?>
        <div class="card-body p-3">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <h4 class="text-center"><span><?= $_GET['aliasjarum'] ?></span></h4>
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
                                            <td class="text-xs" style="text-align: center; font-weight: bold;">0</td>
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
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="numbers d-flex justify-content-center">
                                                                    <h5 class="font-weight-bolder mb-0"><?= $month ?></h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-8 d-flex flex-column align-items-end">
                                                                <?php foreach ($data['weeks'] as $week) : ?>
                                                                    <?php if (isset($week['holidays']) && !empty($week['holidays'])) : ?>
                                                                        <div class="row">
                                                                            <ul class="">
                                                                                <?php foreach ($week['holidays'] as $holiday) : ?>
                                                                                    <li class="text-danger">
                                                                                        <span class="badge bg-danger">
                                                                                            <?= $holiday['nama'] ?> (<?= $holiday['tanggal'] ?>)
                                                                                        </span>
                                                                                    </li>
                                                                                <?php endforeach; ?>
                                                                            </ul>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <table id="example" class="table table-border" style="width:100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Week</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Hari Kerja</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Max Capacity</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Available Capacity</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Confirm Order</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Order</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Booking</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <?php foreach ($data['weeks'] as $week) : ?>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['start_date'] ?> - <?= $week['end_date'] ?> (<?= $week['countWeek'] ?>)</td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['number_of_days'] ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= ceil($week['maxCapacity']); ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= ceil($week['available']); ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= ceil($week['ConfirmOrder']); ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= ceil($week['sisaConfirmOrder']); ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= ceil($week['sisaBooking']); ?></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    <?php endforeach; ?>
                                                                    <footer>
                                                                        <tr>
                                                                            <th class="text-sm" style="text-align: center;" colspan="2">TOTAL</th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalMaxCapacity']); ?></th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalAvailable']); ?></th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalConfirmOrder']); ?></th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalSisaConfirmOrder']); ?></th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalSisaBooking']); ?></th>
                                                                        </tr>
                                                                    </footer>
                                                                </table>
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
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Skrip JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?php $this->endSection(); ?>