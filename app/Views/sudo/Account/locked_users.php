<?php $this->extend('sudo/layout'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6>🔒 Akun Terkunci</h6>
        </div>

        <div class="card-body px-0 pt-0 pb-2">

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success mx-3">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (empty($dataUser)) : ?>
                <div class="alert alert-info mx-3">
                    Tidak ada akun terkunci saat ini.
                </div>
            <?php else : ?>

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Gagal Login</th>
                                <th>IP Terakhir</th>
                                <th>Terkunci Sampai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($dataUser as $u) : ?>
                            <tr>
                                <td>
                                    <b><?= esc($u['username']) ?></b>
                                </td>

                                <td>
                                    <?= $u['failed_attempt'] ?>x
                                </td>

                                <td>
                                    <?= esc($u['ip_address']) ?>
                                </td>

                                <td>
                                    <?= date('d-m-Y H:i', strtotime($u['locked_until'])) ?>
                                </td>

                                <td>
                                    <!-- TANPA JS → LANGSUNG SUBMIT -->
                                    <form action="<?= base_url('sudo/unlock-user/' . $u['id_user']) ?>"
                                          method="POST"
                                          onsubmit="return confirm('Unlock akun ini?')">

                                        <?= csrf_field() ?>

                                        <button class="btn btn-sm btn-success">
                                            🔓 Unlock
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>

                        </tbody>
                    </table>
                </div>

            <?php endif; ?>
        </div>
    </div>

</div>

<?php $this->endSection(); ?>