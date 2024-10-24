<?php $this->extend($role . '/Mesin/detailMesinArea'); ?>
<?php $this->section('capacity'); ?>
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
                        <div class="row">
                            <h5>
                                Capacity Area <?= $area ?> Jarum <?= $jarum ?>
                            </h5>
                            <div class="row">
                                <div class="col-lg-4"> Maximum Capacity</div>
                                <div class="col-lg-6">: <?= $max ?> dz / Week</div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4"> Total Mesin</div>
                                <div class="col-lg-6">: <?= $headerData['totalmesin'] ?> Mesin</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <form action="<?= base_url($role . '/capacityperarea/' . $area) ?>" method="post">
                                    <select name="jarum" id="jarum" class='form-control'>Pilih
                                        <option value="">Pilih Jarum</option>
                                        <?php foreach ($listjarum as $jrm) : ?>
                                            <option value="<?= $jrm ?>"><?= $jrm ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="number" name="target" id="" class="form-control" placeholder="target">
                            </div>

                            <div class="col-lg-12 mt-2">
                                <button type="sumbit" class="btn  bg-gradient-info form-control">
                                    Ganti Jarum
                                </button>

                                </form>
                            </div>


                        </div>
                    </div>
                </div>

            </div>

            <div class="card mt-2">
                <div class="card-header">
                    <h4>Capacity Area Per Week</h4>

                </div>
                <div class="card-body">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Week</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Available Capacity</th>
                                <th>Available Machines</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($calendar as $weekData) : ?>
                                <tr>
                                    <td><?= $weekData['week'] ?></td>
                                    <td><?= $weekData['start_date'] ?></td>
                                    <td><?= $weekData['end_date'] ?></td>
                                    <td><?= $weekData['available_capacity'] ?> dz</td>
                                    <td><?= ceil($weekData['available_machines']) ?> Machine</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </table>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header">

                    <h5>PDK in Area</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>PDK</th>
                                <th>Sisa</th>
                                <th>Delivery</th>
                                <th>Leadtime</th>
                                <th>Target Mesin</th>
                                <th>Kebutuhan Mesin</th>
                                <th>Produksi Perhari</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderWeek as $order) : ?>
                                <tr>
                                    <td><?= $order['PDK'] ?></td>
                                    <td><?= $order['sisa'] ?> dz</td>
                                    <td><?= $order['delivery'] ?></td>
                                    <td><?= $order['leadtime'] ?> days</td>
                                    <td><?= $order['targetPerMesin'] ?> dz/machine</td>
                                    <td><?= round($order['kebMesin']) ?> Machine</td>
                                    <td><?= $order['produksi'] ?> dz</td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>




        <!-- <script>
            function valildasi() {
                let qty = parseInt(document.getElementById("qty").value);
                let sisa = parseInt(document.getElementById("sisa").value);

                if (sisa > qty) {
                    alert("Qty tidak boleh melebihi sisa!");
                    document.getElementById("sisa").value = qty; // Reset nilai qty menjadi nilai sisa
                }
            }
        </script> -->
        <script>
            $(document).ready(function() {
                        $('#dataTable').DataTable({
                            "footerCallback": function(row, data, start, end, display) {
                                var api = this.api();

                                var totalMesin = api.column(2, {
                                    page: 'current'
                                }).data().reduce(function(a, b) {
                                    return parseInt(a) + parseInt(b);
                                }, 0);

                                // Calculate the total of the 5th column (Remaining Qty in dozens) - index 4
                                var mesinJalan = api.column(4, {
                                    page: 'current'
                                }).data().reduce(function(a, b) {
                                    return parseInt(a) + parseInt(b);
                                }, 0);

                                var mesinMati = totalMesin - mesinJalan;

                                // Format totalMesin and mesinJalan with " Mc" suffix and dots for thousands
                                var totalMesinFormatted = numberWithDots(totalMesin) + " Mc";
                                var mesinJalanFormatted = numberWithDots(mesinJalan) + " Mc";
                                var mesinMatiFormatted = numberWithDots(mesinMati) + " Mc";

                                // Update the footer cell for the total Qty
                                $(api.column(2).footer()).html(totalMesinFormatted);

                                // Update the footer cell for the total Mesin Jalan
                                $(api.column(4).footer()).html(mesinJalanFormatted);

                                // Update the footer cell for the percentage
                                $(api.column(5).footer()).html(mesinMatiFormatted);
                            },
                        });

                        function numberWithDots(x) {
                            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>