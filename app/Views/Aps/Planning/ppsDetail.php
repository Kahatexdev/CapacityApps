<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<style>
    td {
        min-width: 120px;
        /* atur sesuai kebutuhan */
    }

    td select.form-control,
    td input.form-control,
    td textarea.form-control {
        width: 150px;
        /* supaya input & select punya lebar tetap */
    }
</style>
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
                    <div class="row">
                        <div class="col">
                            <h5>
                                Preproduction Sample
                            </h5>
                        </div>
                        <div class="col-2">
                            <a href="<?= base_url($role . '/pps') ?>" class="btn btn-info w-100">Back</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="fw-bold d-block">No Model:</label>
                            <h3> <span><?= esc($modelData['no_model']); ?></span></h3>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold d-block">Buyer:</label>
                            <h3><span><?= esc($modelData['buyer']); ?></span></h3>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold d-block">Product Type:</label>
                            <h3> <span><?= esc($modelData['product_type']); ?></span></h3>
                        </div>
                    </div>

                </div>
                <div class="card-body p-3">
                    <div class="row">

                        <div class="table-responsive">
                            <?php
                            function formatDate($datetime)
                            {
                                return !empty($datetime) ? date('Y-m-d', strtotime($datetime)) : '';
                            }
                            ?>
                            <form action="<?= base_url($role . '/updatePps') ?>" method="POST">

                                <table id="dataTable" class="display compact striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Material</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Priority</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Inisial</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Style Size</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Mekanik</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Koor PPS</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Start Production</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Plan to Knitting</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Target to Finish</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Act Start PPS</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Act Finish PPS</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Acc QAD</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Acc MR</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Acc FU</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">Notes</th>
                                            <th class="text-uppercase text-dark text-center text-xxs font-weight-bolder opacity-7 ps-2">History</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($ppsData as $pps) : ?>
                                            <tr>
                                                <?php
                                                // --- Material Status ---
                                                $materialStatus = $pps['material_status'] ?? '';
                                                $inputClass = 'form-control';
                                                if (strtolower($materialStatus) === 'complete') {
                                                    $inputClass .= ' bg-success text-white';
                                                }

                                                // --- Priority ---
                                                $currentPriority = $pps['priority'] ?? 'low';
                                                $priorities = ['low', 'normal', 'high'];

                                                // --- PPS Status ---
                                                $statuses = ['planning', 'process', 'perbaikan', 'hold', 'declined', 'approved'];
                                                $current = !empty($pps['pps_status']) ? $pps['pps_status'] : 'planning';

                                                // --- Format History jadi HTML-friendly ---
                                                $rawHistory = trim($pps['history'] ?? '');
                                                $formattedHistory = '';
                                                if ($rawHistory !== '') {
                                                    $lines = explode("\n", $rawHistory);
                                                    foreach ($lines as $line) {
                                                        // Kasih warna & format timestamp
                                                        $line = htmlspecialchars($line);
                                                        $line = preg_replace('/\[(.*?)\]/', '<span class="text-secondary small fw-bold">[$1]</span>', $line);
                                                        $formattedHistory .= $line . '<br>';
                                                    }
                                                }
                                                ?>

                                                <td class="text-center">
                                                    <input type="text" readonly value="<?= htmlspecialchars($materialStatus); ?>" class="<?= $inputClass; ?>">
                                                </td>

                                                <td class="text-center">
                                                    <select name="priority[]" class="form-control priority-select">
                                                        <?php foreach ($priorities as $priority): ?>
                                                            <option value="<?= $priority ?>" <?= $currentPriority === $priority ? 'selected' : '' ?>>
                                                                <?= ucfirst($priority) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>

                                                <td class="text-center">
                                                    <select name="status[]" class="form-control status-select" style="font-weight:bold;">
                                                        <?php foreach ($statuses as $status): ?>
                                                            <option value="<?= $status ?>" <?= $current === $status ? 'selected' : '' ?>>
                                                                <?= ucfirst($status) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>

                                                <td class="text-center"><?= htmlspecialchars($pps['inisial']); ?></td>
                                                <td class="text-center">
                                                    <input type="hidden" name="imp[]" value="<?= $pps['id_mesin_perinisial']; ?>">
                                                    <input type="hidden" name="id_pps[]" value="<?= $pps['id_pps']; ?>">
                                                    <?= htmlspecialchars($pps['size']); ?>
                                                </td>

                                                <td class="text-center"><input type="text" name="mechanic[]" value="<?= $pps['mechanic']; ?>" class="form-control"></td>
                                                <td class="text-center"><input type="text" name="coor[]" value="<?= $pps['coor']; ?>" class="form-control"></td>
                                                <td class="text-center"><input type="date" name="start_mc[]" readonly value="<?= formatDate($pps['start_mc']); ?>" class="form-control"></td>
                                                <td class="text-center"><input type="date" name="start_pps_plan[]" readonly value="<?= formatDate($pps['start_pps_plan']); ?>" class="form-control"></td>
                                                <td class="text-center"><input type="date" name="stop_pps_plan[]" readonly value="<?= formatDate($pps['stop_pps_plan']); ?>" class="form-control"></td>
                                                <td class="text-center"><input type="date" name="start_pps_act[]" value="<?= formatDate($pps['start_pps_act']); ?>" class="form-control"></td>
                                                <td class="text-center"><input type="date" name="stop_pps_act[]" value="<?= formatDate($pps['stop_pps_act']); ?>" class="form-control"></td>
                                                <td class="text-center"><input type="date" name="acc_qad[]" value="<?= formatDate($pps['acc_qad']); ?>" class="form-control"></td>
                                                <td class="text-center"><input type="date" name="acc_mr[]" value="<?= formatDate($pps['acc_mr']); ?>" class="form-control"></td>
                                                <td class="text-center"><input type="date" name="acc_fu[]" value="<?= formatDate($pps['acc_fu']); ?>" class="form-control"></td>

                                                <td class="text-center">
                                                    <textarea class="form-control" name="notes[]" rows="3" style="min-width:180px;"><?= htmlspecialchars($pps['notes']); ?></textarea>
                                                </td>

                                                <td class="text-center">
                                                    <?php
                                                    $rawHistory = trim($pps['history'] ?? '');
                                                    $formattedHistory = '';
                                                    $lines = [];

                                                    if ($rawHistory !== '') {
                                                        $lines = explode("\n", $rawHistory);
                                                        foreach ($lines as $line) {
                                                            $line = htmlspecialchars($line);
                                                            $line = preg_replace('/\[(.*?)\]/', '<span class="text-secondary small fw-bold">[$1]</span>', $line);
                                                            $formattedHistory .= $line . '<br>';
                                                        }
                                                    }

                                                    // Batasi preview jadi 3 baris pertama
                                                    $preview = implode("<br>", array_slice($lines, 0, 3));
                                                    $hasMore = count($lines) > 1;
                                                    ?>

                                                    <div class="form-control bg-light text-start" style="min-width:220px; height:100px; overflow-y:auto; white-space:pre-line;">
                                                        <?= $preview ?: '<span class="text-muted small fst-italic">No history yet</span>' ?>
                                                    </div>

                                                    <?php if ($hasMore): ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary mt-1 view-history-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#historyModal"
                                                            data-history="<?= htmlspecialchars($formattedHistory, ENT_QUOTES, 'UTF-8'); ?>">
                                                            View Full
                                                        </button>
                                                    <?php endif; ?>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="16">

                                                <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-uppercase shadow-sm rounded-pill" onclick="test()">
                                                    💾 Simpan Data
                                                </button>

                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal: View Full History -->
        <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title text-white" id="historyModalLabel">Full History</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="historyContent" style="white-space:pre-line; max-height:500px; overflow-y:auto;">
                        <!-- Full history injected here -->
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('historyModal');
                const modalBody = document.getElementById('historyContent');

                modal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const historyHtml = button.getAttribute('data-history') || '<span class="text-muted">No history</span>';
                    modalBody.innerHTML = historyHtml;
                });
            });
        </script>


        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "pageLength": 35,
                    "order": []
                });


            });
        </script>
        <script>
            function test() {
                console.log('tes')
            }

            document.querySelectorAll('.priority-select').forEach(sel => {
                const setColor = () => {
                    const colors = {
                        low: '#db8606ff',
                        normal: '#18ee05ff',
                        high: '#dc3545'
                    };
                    const val = sel.value;
                    sel.style.backgroundColor = colors[val] || '';
                    sel.style.color = val ? 'white' : '';
                };
                sel.addEventListener('change', setColor);
                setColor(); // set awal
            });

            document.querySelectorAll('.status-select').forEach(sel => {
                const setColor = () => {
                    const colors = {
                        planning: '#3498db', // biru
                        process: '#b1a006ff', // kuning
                        perbaikan: '#f8a50cff', // kuning
                        hold: '#e74c3c', // merah
                        declined: '#c0392b', // merah tua
                        approved: '#2ecc71' // hijau
                    };
                    const val = sel.value;
                    sel.style.backgroundColor = colors[val] || '';
                    sel.style.color = val ? 'white' : '';
                };
                sel.addEventListener('change', setColor);
                setColor(); // set awal waktu halaman load
            });
        </script>
        <?php $this->endSection(); ?>