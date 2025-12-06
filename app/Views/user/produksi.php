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
                            <option value="200">200</option>
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
                                <select class="select2 form-select" id="nomodel" name="nomodel" required>
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
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<!-- jQuery (Diperlukan oleh Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
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

        $(document).ready(function() {
            // Inisialisasi Select2 saat modal pertama kali dibuka
            $('#inputProduksi').on('shown.bs.modal', function() {
                $('#nomodel').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#inputProduksi'), // Agar dropdown muncul di dalam modal
                    placeholder: "Pilih No Model",
                    allowClear: true
                });
            });
        });
    });
</script>
<?php $this->endSection(); ?>