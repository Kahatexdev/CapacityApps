<?php $this->extend('Capacity/layout'); ?>
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


    <div class="row mt-3">
        <div class="col-xl-12">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                    <h5>Planning Area </h5>
                    
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('aps/SimpanJudul') ?>" method="POST">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">

                                <div class="form-group">
                                    <label for="jarum" class="form-control-label">Judul</label>
                                    <input class="form-control" type="text" value="" placeholder="Masukan Judul" required id="judul" name="judul">
                                </div>                                
                                <div class="form-group">
                                    <label for="area" class="form-control-label">Area</label>
                                    <select class="form-control" id="area" name="area">
                                        <option value="">Pilih Area</option>
                                        <?php
                                        foreach ($area as $area) {
                                            echo "<option value=\"$area\">$area</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jarum" class="form-control-label">Jarum</label>
                                    <input class="form-control" type="text" value="" id="jarum" name="jarum">
                                </div>                               
                            </div>

                        </div>
                        <hr>
                        <div class="row">
                            


                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn bg-gradient-info w-100">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        
                    </div>
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
        $('#area').change(function() {
            var area = $(this).val();
            if (area) {
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('aps/fetch_jarum') ?>', // URL to the PHP script that fetches Jarum data
                    data: {area: area},
                    success: function(response) {
                        var jarumOptions = JSON.parse(response);
                        $('#jarum').empty().append('<option value="">Pilih Jarum</option>');
                        $.each(jarumOptions, function(key, value) {
                            $('#jarum').append('<option value="'+ value +'">'+ value +'</option>');
                        });
                    }
                });
            } else {
                $('#jarum').empty().append('<option value="">Pilih Jarum</option>');
            }
        });
    });
</script>

<?php $this->endSection(); ?>