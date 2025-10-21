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


    <div class="row mt-3">
        <div class="col-xl-12">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5>Planning Area </h5>
                        <a href="<?= base_url($role . '/pps') ?>" class="btn btn-success"> PPS</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- <form id="planningForm" method="POST">
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
                                        foreach ($area as $ar) : ?>
                                            <option value="<?= $ar ?>"><?= $ar ?></option>
                                        <?php endforeach
                                        ?>
                                    </select>
                                </div>
                                <div class=" form-group">
                                    <label for="jarum" class="form-control-label">Jarum</label>
                                    <select class="form-control" id="jarum" name="jarum">
                                        <option value="">Pilih Jarum</option>
                                    </select>
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
                    </form> -->
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="row">
                                <?php foreach ($area as $ar) : ?>
                                    <div class="col-3">
                                        <a href="<?= base_url($role . '/summaryPlanner/' . $ar) ?>" class="btn btn-info w-100">Summary Planner <?= $ar ?></a>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <table id="dataTable" class="display">
                    <thead>

                        <th>Jarum</th>
                        <th>Area</th>
                        <th>Details</th>
                    </thead>
                    <tbody>
                        <?php foreach ($planarea as $order) : ?>
                            <?php foreach ($order as $key => $val) : ?>

                                <tr>
                                    <td><?= $val['jarum'] ?></td>
                                    <td><?= $val['area'] ?></td>
                                    <td>
                                        <a href=" <?= base_url($role . '/detailplnmc/' . $val['id_pln_mc']) ?>" class="btn btn-info">View Details</a>

                                    </td>

                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
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
        // Initialize DataTable
        var table = $('#dataTable').DataTable({
            "order": [
                [3, "desc"]
            ] // Sort by the fourth column (index 3) in descending order (newer first)
        });

        // Trigger import modal when import button is clicked
        $('.import-btn').click(function() {
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
                    url: '<?= base_url($role . '/fetch_jarum') ?>',
                    data: {
                        area: area
                    },
                    success: function(response) {
                        var jarumOptions = response;
                        $('#jarum').empty().append('<option value="">Pilih Jarum</option>');
                        $.each(jarumOptions, function(index, value) {
                            $('#jarum').append('<option value="' + value + '">' + value + '</option>');
                        });
                    }
                });
            } else {
                $('#jarum').empty().append('<option value="">Pilih Jarum</option>');
            }
        });
        $('#planningForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '<?= base_url($role . '/SimpanJudul') ?>',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message
                        });

                        // Update DataTable
                        table.clear().draw();
                        $.each(response.planarea, function(index, item) {
                            // Format date to display month name
                            var date = new Date(item.updated_at);
                            var options = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: 'numeric',
                                minute: 'numeric'
                            };
                            var formattedDate = date.toLocaleDateString('en-US', options);
                            table.row.add([
                                item.judul,
                                item.area,
                                item.jarum,
                                formattedDate,
                                ''
                            ]).draw(false);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again later.'
                    });
                }
            });
        });
    });
</script>

<?php $this->endSection(); ?>