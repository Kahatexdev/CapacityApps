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
                    <div class="row justify-content-between align-items-center mb-3">
                        <div class="col-auto">
                            <h5>
                                Detail Data Model <?= esc($noModel) ?> Jarum <?= esc($jarum) ?>
                            </h5>
                        </div>
                        <div class="col-auto">

                            <a href="<?= base_url($role . '/detailpdk/') ?>" class="btn bg-gradient-info">Kembali</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <h6>
                                Qty Order Perdelivery
                            </h6>
                            <?php foreach ($headerRow as $val): ?>
                                <li><?= date('d M Y', strtotime($val['delivery']))  ?> : <?= round($val['qty'] / 24) ?> dz</li>
                            <?php endforeach ?>
                            <p>---------------------------------------------</p>

                            Total Order : <?= round($totalPo['totalPo'] / 24) ?> dz

                        </div>



                    </div>

                </div>

                <div class="card-body">
                    <?php foreach ($order as $deliv => $val): ?>
                        <div class="row mt-3">
                            <div class="d-flex justify-content-between align-item-center">
                                <h5> <span class='badge  badge-pill badge-lg bg-gradient-info'>Detail Order Delivery <?= date('d M Y', strtotime($deliv)) ?> </span></h5>
                                <h5> <span class='badge  badge-pill badge-lg bg-gradient-info'>Qty Order <?= round($val['totalQty'] / 24) ?> dz</span></h5>

                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Size</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Inisial</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Qty</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Sisa</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Area</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Factory</th>
                                            <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($val as $key => $list): ?>
                                            <?php if (is_array($list)): // Pastikan $list adalah array 
                                            ?>
                                                <tr>
                                                    <td> <?= $list['size'] ?></td>
                                                    <td><?= $list['inisial'] ?></td>
                                                    <td><?= round($list['qty'] / 24) ?> dz</td>
                                                    <td><?= round($list['sisa'] / 24) ?> dz</td>
                                                    <td><?= $list['factory'] ?></td>
                                                    <td><?= $list['production_unit'] ?></td>x
                                                    <td>
                                                        <button type=" button" class="btn btn-info btn-sm edit-btn" data-toggle="modal" data-target="#ModalEdit" data-id="<?= $list['idapsperstyle']; ?>" data-area="<?= $list['factory']; ?>" data-pdk="<?= $list['mastermodel']; ?>" data-deliv="<?= $list['delivery']; ?> " data-size="<?= $list['size']; ?>" data-jarum="<?= $jarum ?>">
                                                            Ubah Inisial
                                                        </button>
                                                        <button type="button"
                                                            class="btn bg-gradient-info btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#GWAktEdit"
                                                            data-pdk="<?= $noModel ?>"
                                                            data-size="<?= esc($list['size'] ?? ''); ?>"
                                                            data-gw_aktual="<?= esc($list['gw_aktual'] ?? ''); ?>">
                                                            GW Aktual
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <hr>
                            </div>
                        </div>
                    <?php endforeach ?>
                    <div class=" card-footer">
                        <div>
                            <br>

                        </div>
                    </div>



                </div>

                <div class="card-body">
                    <div class="row mt-3">
                        <div class="d-flex justify-content-between align-item-center">
                            <h5> <span class='badge  badge-pill badge-lg bg-gradient-info'>History Revisi</span></h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Tanggal Revisi</th>
                                        <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-10">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historyRev as $key): ?>
                                        <tr>
                                            <td><?= $key['tanggal_rev'] ?></td>
                                            <td><?= $key['keterangan'] ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <hr>
                        </div>
                    </div>
                    <div class=" card-footer">
                        <div>
                            <br>

                        </div>
                    </div>
                </div>

            </div>
        </div>



        <div class="modal fade bd-example-modal-lg" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit <Area:d></Area:d>
                        </h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/inputinisial') ?>" method="post">

                            <div id="confirmationMessage"></div>
                            <input type="text" class="form-control" name="jarum" id="" value="" hidden>

                            <div class="form-group">
                                <label for="pdk" class="label">PDK</label>
                                <input type="text" class="form-control" name="pdk" id="" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="size" class="label">Style Size</label>
                                <input type="text" name="size" class="form-control" id="" value="" readonly>

                            </div>
                            <div class="form-group">
                                <label for="inisial" class="label">Inisial</label>
                                <input type="text" name="inisial" class="form-control" id="" value="" placeholder="Masukan Inisial">
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-danger">Ya</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal GW aktual -->
        <div class="modal fade bd-example-modal-lg" id="GWAktEdit" tabindex="-1" aria-labelledby="GWAktEditLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="GWAktEditLabel">Input GW Aktual</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action=$this->urlMaterial . "saveGWAktual" method="get">
                            <div id="confirmationMessage"></div>

                            <div class="form-group mb-3">
                                <label for="pdk" class="form-label">PDK</label>
                                <input type="text" class="form-control" name="pdk" id="pdk" value="" readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="size" class="form-label">Style Size</label>
                                <input type="text" name="size" class="form-control" id="size" value="" readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label for="gw_aktual" class="form-label">GW Aktual</label>
                                <input type="number" step="0.1" min="0" name="gw_aktual" class="form-control" id="gw_aktual" value="" placeholder="Masukan GW Aktual">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn bg-gradient-info">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable();

                // handler untuk modal GW Aktual (Bootstrap 5)
                $('#GWAktEdit').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget); // button yang memicu modal
                    var pdk = button.data('pdk') || '';
                    var size = button.data('size') || '';
                    var gw_aktual = button.data('gw_aktual') || '';

                    // cek gw_aktual dari API
                    fetch($this - > urlMaterial.
                            'getGWAktual?pdk=' + pdk + '&size=' + size)
                        .then(function(response) {
                            return response.json();
                        })
                        .then(function(data) {
                            if (data && data.gw_aktual) {
                                modal.find('input[name="gw_aktual"]').val(data.gw_aktual);
                            }
                        })
                        .catch(function(error) {
                            console.error('Error fetching GW Aktual:', error);
                        });

                    var modal = $(this);
                    modal.find('input[name="pdk"]').val(pdk);
                    modal.find('input[name="size"]').val(size);
                    modal.find('input[name="gw_aktual"]').val(gw_aktual);

                });

                // Tangani submit form via AJAX/fetch
                $('#GWAktEdit').on('submit', 'form', function(e) {
                    e.preventDefault();

                    var $form = $(this);
                    var action = $form.attr('action');
                    var method = ($form.attr('method') || 'GET').toUpperCase();

                    // Disable tombol submit sementara
                    var $submit = $form.find('button[type="submit"]');
                    $submit.prop('disabled', true).text('Menyimpan...');

                    // Ambil data form
                    var formData = new FormData(this);

                    // Untuk GET: ubah ke query string; untuk POST: kirim body
                    var fetchOptions = {
                        method: method,
                        credentials: 'same-origin', // kirim cookie kalau perlu
                        headers: {}
                    };
                    var url = action;

                    if (method === 'GET') {
                        // Buat query string
                        var params = new URLSearchParams();
                        for (var pair of formData.entries()) {
                            params.append(pair[0], pair[1]);
                        }
                        url += (action.indexOf('?') === -1 ? '?' : '&') + params.toString();
                    } else {
                        // POST/PUT... kirim form data
                        fetchOptions.body = formData;
                        // jangan set Content-Type, biarkan browser atur multipart/form-data
                    }

                    fetch(url, fetchOptions)
                        .then(function(resp) {
                            // jika API mengembalikan JSON
                            var ct = resp.headers.get('content-type') || '';
                            if (ct.indexOf('application/json') !== -1) {
                                return resp.json().then(function(json) {
                                    return {
                                        ok: resp.ok,
                                        json: json
                                    };
                                });
                            }
                            return resp.text().then(function(text) {
                                return {
                                    ok: resp.ok,
                                    text: text
                                };
                            });
                        })
                        .then(function(result) {
                            var $msg = $form.find('#confirmationMessage');

                            if (result.ok) {
                                // tampilkan pesan sukses (coba ambil pesan dari json jika ada)
                                var message = (result.json && (result.json.message || result.json.msg)) ||
                                    (typeof result.text === 'string' && result.text) ||
                                    'Berhasil disimpan.';
                                $msg.html('<div class="alert alert-success small mb-2">' + message + '</div>');

                                // tutup modal setelah 700ms
                                setTimeout(function() {
                                    // tutup modal menggunakan Bootstrap5 API
                                    var modalEl = document.getElementById('GWAktEdit');
                                    var bsModal = bootstrap.Modal.getInstance(modalEl);
                                    if (!bsModal) bsModal = new bootstrap.Modal(modalEl);
                                    bsModal.hide();

                                    // coba reload DataTable kalau ada ajax
                                    try {
                                        if ($.fn.dataTable && $.fn.dataTable.isDataTable('#dataTable')) {
                                            var table = $('#dataTable').DataTable();
                                            if (table.ajax && typeof table.ajax.reload === 'function') {
                                                table.ajax.reload(null, false); // false = tetap di halaman sekarang
                                                return;
                                            }
                                        }
                                    } catch (err) {
                                        console.warn('DataTable reload gagal:', err);
                                    }

                                    // fallback: reload halaman
                                    location.reload();
                                }, 700);
                            } else {
                                // error dari server
                                var errText = (result.json && (result.json.error || result.json.message)) ||
                                    (typeof result.text === 'string' && result.text) ||
                                    'Terjadi kesalahan saat menyimpan.';
                                $msg.html('<div class="alert alert-danger small mb-2">' + errText + '</div>');
                                $submit.prop('disabled', false).text('Simpan');
                            }
                        })
                        .catch(function(err) {
                            var $msg = $form.find('#confirmationMessage');
                            $msg.html('<div class="alert alert-danger small mb-2">Error: ' + (err.message || err) + '</div>');
                            $submit.prop('disabled', false).text('Simpan');
                            console.error(err);
                        });
                });

                // (opsional) Reset tombol saat modal ditutup
                $('#GWAktEdit').on('hidden.bs.modal', function() {
                    var $form = $(this).find('form');
                    $form.find('button[type="submit"]').prop('disabled', false).text('Simpan');
                });

                // tetap simpan edit-btn handlermu jika ada modal edit lain
                $('.edit-btn').click(function() {
                    var idAps = $(this).data('id');
                    var area = $(this).data('area');
                    var pdk = $(this).data('pdk');
                    var deliv = $(this).data('deliv');
                    var size = $(this).data('size');
                    var jarum = $(this).data('jarum');

                    $('#editModal').modal('show');
                    $('#editModal').find('input[name="area"]').val(area);
                    $('#editModal').find('input[name="id"]').val(idAps);
                    $('#editModal').find('input[name="pdk"]').val(pdk);
                    $('#editModal').find('input[name="deliv"]').val(deliv);
                    $('#editModal').find('input[name="size"]').val(size);
                    $('#editModal').find('input[name="jarum"]').val(jarum);
                });
            });

            document.getElementById('selectAll').addEventListener('click', function(e) {
                var checkboxes = document.querySelectorAll('.delivery-checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = e.target.checked;
                });
            });
        </script>
        <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
        <?php $this->endSection(); ?>