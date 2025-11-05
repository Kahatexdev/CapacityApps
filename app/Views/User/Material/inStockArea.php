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
<style>
    .fade-message {
        text-align: center;
        color: #198754;
        font-weight: 600;
        margin-bottom: 10px;
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


                <h3 class="mb-4">Penerimaan Bahan Baku <?= $area ?></h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?= base_url($role . '/stockarea/' . $area); ?>" class="btn btn-outline-info btn-sm position-relative">
                    Kembali

                </a>
            </div>
        </div>



        <div class="row g-3">
            <div class="col-lg-4 col-sm-12">
                <input class="form-control" type="text" name="noModel" placeholder="Masukkan No Model">
            </div>
            <div class="col-lg-4 col-sm-12">
                <input class="form-control" type="text" name="warna" placeholder="Masukkan Kode Warna">
            </div>
            <div class="col-lg-4 col-sm-12 d-flex gap-2">
                <button class="btn btn-info flex-grow-1" id="filter_data"><i class="fas fa-search"></i> Cari</button>
                <button class="btn btn-secondary flex-grow-1" id="reset_data"><i class="fas fa-redo"></i> Reset</button>

            </div>
        </div>
    </div>

    <div id="result">
        <?php if ($dataPengiriman): ?>

            <?php
            foreach ($dataPengiriman as $dt):
            ?>
                <div class="result-card">

                    <div class="row g-1">
                        <div class="col-md-4">
                            <span class="badge bg-secondary">Tanggal Kirm: <?= $dt['updated_at']; ?></span>
                            <p><strong>No Karung :</strong> <?= $dt['no_karung']; ?> </p>
                            <p><strong>No Model :</strong> <?= $dt['no_model']; ?> </p>
                            <p><strong>Jenis :</strong> <?= $dt['item_type']; ?> </p>

                        </div>
                        <div class="col-md-4">
                            <p><strong>Lot :</strong> <?= $dt['lot_out']; ?></p>
                            <p><strong>Kode Warna:</strong> <?= $dt['kode_warna']; ?> </p>
                            <p><strong>Warna:</strong> <?= $dt['warna']; ?> </p>

                        </div>
                        <div class="col-md-4 d-flex flex-column gap-2">
                            <form action="<?= base_url($role . '/stockarea/saveStock') ?>" method="post" class="pemasukan-form">
                                <input type="hidden" name="id_pengeluaran" class="form-control" value="<?= $dt['id_pengeluaran']; ?>">
                                <input type="hidden" name="no_karung" class="form-control" value="<?= $dt['no_karung']; ?>">
                                <input type="hidden" name="area" class="form-control" value="<?= $area ?>">
                                <input type="hidden" name="no_model" class="form-control" value="<?= $dt['no_model']; ?>">
                                <input type="hidden" name="item_type" class="form-control" value="<?= $dt['item_type']; ?>">
                                <input type="hidden" name="lot_out" class="form-control" value="<?= $dt['lot_out']; ?>">
                                <input type="hidden" name="kode_warna" class="form-control" value="<?= $dt['kode_warna']; ?>">
                                <input type="hidden" name="warna" class="form-control" value="<?= $dt['warna']; ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Cones Terima:</label>
                                        <input type="number" name="cns" class="form-control cns-out" value="<?= $dt['cns_out']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Kg Terima:</label>
                                        <input type="number" name="kg" class="form-control kg-out" value="<?= $dt['kgs_out']; ?>">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <button type="submit"
                                        class="btn btn-outline-info btn-sm">
                                        Terima
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <div class="row text-center">
                    <h3>Belum ada data kirim</h3>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputModel = document.querySelector('input[name="noModel"]');
        const inputWarna = document.querySelector('input[name="warna"]');
        const resetBtn = document.getElementById('reset_data');
        const cards = document.querySelectorAll('#result .result-card');

        function filterData() {
            const modelVal = inputModel.value.trim().toLowerCase();
            const warnaVal = inputWarna.value.trim().toLowerCase();

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const matchModel = modelVal === '' || text.includes(modelVal);
                const matchWarna = warnaVal === '' || text.includes(warnaVal);

                card.style.display = (matchModel && matchWarna) ? '' : 'none';
            });
        }

        // Realtime filter
        inputModel.addEventListener('input', filterData);
        inputWarna.addEventListener('input', filterData);

        // Tombol Reset
        resetBtn.addEventListener('click', () => {
            inputModel.value = '';
            inputWarna.value = '';
            cards.forEach(card => card.style.display = '');
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const forms = document.querySelectorAll(".pemasukan-form");

        forms.forEach(form => {
            form.addEventListener("submit", async function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                });

                const result = await response.json();

                if (result.status === "success") {
                    const card = form.closest(".result-card");

                    // bikin elemen notifikasi kecil
                    const notif = document.createElement("div");
                    notif.textContent = "✅ Data berhasil diterima";
                    notif.classList.add("fade-message");
                    card.parentNode.insertBefore(notif, card);

                    // animasi fade out card
                    card.style.transition = "opacity 0.5s ease";
                    card.style.opacity = "0";
                    setTimeout(() => {
                        card.remove();
                    }, 500);

                    // tampilkan notifikasi, lalu hilang perlahan
                    notif.style.opacity = "0";
                    notif.style.transition = "opacity 0.5s ease";
                    setTimeout(() => {
                        notif.style.opacity = "1";
                    }, 200);
                    setTimeout(() => {
                        notif.style.opacity = "0";
                        setTimeout(() => notif.remove(), 500);
                    }, 2000);
                }
            });
        });
    });
</script>







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