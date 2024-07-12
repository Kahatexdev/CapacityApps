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
                    <div class="d-flex justify-content-between">
                        <h5>
                            <?= $title ?> 
                        </h5>
                        <div class="col-8 text-end">
                            <form action="<?= base_url('capacity/exportSummaryPerTgl') ?>" method="post" ?>
                                <input type="hidden" class="form-control" name="buyer" value="<?= $dataFilter['buyer'] ?>">
                                <input type="hidden" class="form-control" name="jarum" value="<?= $dataFilter['jarum'] ?>">
                                <input type="hidden" class="form-control" name="pdk" value="<?= $dataFilter['pdk'] ?>">
                                <input type="hidden" class="form-control" name="awal" value="<?= $dataFilter['awal'] ?>">
                                <input type="hidden" class="form-control" name="akhir" value="<?= $dataFilter['akhir'] ?>">
                                <button type="submit" class="btn btn-info"><i class="fas fa-file-import text-lg opacity-10" aria-hidden="true"></i> Report Excel</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="example" class="table table-border" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" colspan="7" style="text-align: center;">Tanggal</th>
                                        <?php foreach ($tglProdUnik as $tgl_produksi) : ?>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" colspan="2" style="text-align: center;"><?= date('d-M', strtotime($tgl_produksi)) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Needle</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">No Model</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Style Size</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Qty PO (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Running</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total Prod (dz)</th>
                                        <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Total Jl Mc</th>
                                        <?php foreach ($tglProdUnik as $tgl_produksi) : ?>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Prod (dz)</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2" style="text-align: center;">Jl Mc</th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $ttl_prod = 0;
                                    $ttl_jlmc = 0;
                                    $totalProdPerModel = array_fill_keys($tglProdUnik, 0);
                                    $totalJlMcPerModel = array_fill_keys($tglProdUnik, 0);
                                    foreach ($uniqueData as $key => $id) : 
                                        $ttl_prod += $id['ttl_prod'];
                                        $ttl_jlmc += $id['ttl_jlmc'];
                                        ?>                                            
                                        <tr>
                                            <td class="text-sm"><?= $id['machinetypeid']; ?></td>
                                            <td class="text-sm"><?= $id['mastermodel']; ?></td>
                                            <td class="text-sm"><?= $id['size']; ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= number_format($id['qty']/24,2); ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= $id['running']; ?> days</td>
                                            <td class="text-sm" style="text-align: center;"><?= number_format($id['ttl_prod']/24,2); ?></td>
                                            <td class="text-sm" style="text-align: center;"><?= $id['ttl_jlmc']; ?></td>
                                            <?php foreach ($tglProdUnik as $tgl_produksi2) : ?>
                                                <?php 
                                                $qty_produksi = 0;
                                                $jl_mc = 0;
                                                foreach ($prodSummaryPertgl as $prod) :  
                                                    if ($id['machinetypeid'] == $prod['machinetypeid'] && $id['mastermodel'] == $prod['mastermodel']
                                                    && $id['size'] == $prod['size'] && $tgl_produksi2 == $prod['tgl_produksi']) :
                                                        $qty_produksi = $prod['qty_produksi'];
                                                        $jl_mc = $prod['jl_mc'];
                                                        break;
                                                    endif; 
                                                endforeach; 
                                                $totalProdPerModel[$tgl_produksi2] += $qty_produksi;
                                                $totalJlMcPerModel[$tgl_produksi2] += $jl_mc;
                                                ?>
                                                <td class="text-sm" style="text-align: center;"><?= number_format($qty_produksi/24,2); ?></td>
                                                <td class="text-sm" style="text-align: center;"><?= $jl_mc; ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php if (!isset($uniqueData[$key+1]) || (isset($uniqueData[$key+1]) && $uniqueData[$key+1]['mastermodel'] != $id['mastermodel'])) : ?>
                                            <tr>
                                                <th colspan="4" style="text-align: center;">Total <?= $id['mastermodel'] ?></th>
                                                <th>:</th>
                                                <th style="text-align: center;"><?= number_format($ttl_prod/24, 2); ?></th>
                                                <th style="text-align: center;"><?= $ttl_jlmc; ?></th>
                                                <?php foreach ($tglProdUnik as $tgl_produksi) : ?>
                                                    <th style="text-align: center;"><?= number_format($totalProdPerModel[$tgl_produksi]/24, 2); ?></th>
                                                    <th style="text-align: center;"><?= $totalJlMcPerModel[$tgl_produksi]; ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                            <?php
                                            $ttl_prod = 0;
                                            $ttl_jlmc = 0;
                                            $totalProdPerModel = array_fill_keys($tglProdUnik, 0);
                                            $totalJlMcPerModel = array_fill_keys($tglProdUnik, 0);
                                        endif;
                                    endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>        
<script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
<?php $this->endSection(); ?>