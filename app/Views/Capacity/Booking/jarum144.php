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
                                    Data Booking Jarum {Jarum}
                                </h5>
                            </div>
                        </div>
                        <div>
                            <a class="btn btn-success bg-gradient-success shadow text-center border-radius-md">
                                Import Data Booking
                            </a>
                            <a class="btn btn-success bg-gradient-info shadow text-center border-radius-md">
                                Input Data Booking
                            </a>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="row mt-3">
        <div class="card">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7">Tgl Booking</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Buyer</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">No Order</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">No PDK</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Desc</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Seam</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Shipment</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Qty Booking</th>
                            <th class="text-uppercase text-dark text-sm font-weight-bolder opacity-7 ps-2">Sisa Booking</th>

                            <th class="text-dark opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</div>
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>