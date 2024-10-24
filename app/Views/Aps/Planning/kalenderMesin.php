<?php ini_set('display_errors', 1);
error_reporting(E_ALL); ?>
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
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h5>
                                Available Machine
                            </h5>
                            <div class="row">
                                <div class="col">
                                    <h6>

                                    </h6>
                                </div>
                                <div class="col-auto">
                                    <a href="<?= base_url($role . '/detailplnmc/') ?>" class="btn btn-secondary">Back</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="content">
                                <div id='calendar'></div>
                            </div>
                        </div>

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

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['interaction', 'dayGrid'],
                    defaultDate: '2024-10-01',
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    events: [{
                            title: 'All Day Event',
                            start: '2024-10-01'
                        },


                    ]
                });

                calendar.render();
            });
        </script>

        <script src="<?= base_url('assets/calendar/js/main.js') ?>'"></script>
        </body>

        <?php $this->endSection(); ?>