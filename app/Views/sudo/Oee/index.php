<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<style>
    .upload-area:hover {
        background-color: #eef5ff;
        border-color: #0d6efd !important;
        transition: 0.2s;
    }
</style>
<style>
    #example thead th {
        font-size: 11px;
        text-transform: uppercase;
    }

    .oee-good {
        background-color: #55df75ff;
        font-weight: bold;
    }

    .oee-mid {
        background-color: #fff3cd;
        font-weight: bold;
    }

    .oee-bad {
        background-color: #f34654ff;
        font-weight: bold;
    }

    .kpi-good {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
        color: #fff;
    }

    .kpi-mid {
        background: linear-gradient(135deg, #f1c40f, #f39c12);
        color: #fff;
    }

    .kpi-bad {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff;
    }
</style>


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


    <div class="row my-1">
        <div class="col-12">
            <div class="card">
                <div class="card-body px-4 py-3">

                    <!-- HEADER -->
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <p class="text-sm mb-1 text-capitalize font-weight-bold">
                                Capacity System
                            </p>
                            <h5 class="font-weight-bolder mb-0">
                                Overall Equipment Effectiveness
                            </h5>
                        </div>

                        <!-- ACTION BUTTON -->
                        <div class="col-md-6 d-flex gap-2 justify-content-md-end mt-3 mt-md-0">
                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalImport"> <i class="fas fa-file-import me-1"></i> Import Downtime </button>

                            <a href="<?= base_url($role . '/oee/download-template') ?>" class="btn btn-success">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </div>
                    </div>

                    <!-- FILTER INFO + CONTROL -->
                    <div class="row align-items-center pt-2 border-top">

                        <!-- INFO -->
                        <div class="col-md-6 d-flex gap-3 align-items-center">
                            <p class="text-sm mb-1 text-capitalize font-weight-bold">
                                Average OEE This Month :
                            </p>
                            <h5 class="font-weight-bolder mb-0" id="averageMonth">
                            </h5>
                        </div>

                        <!-- FILTER CONTROL -->
                        <div class="col-md-6 d-flex gap-2 justify-content-md-end align-items-center mt-2 mt-md-0">
                            Filters:
                            <select id="filterArea" name="filterArea" class="form-control w-auto">
                                <?php foreach ($area as $ar): ?>
                                    <option value="<?= $ar ?>"><?= $ar ?></option>
                                <?php endforeach; ?>
                            </select>

                            <input type="date" id="filterTanggal" value="<?= $latest ?>" name="filterTanggal" class="form-control w-auto">
                            <button id="btnFilter" class="btn btn-info"> 🔎︎</button>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"> OEE </p>
                                <h6 class="font-weight-bolder mb-0" id="oeeText">
                                    %

                                    <span class=" text-sm font-weight-bolder"></span>
                                </h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-percent text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"> Quality </p>
                                <h6 class="font-weight-bolder mb-0" id="qualityText">
                                    %

                                    <span class=" text-sm font-weight-bolder"></span>
                                </h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-percent text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"> Performance </p>
                                <h6 class="font-weight-bolder mb-0" id="performanceText">
                                    %

                                    <span class=" text-sm font-weight-bolder"></span>
                                </h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-percent text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-4 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"> Availability </p>
                                <h6 class="font-weight-bolder mb-0" id="availabilityText">
                                    %

                                    <span class=" text-sm font-weight-bolder"></span>
                                </h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-percent text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row mt-3 mx-1">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-sm align-middle text-center">
                        <thead class="bg-light">
                            <tr>
                                <th rowspan="2">Jarum</th>
                                <th rowspan="2">Mesin</th>

                                <th colspan="3">Time (Menit)</th>
                                <th colspan="4">Performance Indicator (%)</th>
                                <th colspan="2">Downtime</th>
                            </tr>
                            <tr>
                                <th>Loading</th>
                                <th>Operating</th>
                                <th>Total DT</th>

                                <th>Quality</th>
                                <th>Performance</th>
                                <th>Availability</th>
                                <th class="fw-bold text-primary">OEE</th>

                                <th>Breakdown</th>
                                <th>Others</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>


    <div class="modal fade" id="modalImport" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Upload File Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formImportOrder" enctype="multipart/form-data" action="<?= base_url($role . '/oee/importdowntime') ?>" method="post">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="excelFile" class="form-label fw-semibold">
                                <i class="fas fa-file-excel me-1 text-success"></i>
                                Pilih File Excel
                            </label>

                            <!-- Dropzone Style -->
                            <div class="p-4 border border-2 rounded-3 text-center upload-area bg-light"
                                style="cursor: pointer;"
                                onclick="document.getElementById('excelFile').click()">

                                <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-dark"></i>

                                <p class="mb-1 fw-semibold">Klik untuk memilih file</p>
                                <p class="text-muted small mb-0">Format yang didukung: .xlsx, .xls, .csv</p>
                            </div>

                            <!-- Hidden File Input -->
                            <input type="file" class="form-control" id="excelFile" name="file"
                                accept=".xlsx, .xls, .csv" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>

                        <button type="submit" class="btn btn-dark" id="btnImport">
                            <i class="fas fa-upload me-1"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
    document.getElementById("btnFilter").addEventListener("click", handleFilterChange);

    function oeeClass(oee) {
        if (oee >= 85) return 'oee-good';
        if (oee >= 70) return 'oee-mid';
        return 'oee-bad';
    }

    function kpiClass(value) {
        value = Number(value);

        if (value >= 85) return 'kpi-good';
        if (value >= 70) return 'kpi-mid';
        return 'kpi-bad';
    }

    function applyKpiCard(selector, value) {
        const cardBody = $(selector).closest('.card-body');

        cardBody.removeClass('kpi-good kpi-mid kpi-bad');
        cardBody.addClass(kpiClass(value));
    }

    function fetchOee(tanggal, area = "") {
        $.ajax({
            url: "<?= base_url('oee/fetchData') ?>",
            type: "GET",
            data: {
                tanggal: tanggal,
                area: area
            },
            dataType: "json",
            success: function(response) {
                dashboard(response);
            }
        });
    }

    function formatPercent(value) {
        return Number(value).toFixed(2) + ' %';
    }

    function dashboard(data) {
        if (!data.status) {
            alert(data.message);
            return;
        }

        // ================= FILTER INFO =================
        $('#tanggalOee').text(data.filter.tanggal);
        $('#averageMonth').text(formatPercent(data.average.oee));
        $('#area').text(data.filter.area);

        // ================= SUMMARY CARD =================
        $('#oeeText').text(formatPercent(data.summary.oee));
        applyKpiCard('#oeeText', data.summary.oee);
        $('#qualityText').text(formatPercent(data.summary.quality));
        applyKpiCard('#qualityText', data.summary.quality);

        $('#performanceText').text(formatPercent(data.summary.performance));
        applyKpiCard('#performanceText', data.summary.performance);
        $('#availabilityText').text(formatPercent(data.summary.availability));
        applyKpiCard('#availabilityText', data.summary.availability);

        // ================= TABLE =================
        let tbody = '';
        data.detail.forEach(row => {
            tbody += `
            <tr>
                <td>${row.jarum}</td>
                <td>${row.no_mc}</td>
                <td>${row.total_time}</td>
                <td>${row.loading_time}</td>
                <td>${row.operating_time}</td>
                <td>${row.quality} %</td>
                <td>${row.performance} %</td>
                <td>${row.availability} %</td>
       <td class="${oeeClass(row.oee)}">${row.oee} %</td>
                <td>${row.breakdown}</td>
                <td>${row.keterangan}</td>
            </tr>
        `;
        });

        $('#example tbody').html(tbody);
    }


    function handleFilterChange() {
        let tanggal = document.getElementById("filterTanggal").value;
        let area = document.getElementById("filterArea").value;

        fetchOee(tanggal, area);
    }



    handleFilterChange();
</script>
<script type="text/javascript">
    $("#formImportOrder").on("submit", function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "<?= base_url($role . '/oee/importdowntime') ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",

            beforeSend: function() {
                $("#btnImport")
                    .prop("disabled", true)
                    .html(`<span class="spinner-border spinner-border-sm"></span> Importing...`);
            },

            success: function(res) {
                $("#btnImport").prop("disabled", false).html("Import");

                if (res.status === true) {
                    Swal.fire({
                        icon: "success",
                        title: "Import berhasil!",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $("#modalImport").modal("hide");
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: "Gagal Import",
                        text: res.message ?? "Import gagal"
                    });
                }
            },

            error: function(xhr) {
                $("#btnImport").prop("disabled", false).html("Import");

                let msg = "Terjadi kesalahan server.";

                if (xhr.responseJSON) {
                    msg = xhr.responseJSON.message ??
                        xhr.responseJSON.error ??
                        msg;
                }

                console.error(xhr);

                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: msg
                });
            }
        });
    });
</script>

<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>