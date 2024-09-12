<?php $this->extend($role . '/layout'); ?>
<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
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
                            <form action="<?= base_url($role . '/sales/position') ?>" method="POST">
                                <select name=" aliasjarum" class="form-control">
                                    <option value="">Pilih Jarum</option>
                                    <?php if (!empty($dataJarum)) : ?>
                                        <?php foreach ($dataJarum as $jrm) : ?>
                                            <option value="<?= $jrm['aliasjarum'] ?>" name="aliasjarum"><?= $jrm['aliasjarum'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                        </div>
                        <div class="col-4">
                            <button type=" submit" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md">OK</button>
                            </form>
                            <a href="<?= base_url($role . '/generatesales') ?>" class="btn btn-sm bg-gradient-success shadow text-center border-radius-md">Generate Excel</a>
                            <?php if (!empty($aliasjarum)) : ?>
                                <form action="<?= base_url($role . '/exportsales') ?>" method="post">
                                    <input type="text" value="<?= $aliasjarum ?>" name="aliasjarum" hidden>
                                    <button type="submit" class="btn btn-sm bg-gradient-success shadow text-center border-radius-md">Export Excel</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($aliasjarum)) : ?>
        <div class="card-body p-3">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <h4 class="text-center"><span><?= $aliasjarum ?></span></h4>
                            <table id="example" class="table table-border" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;">Machine</th>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;">Jumlah</th>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;">Running</th>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;">Stock Cylinder</th>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;">CJ</th>
                                        <!-- <th class="text-xs font-weight-bolder" style="text-align: center;">MJ</th> -->
                                        <th class="text-xs font-weight-bolder" style="text-align: center;">Prod 28days</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DAKONG -->
                                    <tr>
                                        <td class="text-xs" style="text-align: center;">DAKONG</td>
                                        <?php if (!empty($dakong) && is_array($dakong)) : ?>
                                            <td class="text-xs" style="text-align: center;"><?= $dakong['totalMc'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;"><?= $dakong['totalRunning'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;">0</td>
                                            <td class="text-xs" style="text-align: center;"><?= $dakong['totalCj'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;"><?= ceil($dakong['totalProd'] ?? 0) ?></td>
                                        <?php endif; ?>
                                    </tr>

                                    <!-- ROSSO -->
                                    <tr>
                                        <td class="text-xs" style="text-align: center;">ROSSO</td>
                                        <?php if (!empty($rosso) && is_array($rosso)) : ?>
                                            <td class="text-xs" style="text-align: center;"><?= $rosso['totalMc'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;"><?= $rosso['totalRunning'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;">0</td>
                                            <td class="text-xs" style="text-align: center;"><?= $rosso['totalCj'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;"><?= ceil($rosso['totalProd'] ?? 0) ?></td>
                                        <?php endif; ?>
                                    </tr>

                                    <!-- THS -->
                                    <tr>
                                        <td class="text-xs" style="text-align: center;">THS</td>
                                        <?php if (!empty($ths) && is_array($ths)) : ?>
                                            <td class="text-xs" style="text-align: center;"><?= $ths['totalMc'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;"><?= $ths['totalRunning'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;">0</td>
                                            <td class="text-xs" style="text-align: center;"><?= $ths['totalCj'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;"><?= ceil($ths['totalProd'] ?? 0) ?></td>
                                        <?php endif; ?>
                                    </tr>

                                    <!-- LONATI -->
                                    <tr>
                                        <td class="text-xs" style="text-align: center;">LONATI</td>
                                        <?php if (!empty($lonati) && is_array($lonati)) : ?>
                                            <td class="text-xs" style="text-align: center;"><?= $lonati['totalMc'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;"><?= $lonati['totalRunning'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;">0</td>
                                            <td class="text-xs" style="text-align: center;"><?= $lonati['totalCj'] ?? 0 ?></td>
                                            <td class="text-xs" style="text-align: center;"><?= ceil($lonati['totalProd'] ?? 0) ?></td>
                                        <?php endif; ?>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;">Total</th>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;"><?= $ttlMc['totalMc'] ?? 0 ?></th>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;"><?= $ttlMc['totalRunning'] ?? 0 ?></th>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;">0</th>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;"><?= $ttlMc['totalCj'] ?? 0 ?></th>
                                        <th class="text-xs font-weight-bolder" style="text-align: center;"><?= ceil($ttlMc['totalProd'] ?? 0) ?></th>
                                    </tr>
                                </tfoot>
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
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Confirm Order</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Order</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Sisa Booking</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">(+) Exess</th>
                                                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">%</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <?php foreach ($data['weeks'] as $week) : ?>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['start_date'] ?> - <?= $week['end_date'] ?> (<?= $week['countWeek'] ?>)</td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['number_of_days'] ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['maxCapacity']; ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['ConfirmOrder']; ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['sisaConfirmOrder']; ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['sisaBooking']; ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['exess']; ?></td>
                                                                                <td class="text-sm" style="text-align: center;"> <?= $week['exessPercentage']; ?>%</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    <?php endforeach; ?>
                                                                    <footer>
                                                                        <tr>
                                                                            <th class="text-sm" style="text-align: center;" colspan="2">TOTAL</th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalMaxCapacity']); ?></th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalConfirmOrder']); ?></th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalSisaConfirmOrder']); ?></th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalSisaBooking']); ?></th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalExess']); ?></th>
                                                                            <th class="text-sm" style="text-align: center;"> <?= ceil($data['monthlySummary']['totalExessPercentage']); ?>%</th>
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