<?php

namespace App\Models;


use DateTime;
use CodeIgniter\Model;

class DataMesinModel extends Model
{
    protected $table            = 'data_mesin';
    protected $primaryKey       = 'id_data_mesin';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_data_mesin', 'area', 'jarum', 'total_mc', 'brand', 'mesin_jalan', 'aliasjarum`'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
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


    public function getJarum()
    {
        // Mengambil nilai unik dari kolom 'jarum'
        $query = $this->distinct()->select('jarum')->orderBy('jarum', 'ASC')->findAll();

        // Mengubah hasil query menjadi array dengan nilai 'jarum' saja
        $uniqueJarums = array_column($query, 'jarum');
        return $uniqueJarums;
    }

    public function getArea()
    {
        // Mengambil nilai unik dari kolom 'area' where pu = $pu
        $query = $this->distinct()
            ->select('area')
            ->orderBy('id_data_mesin', 'ASC')
            ->findAll();

        // Mengubah hasil query menjadi array dengan nilai 'area' saja
        $uniqueArea = array_column($query, 'area');
        return $uniqueArea;
    }

    public function getJarumByArea($area)
    {
        $query = $this->distinct()
            ->select('jarum')
            ->orderBy('id_data_mesin', 'ASC')
            ->where('area', $area)
            ->findAll();

        // Extract only the 'jarum' field from the data
        $uniqueJarum = array_column($query, 'jarum');
        return $uniqueJarum;
    }

    public function getArea2($pu)
    {
        // Mengambil nilai unik dari kolom 'area' where pu = $pu
        $query = $this->distinct()
            ->select('area')
            ->where('pu', $pu) // Add where clause for field 'pu' equals $pu
            ->orderBy('id_data_mesin', 'ASC')
            ->findAll();

        // Mengubah hasil query menjadi array dengan nilai 'area' saja
        $uniqueArea = array_column($query, 'area');
        return $uniqueArea;
    }


    public function getJarumArea($area)
    {
        $query = $this->select('*')->where('area', $area)->findAll();

        return $query;
    }

    public function getpu($area)
    {
        $query = $this->select('pu')->where('area', $area)->get()->getRow();
        return $query ? $query->pu : ''; // Return the value of 'pu' if a row is found, otherwise return an empty string
    }


    public function getMesinPerJarum($jarum, $pu)
    {
        $query = $this->select('*')
            ->where('jarum', $jarum)
            ->where('pu', $pu) // Add where clause for field 'pu' equals $pu
            ->findAll();

        return $query;
    }


    public function getTotalMesinByJarum2($pu)
    {
        $customOrder = [
            'JC120' => 1,
            'TJ120' => 2,
            'JC144' => 3,
            'TJ144' => 4,
            'JC168' => 5,
            'TJ168' => 6
        ];

        // Generate the CASE statement for custom ordering
        $caseStatement = "CASE ";
        foreach ($customOrder as $jarum => $index) {
            $caseStatement .= "WHEN jarum = '$jarum' THEN $index ";
        }
        // For '10G', '13G', '240N', and 'POM-POM' entries, set the index to a very large number to move them to the end
        $caseStatement .= "WHEN jarum LIKE '10G%' THEN 1000 ";
        $caseStatement .= "WHEN jarum = '13G' THEN 1001 ";
        $caseStatement .= "WHEN jarum = '240N' THEN 1002 ";
        $caseStatement .= "WHEN jarum = 'POM-POM' THEN 1003 ";
        $caseStatement .= "ELSE " . (count($customOrder) + 1) . " END";

        return $this->select('jarum, SUM(total_mc) as total')
            ->where('pu', $pu) // Add where clause for field 'pu' equals $pu
            ->groupBy('jarum')
            ->orderBy($caseStatement . ', jarum')
            ->findAll();
    }

    public function getTotalMesinByJarum()
    {
        $customOrder = [
            'JC120' => 1,
            'TJ120' => 2,
            'JC144' => 3,
            'TJ144' => 4,
            'JC168' => 5,
            'TJ168' => 6
        ];

        // Generate the CASE statement for custom ordering
        $caseStatement = "CASE ";
        foreach ($customOrder as $jarum => $index) {
            $caseStatement .= "WHEN jarum = '$jarum' THEN $index ";
        }
        // For '10G', '13G', '240N', and 'POM-POM' entries, set the index to a very large number to move them to the end
        $caseStatement .= "WHEN jarum LIKE '10G%' THEN 1000 ";
        $caseStatement .= "WHEN jarum = '13G' THEN 1001 ";
        $caseStatement .= "WHEN jarum = '240N' THEN 1002 ";
        $caseStatement .= "WHEN jarum = 'POM-POM' THEN 1003 ";
        $caseStatement .= "ELSE " . (count($customOrder) + 1) . " END";

        return $this->select('jarum, SUM(total_mc) as total')
            ->groupBy('jarum')
            ->orderBy($caseStatement . ', jarum')
            ->findAll();
    }

    public function getTotalMesinCjByJarum($jarum, $aliasjarum)
    {
        return $this->select('jarum, SUM(total_mc) as total')
            ->where('pu', 'CJ')
            ->where('aliasjarum', $aliasjarum)
            ->where('jarum', $jarum)
            ->groupBy('jarum')
            ->orderBy('jarum', 'ASC')
            ->findAll();
    }

    public function mcJalan()
    {
        return $this->selectSum('mesin_jalan')->get()->getRow()->mesin_jalan;
    }
    public function totalMc()
    {
        return $this->selectSum('total_mc')->get()->getRow()->total_mc;
    }
    public function getAreaModel($noModel)
    {
        return $this->select('data_mesin.*')
            ->join('apsperstyle', 'data_mesin.jarum = apsperstyle.machinetypeid', 'left')
            ->where('apsperstyle.mastermodel', $noModel)
            ->get()
            ->getResult();
    }
    public function getDC()
    {
        return $this->select('aliasjarum, jarum')->like('jarum', 'DC')->findAll();
    }
    public function getBabyComp()
    {
        return $this->select('aliasjarum, jarum')->like('aliasjarum', 'Baby Comp N84')->orLike('aliasjarum', 'Baby Comp N96')->groupBy('aliasjarum')->findAll();
    }
    public function getBabyComp108()
    {
        return $this->select('aliasjarum, jarum')->like('aliasjarum', 'Baby Comp N108')->groupBy('aliasjarum')->findAll();
    }
    public function getChildComp120()
    {
        return $this->select('aliasjarum, jarum')->like('aliasjarum', 'Child Comp N120')->groupBy('aliasjarum')->findAll();
    }
    public function getLadyComp()
    {
        return $this->select('aliasjarum, jarum')->like('aliasjarum', 'Ladies Comp')->groupBy('aliasjarum')->findAll();
    }
    public function getMensComp168()
    {
        return $this->select('aliasjarum, jarum')->like('aliasjarum', 'Mens Comp N168')->groupBy('aliasjarum')->findAll();
    }
    public function getMensComp200()
    {
        return $this->select('aliasjarum, jarum')->like('aliasjarum', 'Mens Comp N200')->groupBy('aliasjarum')->findAll();
    }
    public function getChildComp132()
    {
        return $this->select('aliasjarum, jarum')->like('aliasjarum', 'Child Comp N132')->groupBy('aliasjarum')->findAll();
    }
    public function getMensComp156()
    {
        return $this->select('aliasjarum, jarum')->like('aliasjarum', 'Mens Comp N156')->groupBy('aliasjarum')->findAll();
    }
    public function getAllBrand($aliasjarum)
    {
        return $this->select('brand, jarum, SUM(total_mc) AS total_mc, SUM(mesin_jalan) AS mesin_jalan, SUM(IF(pu="CJ", total_mc, 0)) AS cj, SUM(IF(pu="MJ", total_mc, 0)) AS mj')->where('aliasjarum', $aliasjarum)->groupBy('brand')->findAll();
    }
    public function getBrand($jarum, $brand)
    {
        $result = $this->selectSum('total_mc')->where('aliasjarum', $jarum)->like('brand', $brand)->get()->getRow();

        // Periksa apakah hasilnya ada sebelum mengembalikannya
        return $result ? $result->total_mc : 0; // Mengembalikan total_mc jika ada, jika tidak, kembalikan 0 atau nilai default lainnya
    }
    public function getRunningMc($jarum, $brand)
    {
        $result = $this->selectSum('mesin_jalan')->where('aliasjarum', $jarum)->like('brand', $brand)->get()->getRow();

        // Periksa apakah hasilnya ada sebelum mengembalikannya
        return $result ? $result->mesin_jalan : 0; // Mengembalikan total_mc jika ada, jika tidak, kembalikan 0 atau nilai default lainnya

    }
    public function getRunningMcPU($jarum, $pu)
    {
        $result = $this->selectSum('mesin_jalan')->where('aliasjarum', $jarum)->like('pu', $pu)->get()->getRow();

        // Periksa apakah hasilnya ada sebelum mengembalikannya
        return $result ? $result->mesin_jalan : 0; // Mengembalikan total_mc jika ada, jika tidak, kembalikan 0 atau nilai default lainnya

    }
    public function getStockSylDc($needle, $brand)
    {
        $qry = $this->join('data_cylinder', 'data_mesin.jarum = data_cylinder.needle')
            ->select('data_cylinder.qty')
            ->like('data_cylinder.type_machine',  $brand)
            ->where('data_cylinder.needle', $needle)
            ->get()->getRow();

        return $qry->qty ?? 0;
    }

    public function getAllMachine()
    {
        $query = $this->select('area, jarum, SUM(total_mc) AS total_mc, brand, SUM(mesin_jalan) AS mesin_jalan,SUM(total_mc)-SUM(mesin_jalan) as mesin_mati, pu')
            ->groupBy('pu, brand, area , jarum')
            ->having('SUM(total_mc) !=', 0)
            ->orderBy('pu ASC, SUBSTRING(AREA, 3) + 0 ASC, AREA ASC') // Sorting by 'pu' first, then by KK number, then by Area
            ->findAll();

        return $query;
    }
    public function listmachine($jarum)
    {
        return $this->select('data_mesin.area,data_mesin.jarum,sum(data_mesin.total_mc) as total_mc,data_mesin.brand,sum(data_mesin.mesin_jalan) as mesin_jalan,pu')
            ->where('data_mesin.jarum', $jarum)
            ->where('data_mesin.total_mc !=', 0)
            ->groupBy('data_mesin.brand,data_mesin.jarum,data_mesin.area,data_mesin.pu')
            ->orderBy('pu', 'total_mc', 'Desc')
            ->findAll();
    }
    public function getMesinByArea($area, $jarum)
    {
        $a = $this->select('sum(total_mc) as mesin')
            ->where('area', $area)
            ->where('jarum', $jarum)
            ->first();

        $mesin = reset($a);
        return $mesin;
    }
    public function getJalanMesinPerArea()
    {
        $query = $this->select('area, jarum, SUM(total_mc) AS total_mc, SUM(mesin_jalan) AS mesin_jalan,SUM(total_mc)-SUM(mesin_jalan) as mesin_mati, pu')
            ->groupBy('pu, area')
            ->orderBy('pu ASC, SUBSTRING(AREA, 3) + 0 ASC, AREA ASC') // Sorting by 'pu' first, then by KK number, then by Area
            ->findAll();

        return $query;
    }
    public function jarumPerArea()
    {
        $query = $this->select('area, jarum, SUM(total_mc) AS total_mc, SUM(mesin_jalan) AS mesin_jalan, SUM(total_mc) - SUM(mesin_jalan) as mesin_mati, pu')
            ->groupBy('pu, area, jarum')
            ->orderBy('pu ASC, SUBSTRING(AREA, 3) + 0 ASC, AREA ASC')
            ->findAll();

        // Array untuk menyimpan hasil akhir
        $formattedData = [];

        foreach ($query as $row) {
            $area = strtoupper($row['area']);
            $jarum = strtolower($row['jarum']);

            // Jika area belum ada di array, tambahkan dengan array kosong
            if (!isset($formattedData[$area])) {
                $formattedData[$area] = [
                    'totalmc' => 0
                ];
            }

            // Tambahkan total_mc untuk jarum tertentu
            if (!isset($formattedData[$area][$jarum])) {
                $formattedData[$area][$jarum] = 0;
            }

            $formattedData[$area][$jarum] += $row['total_mc'];

            // Tambahkan total_mc untuk area
            $formattedData[$area]['totalmc'] += $row['total_mc'];
        }

        return $formattedData;
    }
    public function maxCapacity($area, $jarum)
    {
        $totalmc = $this->select('sum(total_mc) as total')->where('area', $area)->where('jarum', $jarum)->first();
        $maxCapacity = $totalmc['total'] * 7 * 14;
        $data = [
            'totalmesin' => $totalmc['total'],
            'maxCapacity' => $maxCapacity
        ];
        return $data;
    }

    public function getCapacityArea($area, $jarum)
    {
        $today = date('Y-m-d');
        $maxDay = strtotime('+90 Days');
    }
    public function getAreaAndJarum()
    {
        $customOrder = [
            'JC84' => 1,
            'JC96' => 2,
            'JC108' => 3,
            'JC120' => 4,
            'JC144' => 5,
            'JC168' => 6,
            'TJ96' => 7,
            'TJ108' => 8,
            'TJ120' => 9,
            'TJ144' => 10,
            'TJ168' => 11
        ];

        // Generate the CASE statement for custom ordering
        $caseStatement = "CASE ";
        foreach ($customOrder as $jarum => $index) {
            $caseStatement .= "WHEN jarum = '$jarum' THEN $index ";
        }
        // For '10G', '13G', '240N', and 'POM-POM' entries, set the index to a very large number to move them to the end
        $caseStatement .= "WHEN jarum LIKE '10G%' THEN 1000 ";
        $caseStatement .= "WHEN jarum = '13G' THEN 1001 ";
        $caseStatement .= "WHEN jarum = '240N' THEN 1002 ";
        $caseStatement .= "WHEN jarum = 'POM-POM' THEN 1003 ";
        $caseStatement .= "ELSE " . (count($customOrder) + 1) . " END";

        return $this->select('jarum, SUM(total_mc) as total')
            ->groupBy('jarum')
            ->orderBy($caseStatement . ', jarum')
            ->findAll();
    }
    public function totalMcArea($ar)
    {
        return $this->select('sum(total_mc) as Total')
            ->where('area', $ar)
            ->first();
    }

    public function getAliasJrm()
    {
        return $this->select('aliasjarum, jarum')->groupBy('aliasjarum')->findAll();
    }
}
