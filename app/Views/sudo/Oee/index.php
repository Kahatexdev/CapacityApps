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
    /* ================= TABLE WRAPPER ================= */
    .oee-table-wrapper {
        max-height: 500px;
        overflow-y: auto;
        position: relative;
    }

    /* ================= TABLE ================= */
    #example {
        border-collapse: separate;
        border-spacing: 0;
    }

    /* ================= HEADER BASE ================= */
    #example thead th {
        font-size: 11px;
        text-transform: uppercase;
        white-space: nowrap;
        background-color: #f8f9fa;
    }

    /* header row 1 */
    #example thead tr:first-child th {
        position: sticky;
        top: 0;
        z-index: 3;
    }

    /* header row 2 */
    #example thead tr:nth-child(2) th {
        position: sticky;
        top: 34px;
        /* tinggi row pertama */
        z-index: 2;
    }

    /* ================= BODY ================= */
    #example tbody td {
        background-color: #fff;
    }

    /* ================= OEE CELL ================= */
    .oee-good {
        background-color: #55df75;
        font-weight: bold;
    }

    .oee-mid {
        background-color: #fff3cd;
        font-weight: bold;
    }

    .oee-bad {
        background-color: #f34654;
        font-weight: bold;
    }

    /* ================= KPI CARD ================= */
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

    .kpi-card h4 {
        font-weight: 700;
    }
</style>

<div class="container-fluid py-4">

    <!-- ================= HEADER ================= -->
    <div class="row mb-1">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body px-4 py-3">

                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <p class="text-sm mb-1 fw-bold text-uppercase">Capacity System</p>
                            <h5 class="fw-bold mb-0">Overall Equipment Effectiveness</h5>
                        </div>

                        <div class="col-md-6 d-flex justify-content-md-end gap-2 mt-3 mt-md-0">
                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalImport"> <i class="fas fa-file-import me-1"></i> Import Downtime </button>
                            <a href="<?= base_url($role . '/oee/download-template') ?>" class="btn btn-success">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </div>
                    </div>

                    <div class="row align-items-center border-top pt-3">
                        <div class="col-md-6">
                            <span class="fw-bold">Area: <strong class="area"> - </strong> </span><br>
                            <span class="fw-bold">Average OEE <strong class="monthName"> - </strong>:</span>
                            <span class="fw-bold text-primary ms-1" id="averageMonth">-</span><br>
                            <span class="fw-bold">Average Quality <strong class="monthName"> - </strong></span>
                            <span class="fw-bold text-primary ms-1" id="averageQuality">-</span><br>
                            <span class="fw-bold">Average Performance <strong class="monthName"> - </strong></span>
                            <span class="fw-bold text-primary ms-1" id="averagePerformance">-</span>
                        </div>

                        <div class="col-md-6 d-flex justify-content-md-end gap-2 mt-2 mt-md-0">
                            <select id="filterArea" class="form-control w-auto">
                                <?php foreach ($area as $ar): ?>
                                    <option value="<?= $ar ?>"><?= $ar ?></option>
                                <?php endforeach; ?>
                            </select>

                            <input type="date" id="filterTanggal" value="<?= $latest ?>" class="form-control w-auto">
                            <button id="btnFilter" class="btn btn-info">🔍</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= KPI ================= -->
<div class="row g-3  mx-4">
    <div class="card">

        <div class="card-header">
            <h5>
                OEE
                <strong id="tanggal">area</strong>
            </h5>
        </div>
    </div>


    <?php
    $kpis = [
        ['OEE', 'oeeText'],
        ['Quality', 'qualityText'],
        ['Performance', 'performanceText'],
        ['Availability', 'availabilityText'],
    ];
    ?>

    <?php foreach ($kpis as [$label, $id]): ?>
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm kpi-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-uppercase small"><?= $label ?></p>
                        <h4 class="mb-0" id="<?= $id ?>">-</h4>
                    </div>
                    <i class="fas fa-percent fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<!-- ================= TABLE ================= -->
