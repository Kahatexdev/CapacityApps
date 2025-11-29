<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>
<style>
    .fc-event.event-normal {
        background-color: #007bff;
        /* Biru untuk event biasa */
        color: white;
    }

    .fc-event.event-holiday {
        background-color: #ff4d4d;
        /* Merah untuk hari libur */
        color: white;
    }
</style>

<div class="container-fluid py-4"><?php if (session()->getFlashdata('success')) : ?><script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                });
            });
        </script><?php endif; ?><?php if (session()->getFlashdata('error')) : ?><script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script><?php endif; ?><div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h5>Available Machine Calendar </h5>
                            <div class="row">
                                <div class="col">
                                    <h6>Area : <?= $area ?><br>Jarum : <?= $jarum ?><br>Mesin : <?= $mesin ?><br></h6>
                                </div>
                                <div class="col-auto"><a href="<?= base_url($role . '/detailplnmc/' . $id) ?>" class="btn btn-secondary">Back</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-lg-12 ">
                            <div class="content">
                                <div id='calendar'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDate"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Title:</strong> <span id="modalTitle"></span></p>
                        <table id="modalDescription" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Model</th>
                                    <th>Mesin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Isi tabel akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>



        <script src='<?= base_url('assets/calendar/fullcalendar/packages/core/main.js') ?>'></script>
        <script src=' <?= base_url('assets/calendar/fullcalendar/packages/interaction/main.js') ?>'></script>
        <script src='<?= base_url('assets/calendar/fullcalendar/packages/daygrid/main.js') ?>'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var libur = <?= $libur ?>;
                var events = <?= $events ?>;
                var allEvents = events.concat(libur);

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['interaction', 'dayGrid'],
                    defaultDate: '<?= $today ?>',
                    editable: false,
                    eventLimit: true,
                    events: allEvents,
                    eventClick: function(info) {
                        // Ambil data event
                        var title = info.event.title;
                        var date = info.event.start.toISOString().split('T')[0]; // Format tanggal
                        var description = JSON.parse(info.event.extendedProps.desk || '[]'); // Parse deskripsi JSON

                        // Masukkan data ke modal
                        document.getElementById('modalDate').textContent = date;
                        document.getElementById('modalTitle').textContent = title;

                        // Masukkan deskripsi ke dalam tabel
                        var tableBody = document.getElementById('modalDescription').getElementsByTagName('tbody')[0];
                        tableBody.innerHTML = ''; // Bersihkan isi tabel sebelumnya

                        for (var model in description) {
                            var row = tableBody.insertRow();
                            var modelCell = row.insertCell(0);
                            var mesinCell = row.insertCell(1);

                            modelCell.textContent = model;
                            mesinCell.textContent = description[model] + ' Mc';
                        }

                        // Tampilkan modal
                        $('#eventModal').modal('show');
                    }
                });

                calendar.render();
            });
        </script>
        <script src="<?= base_url('assets/calendar/js/main.js') ?>'"></script>
        </body><?php $this->endSection(); ?>