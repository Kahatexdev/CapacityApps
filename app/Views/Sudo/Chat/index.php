<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content');?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Components &rsaquo; Chat Box &mdash; Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
<!-- Start GA -->
<script async src=""></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>
<!-- /END GA --></head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <div class="main-sidebar sidebar-style-2">
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Chat Box</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item"><a href="#">Components</a></div>
              <div class="breadcrumb-item">Chat Box</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Chat Boxes</h2>
            <p class="section-lead">The chat component and is equipped with a JavaScript API, making it easy for you to integrate with Back-end.</p>

            <div class="row align-items-center justify-content-center">
              <div class="col-12 col-sm-6 col-lg-4">
                <div class="card">
                  <div class="card-header">
                    <h4>Who's Online?</h4>
                  </div>
                  <div class="card-body">
                    <ul class="list-unstyled list-unstyled-border">
                      <li class="media">
                        <img alt="image" class="mr-3 rounded-circle" width="50" src="assets/img/avatar/avatar-1.png">
                        <div class="media-body">
                          <div class="mt-0 mb-1 font-weight-bold">Hasan Basri</div>
                          <div class="text-success text-small font-600-bold"><i class="fas fa-circle"></i> Online</div>
                        </div>
                      </li>
                      <li class="media">
                        <img alt="image" class="mr-3 rounded-circle" width="50" src="assets/img/avatar/avatar-2.png">
                        <div class="media-body">
                          <div class="mt-0 mb-1 font-weight-bold">Bagus Dwi Cahya</div>
                          <div class="text-small font-weight-600 text-muted"><i class="fas fa-circle"></i> Offline</div>
                        </div>
                      </li>
                      <li class="media">
                        <img alt="image" class="mr-3 rounded-circle" width="50" src="assets/img/avatar/avatar-3.png">
                        <div class="media-body">
                          <div class="mt-0 mb-1 font-weight-bold">Wildan Ahdian</div>
                          <div class="text-small font-weight-600 text-success"><i class="fas fa-circle"></i> Online</div>
                        </div>
                      </li>
                      <li class="media">
                        <img alt="image" class="mr-3 rounded-circle" width="50" src="assets/img/avatar/avatar-4.png">
                        <div class="media-body">
                          <div class="mt-0 mb-1 font-weight-bold">Rizal Fakhri</div>
                          <div class="text-small font-weight-600 text-success"><i class="fas fa-circle"></i> Online</div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-lg-4">
                <div class="card chat-box" id="mychatbox">
                  <div class="card-header">
                    <h4>Chat with Rizal</h4>
                  </div>
                  <div class="card-body chat-content">
                  </div>
                  <div class="card-footer chat-form">
                    <form id="chat-form">
                      <input type="text" class="form-control" placeholder="Type a message">
                      <button class="btn btn-primary">
                        <i class="far fa-paper-plane"></i>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-lg-4">
                <div class="card chat-box card-success" id="mychatbox2">
                  <div class="card-header">
                    <h4><i class="fas fa-circle text-success mr-2" title="Online" data-toggle="tooltip"></i> Chat with Ryan</h4>
                  </div>
                  <div class="card-body chat-content">
                  </div>
                  <div class="card-footer chat-form">
                    <form id="chat-form2">
                      <input type="text" class="form-control" placeholder="Type a message">
                      <button class="btn btn-primary">
                        <i class="far fa-paper-plane"></i>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2018 <div class="bullet"></div> Design By <a href="https://nauval.in/">Muhamad Nauval Azhar</a>
        </div>
        <div class="footer-right">
          
        </div>
      </footer>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/tooltip.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  
  <!-- JS Libraies -->

  <!-- Page Specific JS File -->
  <script src="assets/js/page/components-chat-box.js"></script>
  
  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>
<?php $this->endSection(); ?>
