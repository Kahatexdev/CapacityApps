<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;
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
        return $this->select('nama_karyawan, sum(qty_pcs) as qty_pcs, sum(qty_gram) as qty_gram,no_model,size, no_mesin, tanggal_produksi,area')
            ->where('MONTH(tanggal_produksi)', $bulanNumber) // Filter bulan
            ->where('YEAR(tanggal_produksi)', $tahun) // Filter bulan
            ->where('area', $area) // Filter area
            ->groupBy('no_model, size, tanggal_produksi,no_mesin') // Kelompokkan berdasarkan karyawan dan tanggal
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
        return $this->select('sum(qty_gram) as bs_gram')
            ->where('area', $area)
            ->where('no_model', $nomodel)
            ->where('size', $size)
            ->first(); // Ambil satu hasil
    }
    public function getBsMesin($area, $nomodel, $size)
    {
        return $this->select('sum(qty_gram) as bs_gram')
            ->where('area', $area)
            ->where('no_model', $nomodel)
            ->whereIn('size', $size)
            ->first(); // Ambil satu hasil
    }
    public function getBsMesinHarian(array $mastermodels, array $sizes, string $tanggal, $area)
    {
        return $this->select('no_model, size, SUM(qty_gram) as bs_mesin')
            ->whereIn('no_model', $mastermodels)
            ->whereIn('size', $sizes)
            ->where('tanggal_produksi', $tanggal)
            ->where('area', $area)
            ->groupBy('no_model, size')
            ->findAll();
    }
    public function existingData($insert)
    {
        $query = $this->select('*')
            ->where('nama_karyawan', $insert['nama_karyawan'])
            ->where('shift', $insert['shift'])
            ->where('area', $insert['area'])
            ->where('no_model', $insert['no_model'])
            ->where('no_mesin', $insert['no_mesin'])
            ->where('size', $insert['size'])
            ->where('tanggal_produksi', $insert['tanggal_produksi'])
            ->first(); // Pastikan panggilan get() dilakukan untuk menjalankan query

        // Jika query gagal, get() akan mengembalikan false
        if ($query === false) {
            log_message('error', 'Query failed in existingData(): ' . $this->db->getLastQuery());
            return false;
        }

        return $query;
    }
    public function getbsMesinDaily($filters)
    {
        $builder = $this->select("DATE_FORMAT(tanggal_produksi, '%d-%b') as tanggal_produksi, SUM(qty_gram) as qty_gram")
            ->where('MONTH(tanggal_produksi)',  $filters['bulan'])
            ->where('YEAR(tanggal_produksi)',  $filters['tahun']);

        if (!empty($filters['area'])) {
            $builder->where('area',  $filters['area']);
        }

        $builder->groupBy('tanggal_produksi')
            ->orderBy('tanggal_produksi', 'ASC');

        // Ambil hasil query-nya
        $result = $builder->findAll();

        return $result;
    }
    public function getTotalKgMonth($filters)
    {
        $builder = $this->select(" SUM(qty_gram) as qty_gram")
            ->where('MONTH(tanggal_produksi)',  $filters['bulan'])
            ->where('YEAR(tanggal_produksi)',  $filters['tahun']);

        if (!empty($filters['area'])) {
            $builder->where('area',  $filters['area']);
        }
        // Ambil hasil query-nya
        $result = $builder->first();

        return $result ?? 0;
    }
    public function bsTanggal($fill)
    {
        $builder = $this->select(" SUM(qty_gram) as qty_gram")
            ->where('tanggal_produksi',  $fill['tanggal']);

        if (!empty($fill['area'])) {
            $builder->where('area',  $fill['area']);
        }
        // Ambil hasil query-nya
        $result = $builder->first();

        return $result ?? 0;
    }
    public function bsKary($area, $tanggal)
    {
        // 1. Ambil semua baris BS
        $bsList = $this->select('tanggal_produksi,size,no_model, nama_karyawan, no_mesin, qty_pcs, area, shift')
            ->where('tanggal_produksi', $tanggal)
            ->where('area', $area)
            ->findAll();

        // 2. Deduplikasi per mesin+shift
        $unique = [];
        foreach ($bsList as $row) {
            $key = $row['no_mesin'] . '::' . $row['shift'] . '::' . $row['no_model'] . '::' . $row['size'];
            if (! isset($unique[$key])) {
                $unique[$key] = $row;
            }
        }

        // 3. Hitung produksi sekali per mesin+shift
        $prod = new \App\Models\ProduksiModel();
        $result = [];
        foreach ($unique as $row) {
            $shiftCol = 'shift_' . strtolower($row['shift']);
            $sum = $prod->selectSum($shiftCol)
                ->where('tgl_produksi', $tanggal)
                ->where('area', $area)
                ->where('no_mesin', $row['no_mesin'])
                ->first();
            $row['qty_produksi'] = $sum[$shiftCol] ?? 0;
            $result[] = $row;
        }

        // 4. Grouping per nama_karyawan
        $final = [];
        foreach ($result as $r) {
            $k = $r['nama_karyawan'];
            if (! isset($final[$k])) {
                $final[$k] = [
                    'nama_karyawan'    => $k,
                    'qty_pcs'          => 0,
                    'qty_produksi'     => 0,
                    'area'             => $r['area'],
                    'tanggal_produksi' => $r['tanggal_produksi'],
                    'shift'            => $r['shift'],
                ];
            }
            $final[$k]['qty_pcs']      += $r['qty_pcs'];
            $final[$k]['qty_produksi'] += $r['qty_produksi'];
        }

        return array_values($final);
    }

    public function deleteBsRange($area, $awal, $akhir)
    {
        return $this->where('area', $area)
            ->where('tanggal_produksi >=', $awal)
            ->where('tanggal_produksi <=', $akhir)
            ->delete();
    }
}
