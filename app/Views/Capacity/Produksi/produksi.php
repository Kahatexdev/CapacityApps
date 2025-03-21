<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-4">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Produksi Per Area
                                </h5>
                            </div>
                        </div>
                        <div class="col-8 text-end">
                            <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#summaryPertgl">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Summary Produksi Pertanggal
                            </button>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#summaryTOD">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Summary Produksi
                            </button>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#summaryBS">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Summary BS MC
                            </button>
                            <button type="button" class="btn btn-sm btn-success bg-gradient-info shadow text-center border-radius-md" data-bs-toggle="modal" data-bs-target="#timter">
                                <i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Timter Produksi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
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
        <!-- modal summary produksi pertanggal -->
        <div class="modal fade" id="summaryPertgl" tabindex="-1" role="dialog" aria-labelledby="summaryPerTgl" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Summary Produksi Per Tanggal</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="<?= base_url($role . '/summaryProdPerTanggal'); ?>" method="POST">
                        <div class="modal-body align-items-center">
                            <div class="form-group">
                                <label for="buyer" class="col-form-label">Buyer</label>
                                <select class="form-control" id="buyer" name="buyer">
                                    <option></option>
                                    <?php foreach ($buyer as $buy) : ?>
                                        <option><?= $buy['kd_buyer_order'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="area" class="col-form-label">Area</label>
                                <select class="form-control" id="area" name="area">
                                    <option></option>
                                    <?php foreach ($area as $ar) : ?>
                                        <option><?= $ar ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jarum" class="col-form-label">Jarum</label>
                                <select class="form-control" id="jarum" name="jarum">
                                    <option></option>
                                    <option value="13">13</option>
                                    <option value="84">84</option>
                                    <option value="92">92</option>
                                    <option value="96">96</option>
                                    <option value="106">106</option>
                                    <option value="108">108</option>
                                    <option value="116">116</option>
                                    <option value="120">120</option>
                                    <option value="124">124</option>
                                    <option value="126">126</option>
                                    <option value="144">144</option>
                                    <option value="168">168</option>
                                    <option value="240">240</option>
                                    <option value="POM-POM">POM-POM</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pdk" class="col-form-label">No Model</label>
                                <input type="text" class="form-control" name="pdk">
                            </div>
                            <div class="form-group">
                                <label for="awal" class="col-form-label">Dari</label>
                                <input type="date" class="form-control" name="awal">
                            </div>
                            <div class="form-group">
                                <label for="akhir" class="col-form-label">Sampai</label>
                                <input type="date" class="form-control" name="akhir">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Generate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- modal summary produksi -->
        <div class="modal fade" id="summaryTOD" tabindex="-1" role="dialog" aria-labelledby="summaryTOD" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Summary Produksi</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="<?= base_url($role . '/summaryproduksi'); ?>" method="POST">
                        <div class="modal-body align-items-center">
                            <div class="form-group">
                                <label for="buyer" class="col-form-label">Buyer</label>
                                <select class="form-control" id="buyer" name="buyer">
                                    <option></option>
                                    <?php foreach ($buyer as $buy) : ?>
                                        <option><?= $buy['kd_buyer_order'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="area" class="col-form-label">Area</label>
                                <select class="form-control" id="area" name="area">
                                    <option></option>
                                    <?php foreach ($area as $ar) : ?>
                                        <option><?= $ar ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jarum" class="col-form-label">Jarum</label>
                                <select class="form-control" id="jarum" name="jarum">
                                    <option></option>
                                    <option value="13">13</option>
                                    <option value="84">84</option>
                                    <option value="92">92</option>
                                    <option value="96">96</option>
                                    <option value="106">106</option>
                                    <option value="108">108</option>
                                    <option value="116">116</option>
                                    <option value="120">120</option>
                                    <option value="124">124</option>
                                    <option value="126">126</option>
                                    <option value="144">144</option>
                                    <option value="168">168</option>
                                    <option value="240">240</option>
                                    <option value="POM-POM">POM-POM</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pdk" class="col-form-label">No Model</label>
                                <input type="text" class="form-control" name="pdk">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Generate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- modal timter produksi -->
        <div class="modal fade" id="timter" tabindex="-1" role="dialog" aria-labelledby="timter" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Timter Produksi</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="<?= base_url($role . '/timterProduksi'); ?>" method="POST">
                        <div class="modal-body align-items-center">
                            <div class="form-group">
                                <label for="area" class="col-form-label">Area</label>
                                <select class="form-control" id="area" name="area" required>
                                    <option></option>
                                    <?php foreach ($area as $ar) : ?>
                                        <option><?= $ar ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jarum" class="col-form-label">Jarum</label>
                                <select class="form-control" id="jarum" name="jarum" required>
                                    <option></option>
                                    <option value="13">13</option>
                                    <option value="84">84</option>
                                    <option value="92">92</option>
                                    <option value="96">96</option>
                                    <option value="106">106</option>
                                    <option value="108">108</option>
                                    <option value="116">116</option>
                                    <option value="120">120</option>
                                    <option value="124">124</option>
                                    <option value="126">126</option>
                                    <option value="144">144</option>
                                    <option value="168">168</option>
                                    <option value="240">240</option>
                                    <option value="POM-POM">POM-POM</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pdk" class="col-form-label">No Model</label>
                                <input type="text" class="form-control" name="pdk">
                            </div>
                            <div class="form-group">
                                <label for="awal" class="col-form-label">Tanggal Produksi</label>
                                <input type="date" class="form-control" name="awal" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Generate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- modal summary bs mc -->
        <div class="modal fade" id="summaryBS" tabindex="-1" role="dialog" aria-labelledby="summaryBS" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Summary BS MC</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="<?= base_url($role . '/exportSummaryBs'); ?>" method="POST">
                        <div class="modal-body align-items-center">
                            <div class="form-group">
                                <label for="buyer" class="col-form-label">Buyer</label>
                                <select class="form-control" id="buyer" name="buyer">
                                    <option></option>
                                    <?php foreach ($buyer as $buy) : ?>
                                        <option><?= $buy['kd_buyer_order'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="area" class="col-form-label">Area</label>
                                <select class="form-control" id="area" name="area">
                                    <option></option>
                                    <?php foreach ($area as $ar) : ?>
                                        <option><?= $ar ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jarum" class="col-form-label">Jarum</label>
                                <select class="form-control" id="jarum" name="jarum">
                                    <option></option>
                                    <option value="13">13</option>
                                    <option value="84">84</option>
                                    <option value="92">92</option>
                                    <option value="96">96</option>
                                    <option value="106">106</option>
                                    <option value="108">108</option>
                                    <option value="116">116</option>
                                    <option value="120">120</option>
                                    <option value="124">124</option>
                                    <option value="126">126</option>
                                    <option value="144">144</option>
                                    <option value="168">168</option>
                                    <option value="240">240</option>
                                    <option value="POM-POM">POM-POM</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pdk" class="col-form-label">No Model</label>
                                <input type="text" class="form-control" name="pdk">
                            </div>
                            <div class="form-group">
                                <label for="awal" class="col-form-label">Dari</label>
                                <input type="date" class="form-control" name="awal">
                            </div>
                            <div class="form-group">
                                <label for="akhir" class="col-form-label">Sampai</label>
                                <input type="date" class="form-control" name="akhir">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-info">Generate</button>
                        </div>
                    </form>
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
                <h6 class="card-title">Data Produksi Harian bulan <?= $bulan ?></h6>

            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="chart">
                            <canvas id="mixed-chart" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>


<div class="row">
    <?php foreach ($Area as $ar) : ?>

        <div class="col-xl-6 col-sm-3 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <?php if (stripos($ar, "Gedung") !== false) : ?>
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Majalaya <?= $ar ?></p>
                                <?php else : ?>
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $ar ?></p>
                                <?php endif; ?>
                                <h5 class="font-weight-bolder mb-0">
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <?php if (stripos($ar, 'KK8J') !== false || stripos($ar, '13G') !== false) : ?>

                                <a href="<?= base_url($role . '/detailproduksi/' . $ar) ?>" class="btn btn-info btn-sm"> <i class="fas fa-mitten text-lg opacity-10" aria-hidden="true"></i> Details</a>

                            <?php else : ?>
                                <a href="<?= base_url($role . '/detailproduksi/' . $ar) ?>" class="btn btn-info btn-sm"> <i class="fas fa-socks text-lg opacity-10" aria-hidden="true"></i> Details</a>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">

                            <div class="chart">
                                <canvas id="<?= $ar ?>-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    <?php endforeach ?>
</div>

</div>
<!-- Skrip JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>

<script>
    let productionData = <?php echo json_encode($Produksi); ?>;
    let labels = productionData.map(item => item.tgl_produksi);
    let values = productionData.map(item => (item.qty_produksi / 24).toFixed(0));
    var ctx2 = document.getElementById("mixed-chart").getContext("2d");

    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);
    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)');

    var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);
    gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
    gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)');

    new Chart(ctx2, {
        data: {
            labels: labels,
            datasets: [{
                type: "bar",
                label: "Jumlah Produksi",
                borderWidth: 0,
                pointRadius: 30,
                backgroundColor: "#3A416F",
                fill: true,
                data: values,
                maxBarThickness: 20
            }, ],
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
<script>
    $(document).ready(function() {
        function fetchData() {
            $.ajax({
                url: '<?= base_url($role . '/produksiareachart') ?>',
                type: 'GET',
                success: function(response) {
                    updatechart(response)
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        function updatechart(response) {
            var produksi = JSON.parse(response);
            for (const key in produksi) {
                const value = produksi[key];
                var canvasId = key + '-chart';
                var canvasElement = document.getElementById(canvasId);
                if (canvasElement.chart) {
                    canvasElement.chart.destroy();
                }
                var ctx2 = canvasElement.getContext("2d");

                // Check if value is an array or a single object
                if (Array.isArray(value)) {
                    var tanggal = value.map(item => item.tgl_produksi);
                    var values = value.map(item => (item.qty_produksi / 24).toFixed(0));

                    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);
                    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
                    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
                    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)');

                    var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);
                    gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
                    gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
                    gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)');

                    new Chart(ctx2, {
                        data: {
                            labels: tanggal,
                            datasets: [{
                                type: "bar",
                                label: "Jumlah Produksi",
                                borderWidth: 0,
                                pointRadius: 30,
                                backgroundColor: "#3A416F",
                                fill: true,
                                data: values,
                                maxBarThickness: 20
                            }, ],
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


                }


            }


        }
        setInterval(fetchData, 5000);
        fetchData();

    });
</script>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>