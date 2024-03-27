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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Booking Masuk </p>
                                <h5 class="font-weight-bolder mb-0">
                                    <span class=" text-sm font-weight-bolder">Bulan Ini</span>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Order aktif </p>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Mesin Jalan</p>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Order Selesai</p>
                                <h5 class="font-weight-bolder mb-0">
                                    8
                                    <span class=" text-sm font-weight-bolder">Bulan Ini</span>
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
    <div class="row my-3">
        <div class="col-lg-12">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>Statistik Data Turun Order Perhari</h6>

                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="mixed-chart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2 mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="font-weight-bolder mb-0">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                                Capasity Calendar
                            </h3>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">

                                <button class="btn bg-gradient-warning mr-2" data-bs-toggle="modal" data-bs-target="#lihatLibur">
                                    Lihat Data Libur
                                </button>
                                <div> &nbsp;</div>
                                <button class="btn bg-gradient-success ml-2" data-bs-toggle="modal" data-bs-target="#addLibur">
                                    Tambah Libur
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade  bd-example-modal-lg" id="addLibur" tabindex="-1" role="dialog" aria-labelledby="tambahLibur" aria-hidden="true">
        <div class="modal-dialog  modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Data Libur</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('capacity/inputLibur') ?>" method="post">
                        <div class="form-group">
                            <label for="tgl-bk-form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tgl_libur">
                        </div>
                        <div class="form-group">
                            <label for="No Model" class="col-form-label">Nama</label>
                            <input type="text" name="nama" class="form-control">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-info">Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade  bd-example-modal-lg" id="lihatLibur" tabindex="-1" role="dialog" aria-labelledby="lihatLibur" aria-hidden="true">
        <div class="modal-dialog  modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Data Libur</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="example" class="display compact " style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        Tanggal
                                    </th>
                                    <th>
                                        Nama
                                    </th>
                                    <th>
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($DaftarLibur as $libur) : ?>
                                    <tr>
                                        <td><?= $libur['tanggal'] ?></td>
                                        <td><?= $libur['nama'] ?></td>
                                        <td><a href="<?= base_url('capacity/hapusLibur/' . $libur['id']) ?>" class="btn btn-danger"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-gradient-info">Simpan</button>
                </div>
                </form>
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

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Week </th>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td>Week <?= $index + 1 ?></td>

                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th>Tanggal </th>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><small><?= $range['start_date'] ?> - <?= $range['end_date'] ?> </small></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Hari</th>
                                        <?php foreach ($ranges as $index => $range) : ?>
                                            <td><?= $range['number_of_days'] ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>

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
<script>
    let data = <?php echo json_encode($order); ?>;
    console.log(data)
    // Ekstraksi tanggal dan jumlah produksi dari data
    let labels = data.map(item => item.created_at);
    let values = data.map(item => item.total_produksi);


    var ctx2 = document.getElementById("mixed-chart").getContext("2d");

    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

    var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
    gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

    new Chart(ctx2, {

        data: {
            labels: labels,
            datasets: [{
                    type: "bar",
                    label: "Data Turun Order",
                    borderWidth: 0,
                    pointRadius: 30,

                    backgroundColor: "#3A416F",
                    fill: true,
                    data: values,
                    maxBarThickness: 20

                },
                {
                    type: "line",

                    tension: 0.1,
                    borderWidth: 0,
                    pointRadius: 0,
                    borderColor: "#3A416F",
                    borderWidth: 2,
                    backgroundColor: gradientStroke1,
                    fill: true,
                    data: values,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#b2b9bf',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#b2b9bf',
                        padding: 20,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });
</script>
<?php $this->endSection(); ?>