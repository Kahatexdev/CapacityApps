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

    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card pb-0">
                    <div class="card-header d-flex justify-content-between">
                        <div class="col-4">
                            <h5>
                                Import Data Produksi
                            </h5>
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
                    <div class="card-body">
                        <div class="row">
                            <div class="col">

                                <div id="drop-area" class="border rounded d-flex justify-content-center align-item-center mx-3" style="height:200px; width: 100%; cursor:pointer;">
                                    <div class="text-center mt-5">
                                        <i class="fas fa-upload" style="font-size: 48px;"></i>

                                        <p class=" mt-3" style="font-size: 28px;">
                                            Upload file here
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12 pl-0">

                                <form action="<?= base_url('user/importproduksi') ?>" method="post" enctype="multipart/form-data">
                                    <input type="file" id="fileInput" name="excel_file" multiple accept=".xls , .xlsx" class="form-control mx-3">
                                    <button type="submit" class="btn btn-info btn-block w-100 mx-3"> Simpan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Data Produksi Harian
                        </h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable0" class="display  striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Produksi</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">QTY Upload</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produksi as $prd) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $prd['tgl_produksi']; ?></td>
                                            <td class="text-sm"><?= $prd['qty']; ?></td>
                                            <td class="text-sm"><?= $prd['admin']; ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Data Pdk
                        </h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dataTable" class="display  striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">PDK</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty Produksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pdk as $order) : ?>
                                        <tr>
                                            <td class="text-sm"><?= $order['mastermodel']; ?></td>
                                            <td class="text-sm"><?= $order['totalqty']; ?></td>
                                            <td class="text-sm"><?= $order['totalsisa']; ?></td>
                                            <td class="text-sm"><?= $order['totalproduksi']; ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>



                </div>


            </div>
        </div>
    </div> -->


</div>
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
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#dataTable0').DataTable({
            "order": [
                [0, "desc"]
            ]
        });
        $('#dataTable').DataTable({});

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