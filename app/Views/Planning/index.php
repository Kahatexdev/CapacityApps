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
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Socks System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Key Performance Indicator Monthly
                                </h5>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <div>
                                Filters:

                                <select id="filter-area" class="form-control d-inline w-auto">
                                    <option value="">Semua Area</option>
                                    <?php foreach ($area as $ar): ?>
                                        <option value="<?= $ar ?>"><?= $ar ?></option>
                                    <?php endforeach; ?>
                                </select>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"> Productivity </p>
                                <h6 class="font-weight-bolder mb-0" id="productivity">
                                    %

                                    <span class=" text-sm font-weight-bolder"></span>
                                </h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape  shadow text-center border-radius-md" id="bground">
                                <i aria-hidden="true" id="stats"></i>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Deffect Rate </p>
                                <h6 class="font-weight-bolder mb-0" id="deffectRate">
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
        <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Output</p>
                                <h6 class="font-weight-bolder mb-0" id="output">

                                    <span class=" text-sm font-weight-bolder">pairs </span>

                                </h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-socks text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Target</p>
                                <h6 class="font-weight-bolder mb-0" id="pph">
                                </h6>
                                <span class=" text-sm font-weight-bolder"></span>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-bullseye text-lg opacity-10" aria-hidden="true"></i>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Plan Mc</p>
                                <h6 class="font-weight-bolder mb-0" id="planmc">
                                </h6>
                                <span class=" text-sm font-weight-bolder"></span>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-cogs text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Target/day</p>
                                <h6 class="font-weight-bolder mb-0" id="targetday">
                                </h6>
                                <span class=" text-sm font-weight-bolder"></span>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-bullseye text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-3 progress-item">
        <div class="col-lg-12">

            <div class="card  z-index-2">

                <div class="card-body">

                    Target Export

                    <div class="col-lg-12 col-sm-12">
                        <div class="progress-wrapper" id="progresTarget">


                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
    <div class="row my-3">
        <div class="col-lg-12">
            <div class="card z-index-2">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Data Produksi Harian</h6>

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
    <div class="row my-3">
        <div class="col-lg-12">
            <div class="card z-index-2">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Data BS Mesin Harian</h6>

                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="chart">
                                <canvas id="bsmesin-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5>
                <h5>
                    Data Deffect stocklot

                </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-8">

                    <div class="chart">
                        <canvas id="bs-chart" class="chart-canvas" height="500"></canvas>
                    </div>

                </div>
                <div class="col-lg-6 col-md-6">
                    <table class="table-responsive mx-4">
                        <thead>
                            <tr>
                                <th>Top 10 Deffect</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="desc">

                        </tbody>
                    </table>
                </div>


            </div>
            <div class="row ">
                <div class="col-lg-6 mx-4">

                </div>

            </div>
        </div>

    </div>

    <div class="row my-3 mx-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Productivity Daily
                </h5>
                <!-- button export -->
                <form method="get" action="<?= base_url('/exportProd') ?>" class="d-inline" target="_blank">
                    <input type="hidden" name="bulan" id="export-bulan">
                    <input type="hidden" name="tahun" id="export-tahun">
                    <input type="hidden" name="area" id="export-area">
                    <button type="submit" class="btn btn-info" id="exportBtn">Export to Excel</button>
                </form>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2">Tanggal</th>
                            <th rowspan="2">Target (dz)</th>
                            <th rowspan="2">Productivity (%)</th>
                            <th rowspan="2">Deffect Rate(%)</th>
                            <th colspan="2">Produksi</th>
                            <th colspan="2">Deffect</th>
                        </tr>
                        <tr class="text-center">
                            <th>dz</th>
                            <th>kg</th>
                            <th>Mesin (kg)</th>
                            <th>Setting (dz)</th>
                        </tr>
                    </thead>
                    <tbody id="prodDetails">

                    </tbody>

                </table>
            </div>
        </div>
    </div>


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
    function fetchDashboard(bulan, tahun, area = "") {
        $.ajax({
            url: "<?= base_url('chart/dashboardData') ?>",
            type: "GET",
            data: {
                bulan: bulan,
                tahun: tahun,

                area: area
            },
            dataType: "json",
            success: function(response) {
                dashboard(response);
            }
        });
    }

    function fetchData(bulan, tahun, area = "") {
        $.ajax({
            url: "<?= base_url('chart/getProductionData') ?>",
            type: "GET",
            data: {
                bulan: bulan,
                tahun: tahun,
                area: area
            },
            dataType: "json",
            success: function(response) {
                updateChart(response);
            }
        });
    }

    function fetchBsMesin(bulan, tahun, area = "") {
        $.ajax({
            url: "<?= base_url('chart/getBsMesin') ?>",
            type: "GET",
            data: {
                bulan: bulan,
                tahun: tahun,
                area: area
            },
            dataType: "json",
            success: function(response) {
                updateChartBsMesin(response);
            }
        });
    }

    function fetchDataBs(bulan, tahun, area = "") {
        $.ajax({
            url: "<?= base_url('chart/getBsData') ?>",
            type: "GET",
            data: {
                bulan: bulan,
                tahun: tahun,
                area: area
            },
            dataType: "json",
            success: function(response) {
                updateBs(response);
            }
        });
    }

    function fetchDailyProd(bulan, tahun, area = "") {
        $.ajax({
            url: "<?= base_url('chart/getDailyProd') ?>",
            type: "GET",
            data: {
                bulan: bulan,
                tahun: tahun,
                area: area
            },
            dataType: "json",
            success: function(response) {
                dailyProd(response);
            }
        });
    }




    function dashboard(data) {
        let deffect = parseFloat(data.deffect).toFixed(2); // Format 2 desimal
        let output = parseInt(data.output / 24).toLocaleString(); // Bagi 2, format angka
        let pph = parseInt(data.targetOutput).toLocaleString(); // Format angka
        let qty = parseInt(data.qty).toLocaleString(); // Format angka
        let sisa = parseInt(data.sisa).toLocaleString(); // Format angka
        let prod = (parseInt(data.qty) - parseInt(data.sisa)) / 24; // Produksi yang selesai
        let percent = parseFloat(data.percentage).toFixed(2); // Gunakan `quality` karena `percentage` tidak ada di contoh data
        let qtyDz = parseInt(data.qty / 24).toLocaleString()
        let productivity = data.productivity;
        let planMc = data.planmc;
        let targetday = parseInt(data.targetday).toLocaleString();

        document.getElementById('deffectRate').textContent = `${deffect}%`;
        document.getElementById('output').textContent = `${output} dz`;
        document.getElementById('pph').textContent = `${pph} dz`;
        document.getElementById('productivity').textContent = `${productivity} %`;
        document.getElementById('planmc').textContent = `${planMc} mc`;
        document.getElementById('targetday').textContent = `${targetday} dz`;
        const stats = document.getElementById('stats');
        const bg = document.getElementById('bground');

        // Reset class tambahan dulu (biar nggak numpuk)
        stats.className = 'text-lg opacity-10';
        bg.className = 'icon icon-shape shadow text-center border-radius-md';
        console.log(productivity)
        if (productivity >= 85) {
            stats.classList.add('fas', 'fa-arrow-up');
            bg.classList.add('bg-gradient-success');
        } else if (productivity >= 80) {
            stats.classList.add('fas', 'fa-exclamation-triangle');
            bg.classList.add('bg-gradient-warning');
        } else {
            stats.classList.add('fas', 'fa-arrow-down');
            bg.classList.add('bg-gradient-danger');
        }

        let progres = document.getElementById("progresTarget"); // Gunakan string jika ini ID elemen

        // Tentukan warna progress bar berdasarkan percent
        let progressColor = percent < 100 ? "bg-gradient-info" : (percent == 100 ? "bg-gradient-success" : "bg-gradient-danger");

        progres.innerHTML = `
        <div class="progress-info">
            <div class="progress-percentage">
                <span class="text-sm font-weight-bold">${percent}% (${prod.toLocaleString()} dz/${qtyDz}  dz) </span>
            </div>
        </div>
        <div class="progress">
            <div id="progress-bar"
                class="progress-bar ${progressColor}"
                role="progressbar"
                aria-valuenow="${percent}"
                aria-valuemin="0"
                aria-valuemax="100"
                style="width: ${percent}%; height: 10px;">
            </div>
        </div>
    `;
    }


    let chartInstanceBsArea = null;
    let chartInstanceMixed = null;
    let chartBsMixed = null;



    function updateChart(data) {
        let labels = data.map(item => item.tgl_produksi);
        let values = data.map(item => (item.qty_produksi / 24).toFixed(0));

        if (chartInstanceMixed) {
            chartInstanceMixed.destroy();
        }

        var ctx2 = document.getElementById("mixed-chart").getContext("2d");
        chartInstanceMixed = new Chart(ctx2, {
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

    function updateChartBsMesin(data) {
        let labels = data.map(item => item.tanggal_produksi);
        let values = data.map(item => (item.qty_gram / 1000).toFixed(0));

        if (chartBsMixed) {
            chartBsMixed.destroy();
        }

        var ctx2 = document.getElementById("bsmesin-chart").getContext("2d");
        chartBsMixed = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "Jumlah bs(KG)",
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

    function updateBs(data) {
        if (!data || !Array.isArray(data)) {
            console.error("Invalid data provided to updateBs function.");
            return;
        }

        let labels = data.map(item => item.Keterangan);
        let values = data.map(item => item.qty);

        // Warna yang diulang jika jumlah data lebih banyak dari warna yang tersedia
        let chartColors = ['#845ec2', '#d65db1', '#ff6f91', '#ff9671', '#ffc75f', '#f9f871', '#008f7a', '#b39cd0', '#c34a36', '#4b4453', '#4ffbdf', '#936c00', '#c493ff', '#296073'];
        let colors = data.map((_, index) => chartColors[index % chartColors.length]);

        let ctxElement = document.getElementById("bs-chart");
        if (!ctxElement) {
            console.error("Canvas element with ID 'bs-chart' not found.");
            return;
        }

        let ctx4 = ctxElement.getContext("2d");

        let desc = document.getElementById("desc");
        if (!desc) {
            console.error("Element with ID 'desc' not found.");
            return;
        }

        // Buat tabel dari data menggunakan JavaScript
        desc.innerHTML = data.slice(0, 10).map((ch, index) => {
            let color = chartColors[index % chartColors.length]; // Ambil warna berdasarkan index
            return `
        <tr>
            <td> <i class="ni ni-button-play" style="color: ${color};"></i> ${ch.Keterangan}</td>
            <td>${ch.qty} Pcs</td>
        </tr>
    `;
        }).join('');


        // Hapus grafik lama sebelum menggambar yang baru
        if (window.bsChart) {
            window.bsChart.destroy();
        }

        // Buat grafik baru
        window.bsChart = new Chart(ctx4, {
            type: "pie",
            data: {
                labels: labels,
                datasets: [{
                    label: "Projects",
                    weight: 9,
                    cutout: 0,
                    tension: 0.9,
                    pointRadius: 2,
                    borderWidth: 2,
                    backgroundColor: colors,
                    data: values, // Perbaikan di sini (sebelumnya salah referensi ke `value`)
                    fill: false
                }],
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
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: false,
                        }
                    },
                },
            },
        });
    }

    function dailyProd(response) {
        const tbody = document.getElementById("prodDetails");
        tbody.innerHTML = ""; // Kosongkan dulu isinya

        if (!response || response.length === 0) {
            tbody.innerHTML = "<tr><td colspan='8' class='text-center'>Tidak ada data</td></tr>";
            return;
        }

        response.forEach(row => {
            const tr = document.createElement("tr");
            tr.classList.add("text-center");

            const productivity = row.productivity ?? 0;
            const deffectRate = row.deffectRate ?? 0;

            let prodClass = 'text-danger';
            if (productivity >= 85) {
                prodClass = 'text-success';
            } else if (productivity >= 80) {
                prodClass = 'text-warning';
            }
            let defClass = 'text-danger';
            if (deffectRate <= 2) {
                defClass = 'text-success';
            } else if (deffectRate <= 5) {
                defClass = 'text-warning';
            }

            tr.innerHTML = `
        <td>${row.tanggal || "-"}</td>
        <td>${(row.target ?? 0).toLocaleString()}</td>
        <td class="${prodClass}">${productivity.toFixed(2)}</td>
        <td class="${defClass}">${(row.deffectRate ?? 0).toFixed(2)}</td>
        <td>${(row.prodTotal ?? 0).toLocaleString()}</td>
        <td>${(row.prodGr ?? 0).toLocaleString()}</td>
        <td>${(row.bsmesin ?? 0).toLocaleString()}</td>
        <td>${(row.bsSetting ?? 0).toLocaleString()}</td>
    `;


            tbody.appendChild(tr);
        });
    }



    // Event listener filter bulan & tahun
    // Event listener filter bulan, tahun, dan area (buyer cuma ditampilin)
    document.getElementById("filter-bulan").addEventListener("change", handleFilterChange);
    document.getElementById("filter-tahun").addEventListener("change", handleFilterChange);
    document.getElementById("filter-area").addEventListener("change", handleFilterChange);

    // Function untuk ambil semua filter & trigger fetch
    function handleFilterChange() {
        let bulan = document.getElementById("filter-bulan").value.padStart(2, "0");
        let tahun = document.getElementById("filter-tahun").value;
        let area = document.getElementById("filter-area").value;

        fetchDashboard(bulan, tahun, area);
        fetchData(bulan, tahun, area);
        fetchDataBs(bulan, tahun, area);
        fetchBsMesin(bulan, tahun, area);
        fetchDailyProd(bulan, tahun, area);
    }

    // Set default bulan & tahun saat halaman load
    let currentDate = new Date();
    let defaultBulan = String(currentDate.getMonth() + 1).padStart(2, "0");
    let defaultTahun = currentDate.getFullYear();

    document.getElementById("filter-bulan").value = defaultBulan;
    document.getElementById("filter-tahun").value = defaultTahun;

    // Trigger pertama kali saat halaman load
    handleFilterChange();
</script>

<script>
    // Sync filter values to hidden export form fields before submit
    document.getElementById("exportBtn").addEventListener("click", function(e) {
        document.getElementById("export-bulan").value = document.getElementById("filter-bulan").value.padStart(2, "0");
        document.getElementById("export-tahun").value = document.getElementById("filter-tahun").value;
        document.getElementById("export-area").value = document.getElementById("filter-area").value;
    });
</script>
<?php $this->endSection(); ?>