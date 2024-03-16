<?php $this->extend('Capacity/layout'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Capacity System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Tabel Data Booking
                                </h5>
                            </div>
                        </div>
                        <div >
                                <button class="btn btn-info bg-gradient-info btn-block"> (+) Data Booking</button>  
                                <button class="btn btn-info bg-gradient-info btn-block"> Import Booking</button>

                        </div>
            
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <div class="row my-3">
        <div class="col-lg-12">
            <div class="card z-index-2">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Author</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Function</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Technology</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Employed</th>
                    <th class="text-secondary opacity-7"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td>
                    </td>
                    </tr>
                </tbody>
                </table>
            </div>
                
            </div>
        </div>
    </div>
</div>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>

<?php $this->endSection(); ?>