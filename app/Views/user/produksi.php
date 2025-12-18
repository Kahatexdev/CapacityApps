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