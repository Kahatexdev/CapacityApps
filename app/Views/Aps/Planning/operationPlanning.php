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
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Planning Order for area <?= $area ?> needle <?= $jarum ?> Total <?= $mesin ?> Machine
                        </h5>
                        <div class="div">
                            <a href="<?= base_url($role . '/detailplnmc/' . $id_pln) ?>" class="btn btn-secondary ml-auto">Back</a>
                            <a href="<?= base_url($role . '/cekBahanBaku/' . $id_save . '/' . $id_pln); ?>" class="btn btn-info">Cek Bahan Baku</a>
                        </div>
                    </div>

                </div>
                <div class="card-body p-3" id="planningField">
                    <form action="<?= base_url($role . '/saveplanning'); ?>" id="formPlanning" method="post">

                        <input class="form-control" type="text" name="id_est" value="" readonly id="id-est" hidden>
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="form-control-label">No Model</label>
                                    <input class="form-control" type="text" name="model" value="<?= $pdk ?>" readonly id="model-data">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="form-control-label">Delivery</label>

                                    <select name="delivery" id="delivery" class="form-control">
                                        <option value="null">Pilih Delivery</option>
                                        <?php foreach ($listDeliv as $deliv): ?>
                                            <option value="<?= $deliv['delivery'] ?>"> <?= $deliv['delivery'] ?></option>
                                        <?php endforeach ?>
                                    </select>

                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="form-control-label">Qty</label>
                                    <input class="form-control" type="text" name="qty" value="" readonly id="qty">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="form-control-label">Remaining Qty</label>
                                    <input class="form-control" type="text" name="sisa" value="" readonly id="remaining-qty">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="form-control-label">Target 100%</label>
                                    <input class="form-control" type="text" name="targetawal" value="" readonly id="target-100">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group row">
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="" class="form-control-label">Percentage</label>
                                        <div class="input-group">
                                            <input class="form-control" type="number" name="persen_target" value="80" readonly id="percentage" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="" class="form-control-label"><span style="color: orange">Target Aktual</span></label>
                                        <input class="form-control" type="text" name="target_akhir" value="0" id="calculated-target" oninput="updatePercentage()">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group row">
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="" class="form-control-label">Start
                                            <span id="available_machine" class="ml-2">(Available : 0)</span>
                                        </label>
                                        <div class="input-group">
                                            <?php
                                            // Get today's date
                                            $todayDate = date('Y-m-d');
                                            ?>
                                            <input class="form-control" type="date" name="start_date" value="<?= $todayDate ?>" id="start-date">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="" class="form-control-label">Stop</label>

                                        <div class="input-group">
                                            <input class="form-control stop-date" type="date" name="stop_date" value="" id="stop-date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group row">
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="" class="form-control-label"><span style="color: orange">Days</span> (Exclude Holidays)</label>
                                            <input class="form-control days-count" type="number" name="days_count" readonly id="days-count">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="" class="form-control-label">Holiday Count</label>
                                        <div class="input-group">
                                            <input class="form-control holiday-count" type="number" name="holiday_count" oninput="updateLibur()" id="holiday-count">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group row">
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="" class="form-control-label">Unplanned Qty</label>
                                        <div class="input-group">
                                            <input class="form-control holiday-count" type="number" name="unplanned_qty" readonly id="unplanned-qty">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="" class="form-control-label">
                                            <span style="color: orange">Machines Usages</span>
                                            <span id="machine_suggestion" class="ml-2">(Suggested: 0)</span>
                                        </label>
                                        <input class="form-control" type="number" id="machine_count" name="machine_usage" oninput="calculateEstimatedQty()" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="form-control-label">Estimated Qty <span style="color: orange">(Target x Days x Machine Usage)</span></label>
                                    <input class="form-control estimated-qty" type="number" value="" name="estimated_qty" id="estimated-qty" readonly>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="" class="form-control-label">Keterangan </label>
                                    <input class="form-control " type="text" value="" name="keterangan" id="keterangan">
                                </div>
                            </div>
                        </div>


                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="id_save" value=<?= $id_save ?>>
                            <input type="hidden" name="id_pln" value=<?= $id_pln ?>>
                            <input type="hidden" name="mesin" value=<?= $mesin ?>>
                            <input type="hidden" name="area" value=<?= $area ?>>
                            <input type="hidden" name="jarum" value=<?= $jarum ?>>
                            <input type="hidden" name="judul" value=<?= $judul ?>>
                            <button type="submit" id="saveEditPlan" class="btn btn-primary btn-block d-none" style="width: 100%;">Edit Planning</button>
                            <button type="submit" id="savePlan" class="btn btn-info btn-block" style="width: 100%;">Save Planning</button>
                        </div>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header  d-flex  justify-content-between">
                    <h5>Detail Planning for Model <?= $pdk ?></h5>
                    <button class="btn btn-warning btn-plan" id="planStyle" onclick="planStyle()"
                        data-pdk="<?= $pdk ?>"
                        data-jarum="<?= $jarum ?>"
                        data-area="<?= $area ?>">
                        <span class="text-sm">
                            Plan
                        </span>
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Delivery</th>
                                    <th>Start Machine</th>
                                    <th>Stop Machine</th>
                                    <th>Precentage of Target</th>
                                    <th>Target</th>
                                    <th>Days</th>
                                    <th>Machine</th>
                                    <th>keterangan</th>
                                    <th>Estimated Production</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $totalEstQtyByDelivery = []; // To hold total Estimated Production per delivery
                                $listPlanningEmpty = empty($listPlanning); // Check if $listPlanning is empty
                                foreach ($listPlanning as $order) :
                                    $delivery = $order['delivery'];
                                    $estQty = $order['Est_qty']; // Estimated Production

                                    // Sum Estimated Production per delivery
                                    if (!isset($totalEstQtyByDelivery[$delivery])) {
                                        $totalEstQtyByDelivery[$delivery] = 0;
                                    }
                                    $totalEstQtyByDelivery[$delivery] += $estQty;
                                ?>
                                    <tr>
                                        <td style="text-align: center; vertical-align: middle;"><?= $no++; ?></td>
                                        <input type="text" name="" id="estId-<?= $no ?>" value="<?= $order['id_est_qty'] ?>" hidden>
                                        <input type="text" name="" id="detailId-<?= $no ?>" value="<?= $order['id_detail_pln'] ?>" hidden>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= date('d-M-Y', strtotime($order['delivery'])); ?></td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= date('d-M-Y', strtotime($order['start_date'])); ?></td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= date('d-M-Y', strtotime($order['stop_date'])); ?></td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($order['precentage_target']); ?> %</td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($order['target']); ?> Dz</td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($order['hari']); ?> Days</td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($order['mesin']); ?> Mc</td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= $order['keterangan']; ?> </td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;"><?= number_format($estQty, 0, '.', ','); ?> Dz</td>
                                        <td class="text-sm" style="text-align: center; vertical-align: middle;">
                                            <button class="btn btn-info btn-edit" id="editPlan-<?= $no ?>" onclick="editPlan(<?= $no ?>)"
                                                data-start="<?= $order['start_date'] ?>"
                                                data-stop="<?= $order['stop_date'] ?>"
                                                data-delivery="<?= $order['delivery'] ?>"
                                                data-targetActual="<?= $order['target'] ?>"
                                                data-mc="<?= $order['mesin'] ?>"
                                                data-days="<?= $order['hari']; ?>"
                                                data-idEst="<?= $order['id_est_qty']; ?>">
                                                Edit
                                            </button>

                                            <button class="btn btn-danger btn-update" data-toggle="modal" data-target="#modalUpdate"
                                                data-start="<?= $order['start_date'] ?>"
                                                data-idplan="<?= $order['id_detail_pln'] ?>"
                                                data-idpl="<?= $id_pln ?>"
                                                data-stop="<?= $order['stop_date'] ?>">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <?php foreach ($totalEstQtyByDelivery as $delivery => $totalEstQty) : ?>
                                    <tr>
                                        <th colspan="9" style="text-align: right;">Total Estimated Production <?= htmlspecialchars($delivery) ?>:</th>
                                        <th style="text-align: center; vertical-align: middle;" id="total-est-qty-<?= htmlspecialchars($delivery) ?>">
                                            <?= number_format($totalEstQty ?? 0, 0, '.', ','); ?> Dz
                                        </th>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <th colspan="9" style="text-align: right;">Total Est Full Shipment :</th>
                                    <th style="text-align: center; vertical-align: middle;" id="totalFull">
                                        0 Dz
                                    </th>
                                </tr>
                            </tfoot>

                            <!-- Add a hidden input or data attribute to pass the empty state -->
                            <input type="hidden" id="list-planning-empty" value="<?= $listPlanningEmpty ? 'true' : 'false'; ?>">

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4 planStyleCard d-none">
        <div class="col-md-12">
            <div class="card  ">
                <div class="card-header">
                    <h4 class="text-header headerPlan">
                        Plan Machine
                    </h4>
                    <div class="row headerText">

                    </div>
                </div>
                <form action="<?= base_url($role . '/savePlanStyle') ?>" method="post">
                    <div class="card-body planDetail">
                    </div>
                    <div class="card-footer">
                        <div class="row">

                            <button type="submit" class="btn btn-info "> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Modal for Deleting -->
    <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdate" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Plan Mesin</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-lg-12 col-sm-6">
                                <div class="form-group">
                                    <input type="text" name="id" hidden>
                                    <input type="text" name="idpl" hidden>
                                    Anda yakin ingin menghapus?
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn bg-gradient-danger">Hapus Data</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".btn-plan").forEach(button => {
                button.addEventListener("click", function() {
                    let planStyleCard = document.querySelector(".planStyleCard");
                    const jarum = <?= json_encode($jarum); ?>;
                    const area = <?= json_encode($area); ?>;
                    const pdk = <?= json_encode($pdk); ?>;
                    $.ajax({
                        url: '<?= base_url("aps/getPlanStyle") ?>',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            jarum: jarum,
                            area: area,
                            pdk: pdk,

                        },
                        success: function(response) {
                            if (response) {
                                console.log(response)
                                document.querySelector(".headerPlan").textContent = 'Plan Mesin perStyle ' + pdk;
                                document.querySelector(".headerText").innerHTML = `
                                    <div class="col-md-12">
                                      
                                    </div>
                                `;
                                let planHtml = `
        <table id="planTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Inisial</th>
                    <th>Style</th>
                    <th>Qty</th>
                    <th>Sisa</th>
                    <th>Mesin</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                ${response.data.map(item => `
                    <tr>
                        <td>${item.inisial}</td>
                        <td>${item.style}
                            <input type="hidden" value="${item.idAps}" name="idAps[]">
                        </td>
                           <td>${item.qty} dz</td>
                           <td>${item.sisa} dz</td>
                        <td>
                            <input type="number" class="form-control" value="${item.mesin ?? '0'}" name="mesin[]">
                        </td>
                        <td>
                            <input type="text" class="form-control" value="${item.keterangan ?? ''}" name="keterangan[]">
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
          
    `;

                                document.querySelector(".planDetail").innerHTML = planHtml;
                                document.querySelector(".planStyleCard").classList.toggle("d-none");

                                // **Aktifkan DataTables setelah tabel dirender**
                                $('#planTable').DataTable({
                                    paging: true, // Pagination aktif
                                    searching: true, // Bisa cari data
                                    ordering: true, // Bisa sort kolom
                                    lengthMenu: [
                                        [5, 10, 25, -1],
                                        [5, 10, 25, "All"]
                                    ], // Dropdown jumlah data
                                    language: {
                                        search: "Cari:",
                                        lengthMenu: "Tampilkan _MENU_ data",
                                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                                        paginate: {
                                            previous: "Sebelumnya",
                                            next: "Berikutnya"
                                        }
                                    }
                                });
                            } else {
                                console.error('Error: Response format invalid.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', error);
                        }
                    });

                });
            });
        });
        $(document).ready(function() {
            $("#dataTable").DataTable().destroy();

            var table = $('#dataTable').DataTable({

                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api();

                    // Menghitung total Estimated Production per halaman
                    var total = api.column(9, {
                        page: 'current'
                    }).data().reduce(function(acc, val) {
                        var num = parseFloat(val.replace(/[^\d.-]/g, '')); // Ekstrak angka dari string
                        return acc + (isNaN(num) ? 0 : num);
                    }, 0);

                    // Update Total Estimated Production di halaman saat ini
                    $(api.column(9).footer()).text(total.toLocaleString('en', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }) + ' Dz');

                    // Menghitung Grand Total dari seluruh halaman
                    var grandTotal = api.column(9).data().reduce(function(acc, val) {
                        var num = parseFloat(val.replace(/[^\d.-]/g, ''));
                        return acc + (isNaN(num) ? 0 : num);
                    }, 0);

                    // Update Grand Total
                    $('#totalFull').text(grandTotal.toLocaleString('en', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }) + ' Dz');
                }
            });
        });



        function editPlan(no) {
            let button = document.getElementById('editPlan-' + no);

            // var estId = document.getElementById('estId-' + no).value;
            let estId = button.getAttribute('data-idEst');
            let startMc = button.getAttribute('data-start');
            let stopMc = button.getAttribute('data-stop');
            let target = button.getAttribute('data-targetActual');
            let deliv = button.getAttribute('data-delivery');
            let days = button.getAttribute('data-day');
            let mc = button.getAttribute('data-mc');
            let saveButton = document.getElementById('savePlan');
            let editButton = document.getElementById('saveEditPlan')

            $('#planningField').find('form').attr('action', '<?= base_url($role . '/updatePlanning/') ?>' + estId);
            const deliverySelect = $('#planningField').find('select[name="delivery"]');
            deliverySelect.val(deliv); // Set nilai delivery
            deliverySelect.trigger('change'); // Panggil event change secara manual
            $('#planningField').find('input[name="id_est"]').val(estId);
            $('#planningField').find('input[name="start_date"]').val(startMc);
            $('#planningField').find('input[name="stop_date"]').val(stopMc);
            $('#planningField').find('input[name="days_count"]').val(days);
            $('#planningField').find('input[name="machine_usage"]').val(mc);

            const targetField = $('#planningField').find('input[name="target_akhir"]');
            targetField.val(target);

            // Trigger input event to update percentage
            const event = new Event('input', {
                bubbles: true
            });
            targetField[0].dispatchEvent(event); // Pastikan targetField adalah elemen DOM, bukan objek jQuery

            saveButton.classList.add('d-none');
            editButton.classList.remove('d-none');


        }



        $(document).on('change', '#delivery', function() {
            $('#planningField').find('form').attr('action', '<?= base_url($role . '/saveplanning'); ?>');
            const jarum = <?= json_encode($jarum); ?>;
            const area = <?= json_encode($area); ?>;
            const unplan = document.getElementById('unplanned-qty')
            unplan.value = ''
            const start = document.getElementById('start-date')
            const deliv = this.value; // Ambil nilai yang dipilih dari select
            const model = document.getElementById('model-data').value; // Ambil nilai dari input model

            // Konversi delivery menjadi objek Date
            const deliveryDate = new Date(deliv);
            var saveButton = document.getElementById('savePlan');
            var saveButton = document.getElementById('savePlan');
            var editButton = document.getElementById('saveEditPlan');
            saveButton.disabled = false
            saveButton.textContent = 'Save Planning';
            saveButton.classList.remove('d-none');
            editButton.classList.add('d-none');

            // Tentukan tanggal minimum (hari ini + 3 hari)
            const today = new Date();
            const minDate = new Date(today); // Salin tanggal hari ini
            minDate.setDate(today.getDate() + 3); // Tambahkan 3 hari ke tanggal hari ini
            start.value = today
            // Validasi apakah delivery kurang dari tanggal minimum
            if (deliveryDate < minDate) {
                alert(`Delivery yang dipilih tidak valid. Silakan pilih tanggal delivery lebih dari tanggal ${minDate.toISOString().split('T')[0]}.`);
                return; // Hentikan jika tidak valid
            }

            if (deliv !== "null") {
                $.ajax({
                    url: '<?= base_url("aps/getModelData") ?>',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        model: model,
                        delivery: deliv,
                        jarum: jarum,
                        area: area
                    },
                    success: function(response) {
                        if (response) {
                            const stopDate = document.getElementById('stop-date');
                            const target100 = document.getElementById('target-100');
                            const qty = document.getElementById('qty');
                            const remainingQty = document.getElementById('remaining-qty');

                            if (stopDate && target100 && qty && remainingQty) {
                                const formattedDate = new Date(deliv);
                                formattedDate.setDate(formattedDate.getDate() - 7);

                                stopDate.value = formattedDate.toISOString().split('T')[0];
                                target100.value = response.smv;
                                qty.value = response.qty;
                                remainingQty.value = response.sisa;
                            } else {
                                console.error('Error: One or more HTML elements not found.');
                            }
                        } else {
                            console.error('Error: Response format invalid.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            } else {
                alert('Silakan pilih delivery yang valid.');
            }
        });



        function calculateTarget() {
            var percentageInput = document.getElementById('percentage');
            var target100Input = document.getElementById('target-100');
            var target100 = parseFloat(target100Input.value);
            var percentage = parseFloat(percentageInput.value) || 80; // Default 80% jika kosong atau tidak valid

            // Validasi persentase antara 50 dan 100, jika tidak, set ke 80%
            if (isNaN(percentage) || percentage < 50 || percentage > 100) {
                percentage = 80;
                percentageInput.value = percentage; // Set persentase ke 80% sebagai default
            }

            // Hitung target akhir
            var calculatedTarget = (target100 * (percentage / 100)).toFixed(2);
            document.getElementById('calculated-target').value = calculatedTarget;

            fillMachineSuggestion();
        }

        // Tambahkan event listener untuk perubahan target_akhir

        function updatePercentage() {
            var target100Input = document.getElementById('target-100');
            var target100 = parseFloat(target100Input.value);
            var calculatedTargetInput = document.getElementById('calculated-target');
            var calculatedTarget = parseFloat(calculatedTargetInput.value);

            if (!isNaN(target100) && target100 > 0 && !isNaN(calculatedTarget)) {
                var newPercentage = ((calculatedTarget / target100) * 100).toFixed(2);

                // Update persentase di input persentase
                document.getElementById('percentage').value = newPercentage;
            } else {
                console.log('Invalid values for calculation');
            }
        }




        $(document).on('click', '.btn-update', function() {
            var idplan = $(this).data('idplan');

            var idpl = $(this).data('idpl');
            $('#modalUpdate').find('form').attr('action', '<?= base_url($role . '/deleteplanmesin') ?>');
            $('#modalUpdate').find('input[name="id"]').val(idplan);
            $('#modalUpdate').find('input[name="idpl"]').val(idpl);




            $('#modalUpdate').modal('show'); // Show the modal
        });

        function initCalculations() {

            calculateDaysCount(function() {
                fillUnplannedQty();
                fillMachineSuggestion();
                var startDate = document.querySelector('input[name="start_date"]').value;
                updateAvailableMachines(startDate); // Call updateAvailableMachines with the start date
            });
        }

        function calculateDaysCount(callback) {
            var startDateString = document.querySelector('input[name="start_date"]').value;
            var stopDateString = document.querySelector('.stop-date').value;
            var startDate = new Date(startDateString);
            var stopDate = new Date(stopDateString);
            var isoStartDate = startDate.toISOString().split('T')[0];
            var isoStopDate = stopDate.toISOString().split('T')[0];

            $.ajax({
                url: '<?php echo base_url("aps/getDataLibur") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    startDate: isoStartDate,
                    endDate: isoStopDate
                },
                success: function(response) {
                    if (response.status == 'success') {
                        var totalHolidays = response.total_libur;
                        var totalDays = ((stopDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                        if (totalDays < 1) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Dates',
                                text: 'Stop date and start date are invalid.',
                            }).then((result) => {
                                var deliveryDate = new Date(document.getElementById('delivery').value);
                                var newStopDate = new Date(deliveryDate.getTime() - (3 * 24 * 60 * 60 * 1000)); // 3 days before delivery
                                var newStartDate = new Date(newStopDate.getTime() - (7 * 24 * 60 * 60 * 1000)); // 7 days before delivery

                                document.querySelector('.stop-date').value = newStopDate.toISOString().split('T')[0];
                                document.querySelector('.start-date').value = newStartDate.toISOString().split('T')[0];
                                calculateDaysCount(function() {
                                    fillMachineSuggestion();
                                    fillUnplannedQty();
                                });
                            });
                        }
                        var daysWithoutHolidays = totalDays - totalHolidays;

                        document.querySelector('.days-count').value = daysWithoutHolidays;
                        document.querySelector('.holiday-count').value = totalHolidays;

                        calculateEstimatedQty();

                        if (typeof callback === 'function') {
                            callback();
                        }
                    } else {
                        console.error('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + error);
                }
            });
        }

        function updateLibur() {
            var startDateString = document.querySelector('input[name="start_date"]').value;
            var stopDateString = document.querySelector('.stop-date').value;
            var startDate = new Date(startDateString);
            var stopDate = new Date(stopDateString);
            var isoStartDate = startDate.toISOString().split('T')[0];
            var isoStopDate = stopDate.toISOString().split('T')[0];
            var totalDays = ((stopDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
            var holidays = document.querySelector('.holiday-count').value
            var daysWithoutHolidays = totalDays - holidays;

            document.querySelector('.days-count').value = daysWithoutHolidays;
            document.querySelector('.holiday-count').value = holidays;
            fillUnplannedQty()
            fillMachineSuggestion();

        }

        function calculateEstimatedQty() {
            var daysCount = parseFloat(document.querySelector('.days-count').value);
            var machineCount = parseFloat(document.getElementById('machine_count').value);
            var targetPercentageInput = document.querySelector('[id^="calculated-target"]').value;
            var targetPercentage = parseFloat(targetPercentageInput.split(' ')[0]);

            if (!isNaN(daysCount) && !isNaN(machineCount) && !isNaN(targetPercentage)) {
                var estimatedQty = daysCount * machineCount * targetPercentage;
                document.querySelector('.estimated-qty').value = estimatedQty.toFixed(2);
            }
        }

        function fillMachineSuggestion() {
            var daysCount = parseFloat(document.querySelector('.days-count').value);
            var targetPercentageInput = document.querySelector('[id="calculated-target"]').value;
            var targetPercentage = parseFloat(targetPercentageInput.split(' ')[0]);

            var remainingQty = parseFloat(document.querySelector('[id^="unplanned-qty"]').value);
            var machineSuggestion = remainingQty / daysCount / targetPercentage;
            if (machineSuggestion < 0) {
                machineSuggestion = 0; // Set machineSuggestion to 0
            }
            document.getElementById('machine_suggestion').innerText = "(Suggested: " + machineSuggestion.toFixed(2) + " Mc)";

        }

        function fillUnplannedQty() {
            // Ambil nilai delivery dari input dengan ID 'delivery'
            let deliv = document.getElementById('delivery').value.replace(/\s+/g, '-'); // Ganti spasi dengan '-'
            console.log("Delivery Value:", deliv);

            let listPlanningEmpty = document.getElementById('list-planning-empty').value === 'true';

            // Ambil nilai remainingQty dari input dengan ID 'remaining-qty'
            var remainingQty = parseFloat(document.querySelector('[id="remaining-qty"]').value) || 0;

            // Jika listPlanning kosong, set unplanned-qty langsung dengan remainingQty
            if (listPlanningEmpty) {
                document.getElementById('unplanned-qty').value = remainingQty.toFixed(2);
                var saveButton = document.getElementById('savePlan');
                saveButton.disabled = false;
                saveButton.textContent = 'Save Planning';
                return; // Keluar dari fungsi jika listPlanning kosong
            }

            // Cari elemen total-est-qty berdasarkan ID delivery
            var totalEstQtyElem = document.getElementById('total-est-qty-' + deliv);

            if (!totalEstQtyElem) {
                document.getElementById('unplanned-qty').value = remainingQty.toFixed(2);
                console.error('Element with ID total-est-qty-' + deliv + ' not found.');
                return; // Keluar dari fungsi jika elemen tidak ditemukan
            }

            // Ambil nilai dari elemen dan parse sebagai angka
            var totalEstQty = parseFloat(totalEstQtyElem.innerText.replace(/[^\d.-]/g, '')) || 0;

            // Hitung Unplanned Qty
            var unplannedQty = Math.ceil(remainingQty - totalEstQty);
            document.getElementById('unplanned-qty').value = unplannedQty.toFixed(2);

            // Ubah status tombol 'Save'
            var saveButton = document.getElementById('savePlan');
            if (unplannedQty <= 0) {
                console.log(unplannedQty)
                saveButton.disabled = true;
                saveButton.textContent = 'Qty Has Been Planned Successfully';
            } else {
                saveButton.disabled = false;
                saveButton.textContent = 'Save Planning';
            }
        }


        function updateAvailableMachines(date) {
            $.ajax({
                url: '<?= base_url("aps/getMesinByDate/") . $id_pln ?>', // Adjust the URL to pass the ID if needed
                type: 'GET',
                dataType: 'json',
                data: {
                    date: date
                },
                success: function(response) {
                    if (response && response.available !== undefined) {
                        // Calculate the reduced available machines value
                        var reducedAvailableMachines = <?= $mesin ?> - response.available;

                        // Update the HTML element with the new value
                        $('#available_machine').text("(Available : " + reducedAvailableMachines + " )");
                    } else {
                        console.error('Error: Invalid response format.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + error);
                }
            });
        }

        document.querySelector(' input[name="start_date" ]').addEventListener('change', function() {
            var startDate = this.value;
            var unplan = document.getElementById('unplanned-qyt')
            var saveButton = document.getElementById('savePlan');
            updateAvailableMachines(startDate);
            calculateDaysCount(function() {
                fillMachineSuggestion();
                fillUnplannedQty();
                if (unplan.value > 0) {
                    saveButton.disabled = false;
                    saveButton.textContent = 'Save Planning';
                } else {
                    saveButton.disabled = true;
                    saveButton.textContent = 'Qty Has Been Planned Successfully';
                }
            });
        });

        document.querySelector('.stop-date').addEventListener('change', function() {
            calculateDaysCount(function() {
                fillMachineSuggestion();
                fillUnplannedQty();
            });
        });

        var percentageInputs = document.querySelectorAll('input[name="persen_target" ]');
        percentageInputs.forEach(function(input) {
            input.addEventListener('input', function() {

                calculateTarget();
            });

            input.addEventListener('blur', function() {
                fillMachineSuggestion();
            });
        });

        initCalculations();
    </script>

    <?php $this->endSection(); ?>