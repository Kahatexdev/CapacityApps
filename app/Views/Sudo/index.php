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
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"> OEE </p>
                                <h5 class="font-weight-bolder mb-0">
                                    78 %

                                    <span class=" text-sm font-weight-bolder"></span>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Deffect Rate </p>
                                <h5 class="font-weight-bolder mb-0" id="deffectRate">
                                    %
                                    <span class=" text-sm font-weight-bolder"></span>
                                </h5>
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
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Output</p>
                                <h5 class="font-weight-bolder mb-0" id="output">

                                    <span class=" text-sm font-weight-bolder">pairs </span>

                                </h5>
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
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">PPH</p>
                                <h5 class="font-weight-bolder mb-0" id="pph">
                                    <span class=" text-sm font-weight-bolder">pairs/hour</span>
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
    <div class="row my-3 progress-item">
        <div class="col-lg-12">

            <div class="card  z-index-2">

                <div class="card-body">

                    Target Produksi

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
    <div class="card">
        <div class="card-header">
            <h5>
                <h5>
                    Data Deffect

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

                    <div class="chart">
                        <canvas id="bs-area" class="chart-canvas" height="500"></canvas>
                    </div>
                </div>


            </div>
            <div class="row ">
                <div class="col-lg-6 mx-4">
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
    function fetchDashboard(bulan, tahun) {
        $.ajax({
            url: "<?= base_url('chart/dashboardData') ?>",
            type: "GET",
            data: {
                bulan: bulan,
                tahun: tahun
            },
            dataType: "json",
            success: function(response) {
                dashboard(response);
            }
        });
    }

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

    function fetchDataBs(bulan, tahun) {
        $.ajax({
            url: "<?= base_url('chart/getBsData') ?>",
            type: "GET",
            data: {
                bulan: bulan,
                tahun: tahun
            },
            dataType: "json",
            success: function(response) {
                updateBs(response);
            }
        });
    }



    function dashboard(data) {
        let deffect = parseFloat(data.deffect).toFixed(2); // Format 2 desimal
        let output = parseInt(data.output / 2).toLocaleString(); // Bagi 2, format angka
        let pph = parseInt(data.pph).toLocaleString(); // Format angka
        let qty = parseInt(data.qty).toLocaleString(); // Format angka
        let sisa = parseInt(data.sisa).toLocaleString(); // Format angka
        let prod = (parseInt(data.qty) - parseInt(data.sisa)) / 24; // Produksi yang selesai
        let percent = parseFloat(data.percentage).toFixed(2); // Gunakan `quality` karena `percentage` tidak ada di contoh data
        let qtyDz = parseInt(data.qty / 24).toLocaleString()

        document.getElementById('deffectRate').textContent = `${deffect}%`;
        document.getElementById('output').textContent = `${output} pairs`;
        document.getElementById('pph').textContent = `${pph} pairs/hour`;

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


    function bsArea(bulan, tahun) {
        $.ajax({
            url: "<?= base_url('chart/bsArea') ?>",
            type: "GET",
            data: {
                bulan: bulan,
                tahun: tahun
            },
            dataType: "json",
            success: function(response) {
                chartBsArea(response);
            }
        });
    }
    let chartInstanceBsArea = null;
    let chartInstanceMixed = null;;

    function chartBsArea(data) {
        let labels = data.map(item => item.area);
        let values = data.map(item => item.bs);

        if (chartInstanceBsArea) {
            chartInstanceBsArea.destroy();
        }

        var ctx2 = document.getElementById("bs-area").getContext("2d");
        chartInstanceBsArea = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "Jumlah Deffect",
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



    // Event listener filter bulan & tahun
    document.getElementById("filter-bulan").addEventListener("change", function() {
        let bulan = this.value.padStart(2, "0"); // Pastikan selalu dua digit
        let tahun = document.getElementById("filter-tahun").value;
        fetchDashboard(bulan, tahun);
        fetchData(bulan, tahun);
        fetchDataBs(bulan, tahun);
        bsArea(bulan, tahun);

    });

    document.getElementById("filter-tahun").addEventListener("change", function() {
        let bulan = document.getElementById("filter-bulan").value.padStart(2, "0");
        let tahun = this.value;
        fetchDashboard(bulan, tahun);
        fetchData(bulan, tahun);
        fetchDataBs(bulan, tahun);
        bsArea(bulan, tahun);

    });

    // Load awal dengan bulan & tahun sekarang
    let currentDate = new Date();
    let defaultBulan = String(currentDate.getMonth() + 1).padStart(2, "0");
    let defaultTahun = currentDate.getFullYear();

    document.getElementById("filter-bulan").value = defaultBulan;
    document.getElementById("filter-tahun").value = defaultTahun;

    fetchDashboard(defaultBulan, defaultTahun);
    fetchData(defaultBulan, defaultTahun);
    fetchDataBs(defaultBulan, defaultTahun);
    bsArea(defaultBulan, defaultTahun);
</script>
<?php $this->endSection(); ?>