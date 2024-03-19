<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'data_booking';
    protected $primaryKey       = 'id_booking';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_booking', 'tgl_terima_booking', 'kd_buyer_booking', 'id_product_type', 'no_order', 'no_booking', 'desc', 'opd', 'delivery', 'qty_booking', 'sisa_booking', 'needle', 'seam'];

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

    public function checkExist($validate)
    {
        $query = $this->where('no_order', $validate['no_order'])
            ->where('no_booking ', $validate['no_pdk'])->first();
        return $query;
    }
    public function getAllData()
    {
        return $this->select('data_booking.*, master_product_type.product_type')
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->findAll();
    }
    public function getDataById($idBooking)
    {
        return $this->select('data_booking.*, master_product_type.product_type')
            ->where('id_booking', $idBooking)
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->first();
    }
    public function getDataPerjarum($jarum)
    {
        return $this->select('data_booking.*, master_product_type.product_type')
            ->where('needle', $jarum)
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->findAll();
    }
}
