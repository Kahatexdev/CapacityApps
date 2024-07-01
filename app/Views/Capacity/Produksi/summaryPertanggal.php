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
                            Summary Produksi Per Tanggal  <?= $title ?> 
                        </h5>
                        <div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="table" class="display compact striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" colspan="7">Tanggal</th>
                                        <?php foreach ($tglProdUnik as $tgl_produksi) : ?>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" colspan="2"><?= date('d-M', strtotime($tgl_produksi)) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Needle</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Style Size</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty PO (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Running</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Prod</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Jl Mc</th>
                                        <?php foreach ($tglProdUnik as $tgl_produksi) : ?>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Prod (dz)</td>
                                            <td class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jl Mc</td>
                                        <?php endforeach; ?>
                                        <!-- <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Prod</th> -->
                                        <!-- <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jl Mc</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($uniqueData as $id) : ?>                                            
                                        <tr>
                                            <td class="text-sm"><?= $id['machinetypeid']; ?></td>
                                            <td class="text-sm"><?= $id['mastermodel']; ?></td>
                                            <td class="text-sm"><?= $id['size']; ?></td>
                                            <td class="text-sm"><?= number_format($id['qty'],2); ?></td>
                                            <td class="text-sm"><?= $id['running']; ?> days</td>
                                            <td class="text-sm"><?= number_format($id['ttl_prod'],2); ?></td>
                                            <td class="text-sm"><?= $id['ttl_jlmc']; ?></td>
                                            <?php foreach ($tglProdUnik as $tgl_produksi2) : ?>
                                                <?php
                                                $qty_produksi = 0;
                                                $jl_mc = 0;
                                                foreach ($dataSummaryPertgl as $data) {
                                                    if ($id['machinetypeid'] == $data['machinetypeid'] && 
                                                        $id['mastermodel'] == $data['mastermodel'] && 
                                                        $id['size'] == $data['size'] && 
                                                        $tgl_produksi2 == $data['tgl_produksi']) {
                                                        $qty_produksi = $data['qty_produksi'];
                                                        $jl_mc = $data['jl_mc'];
                                                        break; // Keluar dari loop begitu data ditemukan
                                                    }
                                                }
                                                ?>
                                                <td class="text-sm"><?= $qty_produksi; ?></td>
                                                <td class="text-sm"><?= $jl_mc; ?></td>  
                                                <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th>Total :</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                    </div>
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

                $('.btn-add').click(function() {
                    $('#modalTambah').find('form').attr('action', '<?= base_url($role . '/tambahmesinperarea/') ?>');

                    $('#modalTambah').modal('show'); // Show the modal
                });

                $('.edit-btn').click(function() {
                    var id_data_mesin = $(this).data('id');
                    var area = $(this).data('area');
                    var jarum = $(this).data('jarum');
                    var total_mc = $(this).data('total');
                    var brand = $(this).data('brand');
                    var mesin_jalan = $(this).data('mc-jalan');
                    var pu = $(this).data('pu');
                    var mesin_mati = total_mc - mesin_jalan;

                    $('#ModalEdit').find('form').attr('action', '<?= base_url($role . '/updatemesinperjarum/') ?>' + id_data_mesin);
                    $('#ModalEdit').find('input[name="id"]').val(id_data_mesin);
                    $('#ModalEdit').find('input[name="area"]').val(area);
                    $('#ModalEdit').find('input[name="jarum"]').val(jarum);
                    $('#ModalEdit').find('input[name="total_mc"]').val(total_mc);
                    $('#ModalEdit').find('input[name="brand"]').val(brand);
                    $('#ModalEdit').find('input[name="mesin_jalan"]').val(mesin_jalan);
                    $('#ModalEdit').find('input[name="mesin_mati"]').val(mesin_mati);
                    if (pu === "CJ") {
                        $('#cj_radio').prop('checked', true);
                    } else if (pu === "MJ") {
                        $('#mj_radio').prop('checked', true);
                    }

                    $('#ModalEdit').modal('show'); // Show the modal
                });
                $('.delete-btn').click(function() {
                    var id = $(this).data('id');
                    $('#ModalDelete').find('form').attr('action', '<?= base_url($role . '/deletemesinareal/') ?>' + id);
                    $('#ModalDelete').find('input[name="id_data_mesin"]').val(id);
                    $('#ModalDelete').modal('show'); // Show the modal
                });

            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>