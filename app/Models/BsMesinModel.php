<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;
use DateTime;

class BsMesinModel extends Model
{
    protected $table            = 'bs_mesin';
    protected $primaryKey       = 'id_bsmc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_karyawan', 'nama_karyawan', 'shift', 'area', 'no_model', 'size', 'inisial', 'no_mesin', 'qty_pcs', 'qty_gram', 'tanggal_produksi', 'created_at', 'update_at'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'update_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public function bsDataKaryawan($id)
    {
        return $this->select('id_karyawan, nama_karyawan, sum(qty_gram) as gram, sum(qty_pcs) as pcs')
            ->where('id_karyawan', $id)
            ->groupBy('id_karyawan')
            ->findAll();
    }
    public function bsPeriode($start, $stop)
    {
        return $this->select('id_karyawan, nama_karyawan, sum(qty_gram) as gram, sum(qty_pcs) as pcs')
            ->where('tanggal_produksi >=', $start)
            ->where('tanggal_produksi <=', $stop)
            ->groupBy('id_karyawan')
            ->findAll();
    }
    public function bsDaily($start, $stop)
    {
        return $this->select('tanggal_produksi, id_karyawan, nama_karyawan, sum(qty_gram) as gram, sum(qty_pcs) as pcs')
            ->where('tanggal_produksi >=', $start)
            ->where('tanggal_produksi <=', $stop)
            ->groupBy('tanggal_produksi,id_karyawan,')
            ->findAll();
    }
    public function bsMesinPerbulan($area, $bulan)
    {
        $bulanDateTime = DateTime::createFromFormat('F-Y', $bulan);
        $tahun = $bulanDateTime->format('Y'); // 2024
        $bulanNumber = $bulanDateTime->format('m'); // 12
        return $this->select('nama_karyawan, qty_pcs, qty_gram,no_model,size, no_mesin, tanggal_produksi,area')
            ->where('MONTH(tanggal_produksi)', $bulanNumber) // Filter bulan
            ->where('YEAR(tanggal_produksi)', $tahun) // Filter bulan
            ->where('area', $area) // Filter area
            ->groupBy('no_model, size, tanggal_produksi') // Kelompokkan berdasarkan karyawan dan tanggal
            ->findAll();
    }
    public function totalGramPerbulan($area, $bulan)
    {
        $bulanDateTime = DateTime::createFromFormat('F-Y', $bulan);
        $tahun = $bulanDateTime->format('Y'); // 2024
        $bulanNumber = $bulanDateTime->format('m'); // 12
        $bs = $this->select('sum(qty_gram) as totalGram')
            ->where('MONTH(tanggal_produksi)', $bulanNumber) // Filter bulan
            ->where('YEAR(tanggal_produksi)', $tahun) // Filter bulan
            ->where('area', $area) // Filter area
            ->first();
        $data = reset($bs);
        return $data;
    }
    public function totalPcsPerbulan($area, $bulan)
    {
        $bulanDateTime = DateTime::createFromFormat('F-Y', $bulan);
        $tahun = $bulanDateTime->format('Y'); // 2024
        $bulanNumber = $bulanDateTime->format('m'); // 12
        $bs = $this->select('sum(qty_pcs) as totalPcs')
            ->where('MONTH(tanggal_produksi)', $bulanNumber) // Filter bulan
            ->where('YEAR(tanggal_produksi)', $tahun) // Filter bulan
            ->where('area', $area) // Filter area
            ->first();
        $data = reset($bs);
        return $data;
    }
    public function ChartPdk($area, $bulan)
    {
        $bulanDateTime = DateTime::createFromFormat('F-Y', $bulan);
        $tahun = $bulanDateTime->format('Y'); // 2024
        $bulanNumber = $bulanDateTime->format('m'); // 12
        $bs = $this->select('no_model, sum(qty_gram) as totalGram')
            ->where('MONTH(tanggal_produksi)', $bulanNumber) // Filter bulan
            ->where('YEAR(tanggal_produksi)', $tahun) // Filter bulan
            ->where('area', $area) // Filter area
            ->groupBy('no_model,area') // Filter area
            ->orderBy('totalGram', 'DESC')
            ->findAll();
        return $bs;
    }
    public function getBsMesinPph($area, $nomodel, $size)
    {
        return $this->select('apsperstyle.factory, apsperstyle.mastermodel, apsperstyle.size, (SELECT SUM(qty_gram) FROM bs_mesin AS b WHERE b.no_model = apsperstyle.mastermodel AND b.area = apsperstyle.factory AND b.size = apsperstyle.size) AS bs_gram,')
            ->join(
                'apsperstyle',
                'apsperstyle.factory = bs_mesin.area AND apsperstyle.mastermodel = bs_mesin.no_model AND apsperstyle.size = bs_mesin.size',
                'left'
            )
            ->where('apsperstyle.factory', $area)
            ->where('apsperstyle.mastermodel', $nomodel)
            ->where('apsperstyle.size', $size)
            ->first(); // Ambil satu hasil
    }
    public function getBsMesinHarian(array $mastermodels, array $sizes, string $tanggal)
    {
        return $this->select('no_model, size, SUM(qty_gram) as bs_mesin')
            ->whereIn('no_model', $mastermodels)
            ->whereIn('size', $sizes)
            ->where('tanggal_produksi', $tanggal)
            ->groupBy('no_model, size')
            ->findAll();
    }
}
