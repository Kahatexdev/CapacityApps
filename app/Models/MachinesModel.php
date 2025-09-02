<?php

namespace App\Models;

use CodeIgniter\Model;

class MachinesModel extends Model
{
  protected $table            = 'machines';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $returnType       = 'array';
  protected $useSoftDeletes   = false;
  protected $protectFields    = true;
  protected $allowedFields    = [
    'no_mc',
    'jarum',
    'brand',
    'dram',
    'kode',
    'tahun',
    'area',
    'status',
    'created_at',
    'updated_at'
  ];

  protected bool $allowEmptyInserts = false;

  // Dates
  protected $useTimestamps = true;
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

  public function getDataMcArea(string $area): array
  {
    if ($area === '') return [];

    return $this->select('id,no_mc, jarum, brand, dram, kode, tahun, area, status')
      ->where('area', $area)
      ->findAll(); // findAll() lebih ringkas daripada get()->getResultArray()
  }

  public function getMachineWithProduksi($tanggal, $area)
  {
    $db = \Config\Database::connect();

    $sql = "
            SELECT
              machines.id,
              machines.no_mc,
              machines.jarum,
              machines.area,
              p.id_produksi,
              p.idapsperstyle,
              p.tgl_produksi,
              p.mastermodel,
              p.inisial,
              CASE
                WHEN p.tgl_produksi IS NULL THEN machines.status
                ELSE 'running'
              END AS status
            FROM machines
            LEFT JOIN (
              SELECT 
                produksi.no_mesin,
                produksi.tgl_produksi,
                produksi.area,
                produksi.id_produksi,
                produksi.idapsperstyle,
                apsperstyle.mastermodel,
                apsperstyle.inisial,
                apsperstyle.machinetypeid
              FROM produksi
              LEFT JOIN apsperstyle
                ON apsperstyle.idapsperstyle = produksi.idapsperstyle
              WHERE produksi.tgl_produksi = ?
                AND produksi.area = ?
            ) p
              ON machines.no_mc = p.no_mesin
             AND machines.jarum = p.machinetypeid
            WHERE machines.area = ?
            ORDER BY machines.id ASC
        ";

    return $db->query($sql, [$tanggal, $area, $area])->getResult();
  }
  public function mesinPerJarum($jarum, $area)
  {
    return $this->where('jarum', $jarum)
      ->where('area', $area)
      ->findAll();
  }
}
