<?php $this->extend($role . '/pluspacking'); ?>
<?php $this->section('pluspacking'); ?>


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

<div class="card-body">
    <h5>Po Tambahan <?= $pdk ?></h5>
    <div class="table-responsive">
        <form action="<?= base_url($role . '/inputpo') ?>" method="post">
            <input type="text" name="pdk" id="" value="<?= $pdk ?>" hidden>
            <table class="table align-items-center mb-0">
                <thead>

                    <th>Style </th>
                    <th> Qty Po</th>
                    <th colspan="2"> PO (+)</th>
                </thead>
                <tbody>
                    <?php foreach ($style as $st) : ?>
                        <tr>
                            <td><?= $st['size'] ?></td>
                            <td><?= ceil($st['qty'] / 24) ?> dz</td>
                            <td>
                                <input type="number" class="PoDz" name="PoDz[]" onchange="toPcs(this)" value="<?= ceil($st['po_plus'] / 24) ?>" <?= $st['po_plus'] > 0 ? 'readonly' : 0 ?>> dz
                            </td>
                            <td>
                                <input type="number" class="form po" name="po[]" value="<?= $st['po_plus'] ?>" <?= $st['po_plus'] > 0 ? 'readonly' : 0 ?>> pcs
                                <input type="hidden" class="form" name="id[]" value="<?= $st['idapsperstyle'] ?>">
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <div class="card-footer">
                <div class="row d-flex justify-content-end">
                    <button class="btn btn-info" type="submit">Simpan</button>
        </form>
    </div>
</div>

<script>
    function toPcs(element) {
        // Ambil nilai dari PoDz input
        let poDzValue = element.value;

        // Hitung nilai po (PoDz * 24)
        let poValue = poDzValue * 24;

        // Temukan input po[] yang sesuai dalam baris yang sama
        let poInput = element.closest('tr').querySelector('.po');
        poInput.value = poValue;
    }
</script>


<?php $this->endSection(); ?>