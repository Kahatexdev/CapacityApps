<?php $this->extend('Capacity/Calendar/index'); ?>
<?php $this->section('generate'); ?>

<div class="row mt-5">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">

                <h4>Capacity Mesin dari sampai</h4>
                <a href="" class="btn bg-gradient-success">Export Excel</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table w-25 align-left">
                <tr>
                    <td>
                        Jumlah Hari
                    </td>
                    <td>
                        : Hari
                    </td>
                </tr>
                <tr>
                    <td>
                        Jumlah Libur
                    </td>
                    <td>
                        : Hari
                    </td>
                </tr>
                <tr>
                    <td>
                        Total Kebutuhan Mesin
                    </td>
                    <td>
                        : 81927
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php foreach ($weeklyRanges as $month => $ranges) : ?>
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h3><?= $month ?></h3>
                        <div class="holiday">
                            <div class="week-holiday d-flex justify-content-between">
                                <?php foreach ($ranges as $index => $range) : ?>
                                    <?php if (isset($range['holidays']) && !empty($range['holidays'])) : ?>
                                        <div class="week ">
                                            <h5>Week <?= $index + 1 ?> :</h5>
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
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th rowspan="4" class="text-center">Product Style</th>
                                    <?php foreach ($ranges as $index => $range) : ?>
                                        <th class="text-center">Week <?= $range['week'] ?></th>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($ranges as $index => $range) : ?>
                                        <th class="text-xs"> <span><?= $range['start_date'] ?> - <?= $range['end_date'] ?> </span></th>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($ranges as $index => $range) : ?>
                                        <th class="text-xs "> <span class="badge bg-success"> Hari Kerja : <?= $range['number_of_days'] ?> </span></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>JC108</td>
                                    <?php foreach ($ranges as $index => $range) : ?>
                                        <td><?= $range['normal'] ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <td>JC120</td>
                                    <?php foreach ($ranges as $index => $range) : ?>
                                        <td><?= $range['sneaker'] ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <td>JC144</td>
                                    <?php foreach ($ranges as $index => $range) : ?>
                                        <td><?= $range['footies'] ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <td>JC168</td>
                                    <?php foreach ($ranges as $index => $range) : ?>
                                        <td><?= $range['knee'] ?></td>
                                    <?php endforeach; ?>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php $this->endSection(); ?>