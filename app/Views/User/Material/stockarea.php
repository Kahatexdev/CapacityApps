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


                <h3 class="mb-4">Stock Supermarket <?= $area ?></h3>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-outline-info btn-sm position-relative">
                    Pemasukan
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </button>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-md-6">
                <p><strong>Kapasitas :</strong></p>
                <p><strong>Terisi:</strong></p>
                <p><strong>Sisa Kapasitas:</strong></p>
            </div>

        </div>


        <form method="post" action="">
            <div class="row g-3">
                <div class="col-lg-4 col-sm-12">
                    <input class="form-control" type="text" name="noModel" placeholder="Masukkan No Model / Cluster">
                </div>
                <div class="col-lg-4 col-sm-12">
                    <input class="form-control" type="text" name="warna" placeholder="Masukkan Kode Warna">
                </div>
                <div class="col-lg-4 col-sm-12 d-flex gap-2">
                    <button class="btn btn-info flex-grow-1" id="filter_data"><i class="fas fa-search"></i> Cari</button>
                    <button class="btn btn-secondary flex-grow-1" id="reset_data"><i class="fas fa-redo"></i> Reset</button>
                    <!-- <button type="button" class="btn btn-success flex-grow-1" id="export_excel">
                        <i class="fas fa-file-excel"></i> Excel
                    </button> -->

                </div>
            </div>
        </form>
    </div>

    <div id="result">
        <div class="result-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="badge bg-info"> No Model:</h5>
                <span class="badge bg-warning">No Karung:</span>
            </div>
            <div class="row g-1">
                <div class="col-md-6">
                    <p><strong>Jenis :</strong> </p>
                    <p><strong>Lot :</strong> </p>
                    <p><strong>Kode Warna:</strong> </p>
                    <p><strong>Warna:</strong> </p>
                    <p><strong>Total Cones:</strong>
                    <p><strong>Total Kgs:</strong>
                </div>
                <div class="col-md-6 d-flex flex-column gap-2">
                    <form action="" method="">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" name="cns" class="form-control cns-out" placeholder="Cones Out">
                            </div>
                            <div class="col-md-6">
                                <input type="number" name="cns" class="form-control kg-out" placeholder="KG Out">

                            </div>
                        </div>
                        <div class="row mt-2">


                            <button
                                class="btn btn-outline-info btn-sm ">

                                Keluarkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // Reset filter
        $('#reset_data').click(function(e) {
            e.preventDefault();
            $('input[name="noModel"]').val('');
            $('input[name="warna"]').val('');
            $('#result').html('');
        });


    });
</script>

<?php $this->endSection(); ?>