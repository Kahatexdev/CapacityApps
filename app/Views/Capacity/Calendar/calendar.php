<?php $this->extend('Capacity/layout'); ?>
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

    <div class="row mb-2 mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="font-weight-bolder mb-0">
                                <a href="" # class="btn bg-gradient-info">

                                    <i class="fas fa-calendar-alt text-lg opacity-10" aria-hidden="true"></i>
                                </a>
                                Capacity Calendar
                            </h3>
                        </div>
                        <div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <!-- kalender -->
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
                                        <td>Normal Sock</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['normal'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>Sneakers</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['sneaker'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>Footies</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['footies'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>Knee High</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['knee'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td>Tight</td>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['tight'] ?></td>
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
</div>


</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({});

        // Trigger import modal when import button is clicked
        $('.import-btn').click(function() {
            console.log("a");
            var idModel = $(this).data('id');
            var noModel = $(this).data('no-model');

            $('#importModal').find('input[name="id_model"]').val(idModel);
            $('#importModal').find('input[name="no_model"]').val(noModel);

            $('#importModal').modal('show'); // Show the modal
        });
    });
</script>

<?php $this->endSection(); ?>