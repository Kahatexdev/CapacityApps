<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row d-flex justify-content-between">
                        <div class="col-4">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Produksi Per Area
                                </h5>
                            </div>
                        </div>
                        <div class="col-8 text-end">
                            <ul class="navbar-nav d-flex flex-row justify-content-end">
                                <li class="nav-item dropdown me-3"> <!-- Tambahkan margin kanan untuk jarak -->
                                    <a href="#" class="nav-link dropdown-toggle text-body font-weight-bold px-2" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-file-alt"></i>
                                        <span class="d-lg-inline-block d-none ms-1">Summary</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownReports">
                                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#summaryPertgl">Produksi Pertanggal</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#summaryTOD">Produksi Global</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#summaryBS">Bs Mesin</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#timter">Timter</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle text-body font-weight-bold px-2" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-file-alt"></i>
                                        <span class="d-lg-inline-block d-none ms-1">Produksi</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownReports">
                                        <li><a class="dropdown-item" href="<?= base_url($role . '/produksi') ?>">Import</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#inputProduksi">Input Manual</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reset">Reset PDK</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#resetarea">Reset Produksi Area</a></li>
                                    </ul>
                                </li>
                            </ul>
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
        <div class="modal fade" id="reset" tabindex="-1" role="dialog" aria-labelledby="reset" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Reset Produksi</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body align-items-center">
                        <form action="<?= base_url($role . '/resetproduksi'); ?>" method="post">
                            <div class="form-group">
                                <label for="awal">PDK</label>
                                <input type="text" class="form-control" name="pdk">
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Reset</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="resetarea" tabindex="-1" role="dialog" aria-labelledby="resetarea" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Reset Produksi Area</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body align-items-center">
                        <form action="<?= base_url($role . '/resetproduksiarea'); ?>" method="post">
                            <div class="form-group">
                                <label for="awal">Area</label>
                                <input type="text" class="form-control" name="area" required>
                            </div>
                            <div class="form-group">
                                <label for="awal">Awal</label>
                                <input type="date" class="form-control" name="awal">
                            </div>
                            <div class="form-group">
                                <label for="awal">Akhir</label>
                                <input type="date" class="form-control" name="akhir">
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Reset</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- modal input produksi -->
        <div class="modal fade" id="inputProduksi" tabindex="-1" role="dialog" aria-labelledby="inputProduksi" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Input Produksi</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="<?= base_url($role . '/prosesInputProdManual'); ?>" method="POST">
                        <div class="modal-body align-items-center">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tgl_produksi" class="col-form-label">Tanggal Produksi</label>
                                        <input type="date" class="form-control" name="tgl_produksi" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="area" class="col-form-label">Area</label>
                                        <select class="form-control" id="area-prod" name="area" required>
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="box" class="col-form-label">No Box</label>
                                        <input type="text" class="form-control" name="box" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_mesin" class="col-form-label">No Mesin</label>
                                        <input type="text" class="form-control" name="no_mesin" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shift_b" class="col-form-label">Shift B</label>
                                        <input type="text" class="form-control" name="shift_b" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nomodel" class="col-form-label">No Model</label>
                                        <select class="form-control select2" id="nomodel" name="nomodel" required>
                                            <option value="">Pilih No Model</option>
                                            <?php foreach ($models as $model) : ?>
                                                <option value="<?= $model['mastermodel']; ?>"><?= $model['mastermodel']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="size" class="col-form-label">Style Size</label>
                                        <select class="form-control" id="size" name="size" required>
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="label" class="col-form-label">No Label</label>
                                        <input type="text" class="form-control" name="label" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shift_a" class="col-form-label">Shift A</label>
                                        <input type="text" class="form-control" name="shift_a" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shift_c" class="col-form-label">Shift C</label>
                                        <input type="text" class="form-control" name="shift_c" required>
                                    </div>
                                    <input type="hidden" id="qty_produksi" name="qty_produksi">
                                </div>

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

    <div class="row my-3">
        <div class="col-lg-12">
            <div class="card z-index-2">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Data Produksi Harian</h6>
                    <div>

                        <select id="filter-bulan" class="form-control d-inline w-auto">
                            <option value="">Semua Bulan</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>"><?= date("F", mktime(0, 0, 0, $i, 1)) ?></option>
                            <?php endfor; ?>
                        </select>
                        <select id="filter-tahun" class="form-control d-inline w-auto">
                            <option value="">Semua Tahun</option>
                            <?php for ($i = date("Y") - 5; $i <= date("Y"); $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <a href="<?= base_url($role . '/detailproduksi') ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-socks text-lg opacity-10" aria-hidden="true"></i> Details
                        </a>
                    </div>
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
                                    <h5 class="font-weight-bolder mb-0"></h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <?php if (stripos($ar, 'KK8J') !== false || stripos($ar, '13G') !== false) : ?>
                                    <a href="<?= base_url($role . '/detailproduksi/' . $ar) ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-mitten text-lg opacity-10" aria-hidden="true"></i> Details
                                    </a>
                                <?php else : ?>
                                    <a href="<?= base_url($role . '/detailproduksi/' . $ar) ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-socks text-lg opacity-10" aria-hidden="true"></i> Details
                                    </a>
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
        <?php endforeach; ?>
    </div>


</div>
<!-- Skrip JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>

<script>
    let chartInstance = null;

    function fetchData(bulan, tahun) {
        $.ajax({
            url: "<?= base_url('chart/getProductionData') ?>",
            type: "GET",
            data: {
                bulan: bulan,
                tahun: tahun
            },
            dataType: "json",
            success: function(response) {
                updateChart(response);
            }
        });
    }

    function updateChart(data) {
        let labels = data.map(item => item.tgl_produksi);
        let values = data.map(item => (item.qty_produksi / 24).toFixed(0));

        if (chartInstance) {
            chartInstance.destroy();
        }

        var ctx2 = document.getElementById("mixed-chart").getContext("2d");
        chartInstance = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "Jumlah Produksi",
                    backgroundColor: "#3A416F",
                    data: values,
                    maxBarThickness: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        grid: {
                            borderDash: [5, 5]
                        },
                        ticks: {
                            padding: 10
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                }
            }
        });
    }

    // Event listener filter bulan & tahun
    document.getElementById("filter-bulan").addEventListener("change", function() {
        let bulan = this.value.padStart(2, "0"); // Pastikan selalu dua digit
        let tahun = document.getElementById("filter-tahun").value;
        fetchData(bulan, tahun);
    });

    document.getElementById("filter-tahun").addEventListener("change", function() {
        let bulan = document.getElementById("filter-bulan").value.padStart(2, "0");
        let tahun = this.value;
        fetchData(bulan, tahun);
    });

    // Load awal dengan bulan & tahun sekarang
    let currentDate = new Date();
    let defaultBulan = String(currentDate.getMonth() + 1).padStart(2, "0");
    let defaultTahun = currentDate.getFullYear();

    document.getElementById("filter-bulan").value = defaultBulan;
    document.getElementById("filter-tahun").value = defaultTahun;

    fetchData(defaultBulan, defaultTahun);