<div class="card shadow-sm">
    <div class="card-body oee-table-wrapper">
        <table id="example" class="table table-sm table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th rowspan="2">Jarum</th>
                    <th rowspan="2">No Mesin</th>
                    <th colspan="2">Time (Menit)</th>
                    <th colspan="4">Performance Indicator (%)</th>
                    <th colspan="2">Downtime</th>
                </tr>
                <tr>
                    <th>Total DT</th>
                    <th>Operating</th>
                    <th>Quality</th>
                    <th>Performance</th>
                    <th>Availability</th>
                    <th class="text-primary">OEE</th>
                    <th>Breakdown</th>
                    <th>Others</th>
                </tr>
            </thead>
            <tbody>
                <!-- data -->
            </tbody>
        </table>
    </div>
</div>


</div>
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Upload File Excel</h5> <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formImportOrder" enctype="multipart/form-data" action="<?= base_url($role . '/oee/importdowntime') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3"> <label for="excelFile" class="form-label fw-semibold"> <i class="fas fa-file-excel me-1 text-success"></i> Pilih File Excel </label> <!-- Dropzone Style -->
                        <div class="p-4 border border-2 rounded-3 text-center upload-area bg-light" style="cursor: pointer;" onclick="document.getElementById('excelFile').click()"> <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-dark"></i>
                            <p class="mb-1 fw-semibold">Klik untuk memilih file</p>
                            <p class="text-muted small mb-0">Format yang didukung: .xlsx, .xls, .csv</p>
                        </div> <!-- Hidden File Input --> <input type="file" class="form-control" id="excelFile" name="file" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer"> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> <i class="fas fa-times me-1"></i> Batal </button> <button type="submit" class="btn btn-dark" id="btnImport"> <i class="fas fa-upload me-1"></i> Upload </button> </div>
            </form>
        </div>
    </div>
</div>
<!-- ================= SCRIPT ================= -->
<script>
    $('#btnFilter').on('click', handleFilterChange);

    function oeeClass(v) {
        return v >= 85 ? 'oee-good' : v >= 70 ? 'oee-mid' : 'oee-bad';
    }

    function kpiClass(v) {
        return v >= 85 ? 'kpi-good' : v >= 70 ? 'kpi-mid' : 'kpi-bad';
    }

    function formatPercent(v) {
        return Number(v).toFixed(2) + ' %';
    }

    function applyKpi(id, value) {
        const card = $('#' + id).closest('.card');
        card.removeClass('kpi-good kpi-mid kpi-bad')
            .addClass(kpiClass(value));
        $('#' + id).text(formatPercent(value));
    }

    function fetchOee(tanggal, area) {
        $.getJSON(
            "<?= base_url('oee/fetchData') ?>", {
                tanggal,
                area
            },
            function(response) {
                dashboard(response, area, tanggal);
            }
        );
    }

    function dashboard(data, area, tanggal) {
        if (!data.status) return alert(data.message);

        const dateObj = new Date(tanggal);
        const monthName = new Intl.DateTimeFormat('en-US', {
                month: 'long'
            })
            .format(dateObj);
        $('.monthName').text(monthName);
        $('.area').text(area);
        $('#tanggal').text(tanggal);
        $('#averageMonth').text(formatPercent(data.average.oee));
        $('#averageQuality').text(formatPercent(data.average.quality));
        $('#averagePerformance').text(formatPercent(data.average.performance));

        applyKpi('oeeText', data.summary.oee);
        applyKpi('qualityText', data.summary.quality);
        applyKpi('performanceText', data.summary.performance);
        applyKpi('availabilityText', data.summary.availability);

        let rows = '';
        data.detail.forEach(r => {
            rows += `
                <tr>
                    <td>${r.jarum}</td>
                    <td>${r.no_mc}</td>
                    <td>${r.total_time}</td>
                    <td>${r.operating_time}</td>
                    <td>${r.quality}%</td>
                    <td>${r.performance}%</td>
                    <td>${r.availability}%</td>
                    <td class="${oeeClass(r.oee)}">${r.oee}%</td>
                   <td>${normalizeKeterangan(r.breakdown)}</td>
                   <td>${normalizeKeterangan(r.keterangan)}</td>
                </tr>`;
        });

        $('#example tbody').html(rows);
    }

    function normalizeKeterangan(text) {
        if (!text) return '';

        return text
            .replace(/,\s*TIDAK ADA\(\d+\)/gi, '') // hapus ", TIDAK ADA(x)"
            .replace(/TIDAK ADA\(\d+\)/gi, '') // jaga-jaga kalau sendirian
            .trim();
    }



    function handleFilterChange() {
        fetchOee($('#filterTanggal').val(), $('#filterArea').val());
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