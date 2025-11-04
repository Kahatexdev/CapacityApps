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
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <h6 class="mb-1 text-muted">Kapasitas</h6>
                <h5 class="fw-bold mb-0">
                    <?= number_format($kapasitas, 0, ',', '.') ?> kg
                </h5>
            </div>

            <div class="col-md-4">
                <h6 class="mb-1 text-muted">Terisi</h6>
                <h5 class="fw-bold mb-0 text-primary">
                    <?= number_format($terisi, 0, ',', '.') ?> kg
                </h5>
            </div>

            <div class="col-md-4">
                <h6 class="mb-1 text-muted">Sisa Kapasitas</h6>
                <h5 class="fw-bold mb-0 <?= $sisaKapasitas < 0 ? 'text-danger' : 'text-success' ?>">
                    <?= number_format($sisaKapasitas, 0, ',', '.') ?> kg
                </h5>
            </div>
        </div>


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

            </div>
        </div>
    </div>

    <div id="result">
        <?php
        foreach ($stock as $st):
        ?>
            <div class="result-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="badge bg-info"> No Model:<?= $st['no_model']; ?></h5>
                    <span class="badge bg-danger">No Karung: <?= $st['no_karung']; ?></span>
                </div>
                <div class="row g-1">
                    <div class="col-md-6">
                        <p><strong>Jenis :</strong> <?= $st['item_type']; ?> </p>
                        <p><strong>Lot :</strong> <?= $st['lot']; ?></p>
                        <p><strong>Kode Warna:</strong> <?= $st['kode_warna']; ?> </p>
                        <p><strong>Warna:</strong> <?= $st['warna']; ?> </p>
                        <p><strong>Total Cones: <?= $st['cns_in_out']; ?></strong>
                        <p><strong>Total Kgs: <?= $st['kgs_in_out']; ?></strong>
                    </div>
                    <div class="col-md-6 d-flex flex-column gap-2">
                        <form action="<?= base_url($role . '/stockarea/outStock') ?>" method="post" class="keluarkan-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="">Cones Out:</label>
                                    <input type="hidden" name="idStock" class="form-control" value="<?= $st['id_stock_area']; ?>">
                                    <input type="hidden" name="total_cns" class="form-control total-cns" value="<?= $st['cns_in_out']; ?>">
                                    <input type="number" name="cns" class="form-control cns-out" placeholder="Cones Out" min="0" step="1">
                                    <input type="hidden" name="kg_cns" class="form-control kg_cns" value="<?= $st['kg_cns']; ?>">
                                    <input type="hidden" name="area" class="form-control total-cns" value="<?= $area ?>">

                                </div>
                                <div class="col-md-6">
                                    <label for="">Kg Out:</label>
                                    <input type="number" name="kg" class="form-control kg-out" placeholder="KG Out" readonly>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <button type="submit" class="btn btn-outline-info btn-sm">
                                    Keluarkan
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="row text-end">
                    <div class="col">

                        <span class="badge bg-secondary"> <?= $st['created_at']; ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
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
    document.addEventListener('DOMContentLoaded', () => {
        const resultDiv = document.getElementById('result');

        // helper: show toast/alert (simple)
        function showAlert(msg, type = 'danger') {
            // simple alert fallback - ganti sesuai UI/Toast yang lo pakai
            alert(msg);
            // you can implement nicer toasts here
        }

        // bind kalkulasi + submit untuk setiap form
        function bindForms() {
            document.querySelectorAll('.keluarkan-form').forEach(form => {
                // avoid double-binding by checking a flag
                if (form.dataset.bound === '1') return;
                form.dataset.bound = '1';

                const cnsInput = form.querySelector('.cns-out');
                const kgInput = form.querySelector('.kg-out');
                const kgPerCnsEl = form.querySelector('.kg_cns');
                const totalCnsEl = form.querySelector('.total-cns');

                const kgPerCns = parseFloat(kgPerCnsEl?.value || 0);
                const totalCones = parseFloat(totalCnsEl?.value || 0);

                // realtime kalkulasi + limit
                cnsInput?.addEventListener('input', () => {
                    let cnsVal = parseFloat(cnsInput.value) || 0;

                    if (cnsVal < 0) {
                        cnsVal = 0;
                        cnsInput.value = 0;
                    }

                    if (cnsVal > totalCones) {
                        cnsVal = totalCones;
                        cnsInput.value = totalCones;

                        // quick invalid feedback
                        cnsInput.classList.add('is-invalid');
                        setTimeout(() => cnsInput.classList.remove('is-invalid'), 700);
                    }

                    const hasilKg = cnsVal * kgPerCns;
                    kgInput.value = hasilKg ? hasilKg.toFixed(2) : '';
                });

                // AJAX submit
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const idStock = form.querySelector('input[name="idStock"]').value;
                    const cnsOut = parseFloat(form.querySelector('input[name="cns"]').value) || 0;
                    const kgOut = parseFloat(form.querySelector('input[name="kg"]').value) || 0;

                    // basic frontend validation
                    if (cnsOut <= 0) {
                        showAlert('Masukkan jumlah cones yang akan dikeluarkan', 'warning');
                        return;
                    }
                    if (cnsOut > totalCones) {
                        showAlert('Jumlah cones melebihi stok', 'warning');
                        return;
                    }

                    const btn = form.querySelector('button[type="submit"]');
                    const originalBtnHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                    try {
                        const formData = new FormData(form);

                        const res = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            // HTTP error
                            throw new Error(data.message || `Server error ${res.status}`);
                        }

                        if (data.success) {
                            // replace #result dengan partial HTML terbaru dari server
                            if (data.html) {
                                resultDiv.innerHTML = data.html;
                                // re-bind forms on new content
                                bindForms();
                            } else {
                                // kalau server cuma ngasih success tanpa html, kita bisa lakukan update kecil (opsional)
                                showAlert('Berhasil mengeluarkan stok', 'success');
                            }
                        } else {
                            showAlert(data.message || 'Gagal memproses permintaan', 'danger');
                        }
                    } catch (err) {
                        console.error(err);
                        showAlert('Terjadi kesalahan: ' + err.message, 'danger');
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = originalBtnHtml;
                    }
                });
            });
        }

        // initial bind
        bindForms();
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