</script>
<script>
    $(document).ready(function() {
        let charts = {}; // Menyimpan referensi chart agar bisa di-destroy

        function fetchData() {
            // Ambil nilai filter bulan & tahun dari dropdown
            var selectedBulan = $('#filter-bulan').val();
            var selectedTahun = $('#filter-tahun').val();

            $.ajax({
                url: '<?= base_url($role . '/produksiareachart') ?>',
                type: 'GET',
                dataType: "json",
                data: {
                    bulan: selectedBulan,
                    tahun: selectedTahun
                },
                success: function(response) {
                    updateChart(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        function updateChart(produksi) {
            for (const key in produksi) {
                const value = produksi[key];
                var canvasId = key + '-chart';
                var canvasElement = document.getElementById(canvasId);

                if (!canvasElement) {
                    console.warn(`Canvas dengan ID "${canvasId}" tidak ditemukan.`);
                    continue;
                }

                var ctx2 = canvasElement.getContext("2d");

                // Hapus chart lama jika ada
                if (charts[canvasId]) {
                    charts[canvasId].destroy();
                }

                if (Array.isArray(value)) {
                    var tanggal = value.map(item => item.tgl_produksi);
                    var values = value.map(item => (item.qty_produksi / 24).toFixed(0));

                    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);
                    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
                    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
                    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)');

                    charts[canvasId] = new Chart(ctx2, {
                        type: "bar",
                        data: {
                            labels: tanggal,
                            datasets: [{
                                label: "Jumlah Produksi",
                                backgroundColor: "#3A416F",
                                borderWidth: 0,
                                data: values,
                                maxBarThickness: 20
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            scales: {
                                y: {
                                    grid: {
                                        drawBorder: false,
                                        display: true,
                                        drawTicks: false,
                                        borderDash: [5, 5]
                                    },
                                    ticks: {
                                        display: true,
                                        padding: 10,
                                        color: '#b2b9bf',
                                        font: {
                                            size: 11,
                                            family: "Open Sans"
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        drawBorder: false,
                                        display: false
                                    },
                                    ticks: {
                                        display: true,
                                        color: '#b2b9bf',
                                        padding: 20,
                                        font: {
                                            size: 11,
                                            family: "Open Sans"
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }

        // Trigger fetchData saat filter dropdown berubah
        $('#filter-bulan, #filter-tahun').on('change', fetchData);

        // Initial fetch saat halaman dimuat
        fetchData();
    });
</script>
<script>
    $(document).ready(function() {
        // Saat No Model diinputkan, fetch Area
        $('#nomodel').on('change', function() {
            let nomodel = $(this).val();
            if (nomodel !== '') {
                $.ajax({
                    url: "<?= base_url($role . '/get-area') ?>",
                    type: "POST",
                    data: {
                        nomodel: nomodel
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        $('#area-prod').empty().append('<option></option>'); // Reset area dropdown

                        $.each(response, function(index, value) {
                            $('#area-prod').append('<option value="' + value.factory + '">' + value.factory + '</option>');
                        });
                    }
                });
            } else {
                $('#area-prod').empty().append('<option></option>'); // Kosongkan dropdown jika No Model dikosongkan
            }
        });

        // Saat Area dipilih, fetch Size berdasarkan No Model dan Area
        $('#area-prod').on('change', function() {
            let nomodel = $('#nomodel').val();
            let area = $(this).val();
            if (nomodel !== '' && area !== '') {
                $.ajax({
                    url: "<?= base_url($role . '/get-size') ?>", // Ganti dengan endpoint untuk mengambil Size
                    type: "POST",
                    data: {
                        nomodel: nomodel,
                        area: area
                    },
                    dataType: "json",
                    success: function(response) {
                        $('#size').empty().append('<option value="">Pilih Size</option>');

                        $.each(response, function(index, value) {
                            $('#size').append('<option value="' + value.size + '">' + value.size + '</option>');
                        });
                    }
                });
            } else {
                $('#size').empty().append('<option></option>');
            }
        });

        //Hitung qty_produksi
        function hitungTotal() {
            let shiftA = parseInt($('input[name="shift_a"]').val()) || 0;
            let shiftB = parseInt($('input[name="shift_b"]').val()) || 0;
            let shiftC = parseInt($('input[name="shift_c"]').val()) || 0;
            let total = shiftA + shiftB + shiftC;

            $('#qty_produksi').val(total);
        }

        // Panggil fungsi saat input berubah
        $('input[name="shift_a"], input[name="shift_b"], input[name="shift_c"]').on('input', hitungTotal);
    });
</script>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>