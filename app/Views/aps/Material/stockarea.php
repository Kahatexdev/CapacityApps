<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>

<style>
    :root {
        --primary-color: #2e7d32;
        /* secondary color is abu-abu*/
        --secondary-color: #778899;
        --background-color: #f4f7fa;
        --card-background: #ffffff;
        --text-color: #333333;
    }

    body {
        background-color: var(--background-color);
        color: var(--text-color);
        font-family: 'Arial', sans-serif;
    }

    .container-fluid {
        /* max-width: 1200px; */
        margin: 0 auto;
        padding: 2rem;
    }

    .card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    /* .form-control {
        border: none;
        border-bottom: 2px solid var(--primary-color);
        border-radius: 0;
        padding: 0.75rem 0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    } */

    .form-control:focus {
        box-shadow: none;
        border-color: var(--secondary-color);
    }

    .btn {
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        font-weight: bold;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    .result-card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                    confirmButtonColor: '#4a90e2'
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
                    confirmButtonColor: '#4a90e2'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card">
        <div class="row align-items-center">
            <div class="col-md-6">


                <h3 class="mb-4">Stock Supermarket </h3>
            </div>

        </div>
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table id="dataTable2" class="display compact striped" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">No Karung</th>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2"> Delivery Akhir</th>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2"> No Model</th>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2"> Item Type</th>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2"> Lot</th>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2"> Warna</th>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2"> Kode Warna</th>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2"> KGs</th>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2"> Cones</th>
                                <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2"> tanggal datang </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($stock as $st): ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($st['no_karung']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['delivery']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['no_model']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['item_type']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['lot']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['warna']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['kode_warna']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['kgs_in_out']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['cns_in_out']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['created_at']); ?></td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>


                    </table>
                </div>
            </div>
        </div>

    </div>


</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 35,
            "order": []
        });
        $('#dataTable2').DataTable({
            "pageLength": 35,
            "order": []
        });


    });
</script>
<?php $this->endSection(); ?>