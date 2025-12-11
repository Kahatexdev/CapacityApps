<?php

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round;

$this->extend($role . '/layout'); ?>
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
        <div class="col-lg-12">
            <div class="card pb-0">
                <div class="card-header d-flex justify-content-between">
                    <h5>
                        Data Global Produksi
                    </h5>
                </div>
                <div class="card-body">

                    <form action="<?= base_url($role . '/reportGlobalProduksi') ?>" method="get">
                        <div class="row">

                            <div class="col-6">
                                <label>No Model</label>
                                <input type="text" name="no_model" class="form-control" placeholder="Masukkan No Model">
                            </div>

                            <div class="col-2 d-flex" style="margin-top: 31px;">
                                <button type="submit" class="btn btn-info w-100">
                                    Filter
                                </button>
                            </div>
                            <div class="col-2 d-flex" style="margin-top: 31px;">
                                <a href="<?= base_url($role . '/reportGlobalProduksi') ?>" class="btn btn-warning w-100">
                                    Reset
                                </a>
                            </div>
                            <?php if (!empty($noModel)) : ?>
                                <div class="col-2 d-flex" style="margin-top: 31px;">
                                    <a href="<?= base_url($role . '/excelGlobalProduksi/' . $noModel) ?>" class="btn btn-success w-100" target="_blank">
                                        Export
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($noModel)) : ?>
        <?php if (!empty($data)) : ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card pb-0 mt-3">
                        <div class="card-header d-flex justify-content-between">
                            <h5>
                                Data Produksi, BS Mesin, Perbaikan & Stocklot <?= $noModel ?>
                            </h5>
                        </div>
                        <?php foreach ($data as $area => $ar) : ?>
                            <div class="card-body" style="padding-top: 5px; padding-bottom: 5px;">
                                <div class="row">
                                    <h5>
                                        Area <b><?= $area ?></b>
                                    </h5>
                                    <div class="table-responsive">
                                        <table id="example" class="table table-border" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="3">Needle</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="3">No Model</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="3">Inisial</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="3">Style Size</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="12">TOTAL GLOBAL</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" rowspan="2">Qty Po (Dz)</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="2">Produksi</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="3">BS MC</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="3">IN PB</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;" colspan="3">IN STOCKLOT</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Gram</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">%</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">%</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Dz</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Pcs</th>
                                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ar as $id) : ?>
                                                    <tr>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['machinetypeid'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['mastermodel'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['inisial'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['size'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= round($id['qty'], 2) ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['prodDz'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['prodPcs'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['bsMcGram'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['bsMcPcs'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['bsMcPercen'] ?>%</td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['pbAreaDz'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['pbAreaPcs'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['pbAreaPercen'] ?>%</td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['bsStocklotDz'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['bsStocklotPcs'] ?></td>
                                                        <td class="text-sm" style="text-align: center;"><?= $id['bsStocklotPercen'] ?>%</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card pb-0 mt-3">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="text-danger">
                                Tidak ada data, cek kembali no model yang di filter
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>



</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#dataTable').DataTable({});
        $('#dataTable0').DataTable({
            "order": [
                [0, "desc"]
            ]
        });

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
<!-- <script>
    let data =;
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
</script> -->
<?php $this->endSection(); ?>