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

    .btn-remove-item {
        background: transparent;
        border: none;
        font-size: 16px;
        line-height: 1;
        color: #dc3545;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-remove-item:hover {
        color: #b02a37;
        transform: scale(1.2);
    }

    .btn-add-row {
        background: transparent;
        border: none;
        font-size: 16px;
        line-height: 1;
        color: #c8f7c5;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-add-row:hover {
        color: #2d7a2d;
        transform: scale(1.2);
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


                <h3 class="mb-4">Retur Bahan Baku dari Mc <?= $area ?></h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?= base_url($role . '/stockarea/' . $area); ?>" class="btn btn-outline-info btn-sm position-relative">
                    Kembali

                </a>
            </div>
        </div>

        <form id="saveReturFromMc">
            <div id="repeatContainer">
                <div class="repeat-item card p-3 mb-3 position-relative">
                    <button type="button" id="addRepeatBtn" class="btn-add-row position-absolute top-0 end-0 p-1 m-1"
                        style="border:none; background:#d4edda; color:#155724; border-radius:4px;">
                        +
                    </button>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold mb-1">No Model</label>
                            <select name="no_model[]" class="form-select no-model select2" style="width: 100%;">
                                <option value="">Pilih Model</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">Lot</label>
                            <select name="lot[]" class="form-select form-select-sm lot">
                            </select>
                        </div>

                        <input type="hidden" name="id[]" class="form-control form-control-sm">
                        <input type="hidden" name="kg_cns[]" class="form-control form-control-sm">

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">CNS</label>
                            <input type="number" name="cones[]"
                                class="form-control form-control-sm" placeholder="CNS">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">KG</label>
                            <input type="number" step="0.01" name="kg[]"
                                class="form-control form-control-sm" placeholder="KG">
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-sm btn-info mt-1" style="width: 100%;">
                Simpan
            </button>
        </form>
    </div>
</div>



<!-- <script>
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
</script> -->
<!-- <script>
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
</script> -->

<script>
    // === INIT SELECT2 ===
    function initSelect2(element) {
        if ($(element).hasClass("select2-hidden-accessible")) {
            $(element).select2("destroy");
        }

        let getModelUrl = "<?= base_url($role . '/getNoModelList'); ?>"; // pastikan ini URL bener


        $(element).select2({
            dropdownParent: $('#repeatContainer'),
            minimumInputLength: 3,
            placeholder: "Ketik minimal 3 karakter...",
            allowClear: true,
            width: "100%",
            ajax: {
                url: getModelUrl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: `${item.no_model}|${item.item_type}|${item.kode_warna}|${item.warna}`,
                            text: `${item.no_model} | ${item.item_type} | ${item.kode_warna} | ${item.warna}`
                        }))
                    };
                },
                error: function(xhr) {
                    console.log("AJAX ERROR STATUS no model:", xhr.status);
                    console.log("AJAX ERROR RESPONSE:", xhr.responseText);
                }
            }
        });
    }
    // Event ketika no_model berubah
    $(document).on('change', '.no-model', function() {
        let selected = $(this).val(); // misal: "GP0720|TypeA|KW01|Merah"
        if (!selected) return;

        // Split menjadi bagian-bagian
        let parts = selected.split('|');
        let no_model = parts[0].trim();
        let item_type = parts[1].trim();
        let kode_warna = parts[2].trim();
        let warna = parts[3].trim();

        // Cari select lot di baris yang sama
        let $row = $(this).closest('.repeat-item');
        let $lotSelect = $row.find('.lot');

        // Kosongkan pilihan sebelumnya
        $lotSelect.empty().append('<option value="">Pilih Lot</option>');

        // Ambil data lot via AJAX
        $.ajax({
            url: "<?= base_url($role . '/getLotByList'); ?>",
            type: 'GET',
            dataType: 'json',
            data: {
                no_model,
                item_type,
                kode_warna,
                warna
            },
            success: function(data) {
                data.forEach(function(item) {
                    console.log(item);
                    $lotSelect.append(
                        `<option value="${item.lot}" data-kgcns="${item.kg_cns}" data-idstock="${item.idstock}">${item.lot}</option>`
                    );
                });
            },
            error: function(xhr) {
                console.log('AJAX ERROR STATUS lot:', xhr.status);
                console.log('AJAX ERROR RESPONSE:', xhr.responseText);
            }
        });
    });
    // Event ketika user memilih lot berbeda
    $(document).on('change', '.lot', function() {
        let $row = $(this).closest('.repeat-item');
        let $idInput = $row.find('input[name="id[]"]');
        let $kgCnsInput = $row.find('input[name="kg_cns[]"]');

        let selectedOption = $(this).find('option:selected');
        let id = selectedOption.data('idstock');
        let kg_cns = selectedOption.data('kgcns');
        console.log(id, kg_cns);

        $idInput.val(id || '');
        $kgCnsInput.val(kg_cns || '');
    });

    // Event ketika CNS diisi → hitung KG
    $(document).on('input', 'input[name="cones[]"]', function() {
        let $row = $(this).closest('.repeat-item');
        let cns = parseFloat($(this).val()) || 0; // CNS input
        let kg_cns = parseFloat($row.find('input[name="kg_cns[]"]').val()) || 0; // KG per CNS

        // Hitung KG
        let kg = cns * kg_cns;

        // Isi field KG
        $row.find('input[name="kg[]"]').val(kg.toFixed(2)); // dua desimal
    });

    // === BUILD HTML ROW ===
    function buildRepeatItem() {
        let index = $(".repeat-item").length;

        return `
            <div class="repeat-item card p-3 mb-2 position-relative">
                ${index > 0 ? `
                <button type="button" class="btn-remove-item position-absolute top-0 end-0 p-1 m-1"
                    style="border:none; background:#f8d7da; color:#a00; border-radius:4px;">
                    ×
                </button>` : ""}

                <div class="row g-2">
                    <div class="col-md-12">
                        <label class="form-label small mb-1">No Model</label>
                        <select name="no_model[]" class="form-select form-select-sm select2 no-model">
                            <option value="">Pilih Model</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small mb-1">Lot</label>
                        <select name="lot[]" class="form-select form-select-sm lot">
                            <option value="">Pilih Lot</option>
                        </select>
                    </div>

                    <input type="hidden" name="id[]" class="form-control form-control-sm">
                    <input type="hidden" name="kg_cns[]" class="form-control form-control-sm">

                    <div class="col-md-4">
                        <label class="form-label small mb-1">CNS</label>
                        <input type="number" name="cones[]" class="form-control form-control-sm" placeholder="CNS">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small mb-1">KG</label>
                        <input type="number" step="0.01" name="kg[]" class="form-control form-control-sm" placeholder="KG">
                    </div>
                </div>
            </div>
        `;
    }

    // === ADD ROW ===
    $("#addRepeatBtn").on("click", function() {
        $("#repeatContainer").append(buildRepeatItem());

        // init select2 untuk row yang barusan ditambahkan
        $("#repeatContainer .repeat-item:last .select2").each(function() {
            initSelect2(this);
        });
    });

    // === REMOVE ROW ===
    $(document).on("click", ".btn-remove-item", function() {
        $(this).closest(".repeat-item").remove();
    });

    // === INIT SELECT2 UNTUK ROW PERTAMA ===
    $(document).ready(function() {
        $("#repeatContainer .select2").each(function() {
            initSelect2(this);
        });
    });

    $('#saveReturFromMc').on('submit', function(e) {
        e.preventDefault(); // cegah reload page

        let formData = $(this).serialize(); // ambil semua input array

        $.ajax({
            url: "<?= base_url($role . '/saveStockareaFromMc'); ?>", // ganti sesuai method controller
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan!'
                    });
                    // Hapus semua baris tambahan
                    $('#repeatContainer .repeat-item').not(':first').remove();

                    // Reset semua input/select di baris pertama
                    $('#repeatContainer .repeat-item:first').find('input, select').each(function() {
                        if ($(this).is('select')) {
                            // kembalikan ke option pertama
                            $(this).prop('selectedIndex', 0).trigger('change');
                        } else {
                            // kosongkan input
                            $(this).val('');
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan!'
                    });
                }
            },
            error: function(xhr) {
                console.log("AJAX ERROR STATUS:", xhr.status);
                console.log("AJAX ERROR RESPONSE:", xhr.responseText);
            }
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