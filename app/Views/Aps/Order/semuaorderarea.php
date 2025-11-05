<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Order
                                </h5>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <!-- Dropdown Estimasi SPK -->
                            <div class="dropdown">
                                <button class="btn btn-info dropdown-toggle" type="button" id="dropdownEstimasiSPK" data-bs-toggle="dropdown" aria-expanded="false">
                                    Estimasi SPK
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownEstimasiSPK">
                                    <?php foreach ($area as $ar): ?>
                                        <li>
                                            <a class="dropdown-item" href="<?= base_url($role . '/estimasispk/' . $ar) ?>">
                                                <?= $ar ?>
                                            </a>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>

                            <!-- Dropdown Status Order -->
                            <div class="dropdown">
                                <button class="btn btn-info dropdown-toggle" type="button" id="dropdownStatusOrder" data-bs-toggle="dropdown" aria-expanded="false">
                                    Status Order
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownStatusOrder">
                                    <?php foreach ($area as $ar): ?>
                                        <li>
                                            <a class="dropdown-item" href="<?= base_url($role . '/statusorder/' . $ar) ?>">
                                                <?= $ar ?>
                                            </a>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>

                            <!-- Tombol Excel -->
                            <a class="btn bg-gradient-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-file-excel me-2"></i> Excel Data Order
                            </a>
                        </div>
                    </div>

                </div>



            </div>


            <div class="row mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="display compact " style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Turun Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Buyer</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Order</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Product Type</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Desc</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Seam</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Color</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">StartMc</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Tgl Sch Celup</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Repeat</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
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

            <!-- modal -->
            <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModal" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Input Aps Report</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">Ã—</span>
                            </button>
                        </div>
                        <div class="modal-body align-items-center">
                            <div class="row align-items-center">
                                <div id="drop-area" class="border rounded d-flex justify-content-center align-item-center mx-3" style="height:200px; width: 95%; cursor:pointer;">
                                    <div class="text-center mt-5">
                                        <i class="ni ni-cloud-upload-96" style="font-size: 48px;">

                                        </i>
                                        <p class="mt-3" style="font-size: 28px;">
                                            Upload file here
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-9 pl-0">

                                    <form action="<?= base_url($role . '/importModel') ?>" id="modalForm" method="POST" enctype="multipart/form-data">
                                        <input type="text" class="form-control" name="id_model" hidden>
                                        <input type="text" class="form-control" name="no_model" hidden>
                                        <input type="file" id="fileInput" name="excel_file" multiple accept=".xls , .xlsx" class="form-control ">
                                </div>
                                <div class="col-3 pl-0">
                                    <button type="submit" class="btn btn-info btn-block"> Simpan</button>
                                    </form>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <!-- modal flowproses -->
            <div
                class="modal fade"
                id="flowProsesModal"
                tabindex="-1"
                aria-labelledby="flowProsesModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
                    <div class="modal-content rounded-3 shadow">
                        <!-- HEADER -->
                        <div class="modal-header bg-light border-bottom-2">
                            <h5 class="modal-title" id="flowProsesModalLabel">
                                <i class="fas fa-cogs me-2"></i>
                                Flow Proses
                            </h5>
                            <button
                                type="button"
                                class="btn-close bg-transparent text-dark"
                                data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <!-- BODY -->
                        <div class="modal-body">
                            <div id="flowProsesContent">
                                <!-- Spinner -->
                                <div class="d-flex justify-content-center my-5">
                                    <div
                                        class="spinner-border text-info"
                                        role="status"
                                        style="width: 3rem; height: 3rem;">
                                        <span class="visually-hidden">Loading…</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="modal-footer bg-light border-top-2">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- export modal -->
            <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModal" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Export Data Order</h5>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">x</span>
                            </button>
                        </div>
                        <div class="modal-body align-items-center">
                            <form action="<?= base_url($role . '/exportDataOrderArea') ?>" method="POST">
                                <div class="row align-items-center">
                                    <label for="searchModel">Pilih Model</label>
                                    <select name="searchModel" id="searchModel" class="form-select" style="width: 100%">
                                        <option value="">Pilih Model</option>
                                    </select>

                                </div>

                                <div class="row mt-2">
                                    <div class="col text-end">
                                        <button type="submit" class="btn btn-success btn-block">Generate</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>

            <script type="text/javascript">
                $(document).ready(function() {
                    $('#searchModel').select2({
                        placeholder: 'Pilih Model',
                        allowClear: true,
                        dropdownParent: $('#exportModal'), // ini penting biar dropdown muncul dalam modal
                        ajax: {
                            url: '<?= base_url($role . "/dataOrderSearch") ?>',
                            type: 'POST',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    searchTerm: params.term // user input
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: $.map(data, function(item) {
                                        console.log(data)
                                        return {
                                            id: item.value, // VALUE dari <option>
                                            text: item.label // TEXT yang ditampilkan
                                        }
                                    })
                                };
                            },
                            cache: true
                        }
                    });

                    $('#example').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            url: '<?= base_url($role . '/tampilPerdelivery') ?>',
                            type: 'POST',
                            dataSrc: function(json) {
                                console.log("🧾 DataTables JSON diterima:", json);
                                return json.data;
                            }
                        },
                        "columns": [{
                                "data": "created_at",
                                "render": function(data, type, row) {
                                    if (data) {
                                        // Parse the date and format it as d-M-Y
                                        var date = new Date(data);
                                        var day = ('0' + date.getDate()).slice(-2);
                                        var month = date.toLocaleString('default', {
                                            month: 'short'
                                        });
                                        var year = date.getFullYear();
                                        return `${day}-${month}-${year}`;
                                    }
                                    return data;
                                }
                            },
                            {
                                "data": "kd_buyer_order"
                            },
                            {
                                "data": "no_model"
                            },
                            {
                                "data": "no_order"
                            },
                            {
                                "data": "machinetypeid"
                            },
                            {
                                "data": "product_type"
                            },
                            {
                                "data": "description"
                            },
                            {
                                "data": "seam"
                            },
                            {
                                "data": "color"
                            },
                            {
                                "data": "factory"
                            },
                            {
                                "data": "qty",
                                "render": function(data, type, row) {
                                    if (data) {
                                        return parseFloat(data).toLocaleString('en-US', {
                                            minimumFractionDigits: 0
                                        });
                                    }
                                    return data;
                                }
                            },
                            {
                                "data": "sisa",
                                "render": function(data, type, row) {
                                    if (data) {
                                        return parseFloat(data).toLocaleString('en-US', {
                                            minimumFractionDigits: 0
                                        });
                                    }
                                    return data;
                                }
                            },
                            {
                                "data": "delivery",
                                "render": function(data, type, row) {
                                    if (data) {
                                        // Parse the date and format it as d-M-Y
                                        var date = new Date(data);
                                        var day = ('0' + date.getDate()).slice(-2);
                                        var month = date.toLocaleString('default', {
                                            month: 'short'
                                        });
                                        var year = date.getFullYear();
                                        return `${day}-${month}-${year}`;
                                    }
                                    return data;
                                }
                            },
                            {
                                "data": "start_mc",
                                "render": function(data, type, row) {
                                    if (!data) return data;

                                    const startDate = new Date(data);
                                    const day = ('0' + startDate.getDate()).slice(-2);
                                    const month = startDate.toLocaleString('default', {
                                        month: 'short'
                                    });
                                    const year = startDate.getFullYear();
                                    const formatted = `${day}-${month}-${year}`;

                                    // 🔹 Hitung selisih absolut hari antara start_mc dan tgl_schedule
                                    if (row.tgl_schedule) {
                                        const scheduleDate = new Date(row.tgl_schedule);
                                        const diffDays = Math.abs((scheduleDate - startDate) / (1000 * 60 * 60 * 24));

                                        if (diffDays < 7) {
                                            return `<span style="color:red; font-weight:bold;" title="Jarak ${diffDays.toFixed(0)} hari">${formatted}</span>`;
                                        }
                                    }

                                    return formatted;
                                }
                            },
                            {
                                "data": "tgl_schedule",
                                "render": function(data, type, row) {
                                    if (!data) return data;

                                    const scheduleDate = new Date(data);
                                    const day = ('0' + scheduleDate.getDate()).slice(-2);
                                    const month = scheduleDate.toLocaleString('default', {
                                        month: 'short'
                                    });
                                    const year = scheduleDate.getFullYear();
                                    const formatted = `${day}-${month}-${year}`;

                                    // 🔹 Hitung selisih absolut hari antara tgl_schedule dan start_mc
                                    if (row.start_mc) {
                                        const startDate = new Date(row.start_mc);
                                        const diffDays = Math.abs((scheduleDate - startDate) / (1000 * 60 * 60 * 24));

                                        if (diffDays < 7) {
                                            return `<span style="color:red; font-weight:bold;" title="Jarak ${diffDays.toFixed(0)} hari">${formatted}</span>`;
                                        }
                                    }

                                    return formatted;
                                }
                            },
                            {
                                "data": "repeat"
                            },
                            {
                                "data": null,
                                "render": customRender
                            }
                        ],
                        "order": []
                    });

                    function customRender(data, type, row) {
                        if (row.qty === null) {
                            return `<button type="button" class="btn import-btn btn-success text-xs" data-toggle="modal" data-target="#importModal" data-id="${row.id_model}" data-no-model="${row.no_model}">
                            Import
                        </button>`;
                        } else {
                            return `
                        <a href="<?= base_url($role . '/detailPdkAps') ?>/${row.no_model}/${row.factory}">
                            <button type="button" class="btn btn-info btn-sm details-btn">Details</button>
                        </a>
                       <button
    type="button"
    class="btn bg-gradient-info btn-sm flowproses-btn"
    data-no-model="${row.no_model}"
    data-delivery="${row.delivery}"
    data-machinetypeid="${row.machinetypeid}">
    Flow Proses
</button>

                        `;
                        }
                    }
                });
            </script>
            <script>
                $(document).on('click', '.flowproses-btn', function() {
                    const model = $(this).data('no-model');
                    const delivery = $(this).data('delivery');
                    const machinetypeid = $(this).data('machinetypeid');

                    $('#import_no_model').val(model);
                    $('#import_delivery').val(delivery);
                    $('#import_needle').val(machinetypeid);

                    const url = `<?= base_url($role . '/flowProses') ?>?mastermodel=` +
                        encodeURIComponent(model) +
                        `&delivery=` +
                        encodeURIComponent(delivery);

                    $('#flowProsesModal').modal('show');
                    // reset konten dengan spinner
                    $('#flowProsesContent').html(`
      <div class="d-flex justify-content-center my-5">
        <div class="spinner-border text-info" style="width: 2.5rem; height: 2.5rem;" role="status">
          <span class="visually-hidden">Loading…</span>
        </div>
      </div>
    `);

                    $.getJSON(url)
                        .done(function(res) {
                            if (!res.flows || res.flows.length === 0) {
                                $('#flowProsesContent').html(
                                    `<p class="text-center text-warning">
               <i class="bi bi-exclamation-triangle-fill me-1"></i>
               No data found for model <strong>${model}</strong>.
             </p>`
                                );
                                return;
                            }

                            let html = `
          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>No Model</th>
                  <th>Size</th>
                  <th>Inisial</th>
                  <th>Delivery</th>
                  <th>Flow Proses</th>
                </tr>
              </thead>
              <tbody>
        `;

                            res.flows.forEach(function(flow) {
                                html += `
            <tr>
              <td>${flow.style.mastermodel}</td>
              <td>${flow.style.size}</td>
              <td>${flow.style.inisial}</td>
              <td>${
                flow.style.delivery
                  ? new Date(flow.style.delivery).toLocaleDateString(
                      'id-ID', {
                          day: '2-digit',
                          month: 'short',
                          year: 'numeric'
                      }) 
                    : 'N/A'
              }</td>
              <td>
          `;
                                // tampilkan setiap step dengan badge
                                flow.flow_proses.forEach(function(fp) {
                                    html += `<span class="badge bg-info me-1">
                       ${fp.step_order}. ${fp.master_proses.nama_proses}
                     </span>`;
                                });
                                html += `</td></tr>`;
                            });

                            html += `
              </tbody>
            </table>
          </div>
        `;

                            $('#flowProsesContent').html(html);
                        })
                        .fail(function() {
                            $('#flowProsesContent').html(`
                   <h4 class="text-center text-danger" > Flow Prosses ${model} Belum Di Input</h4>
        `);
                        });
                });
            </script>

            <?php $this->endSection(); ?>