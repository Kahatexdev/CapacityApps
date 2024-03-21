<?php $this->extend('Capacity/layout'); ?>
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
                    <div class="d-flex justify-content-between">
                        <h5>
                            Detail Data Model 
                        </h5>
                        <a href="<?= base_url('capacity/semuaOrder/') ?>" class="btn bg-gradient-info"> Kembali</a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        

                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#ModalBooking">Booking to Booking</a>
                            <a href="#" class="btn btn-info order-btn" data-bs-toggle="modal" data-bs-target="#exampleModalMessage">Booking to Order</a>
                            <a href="" class="btn btn-success" Data-bs-toggle="modal" data-bs-target="#ModalEdit">Edit Booking</a>
                            <a href="" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ModalCancel">Cancel Booking</a>
                            <a href="" class="btn btn-danger" Data-bs-toggle="modal" data-bs-target="#ModalDelete">Delete Booking</a>
                        </div>

                    </div>
                </div>
                
            </div>


        </div>
        <script>
            
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>