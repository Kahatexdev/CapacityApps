<?php

namespace App\Models;

use CodeIgniter\Model;

class TransferModel extends Model
{
    protected $table = 'transfers';
    protected $primaryKey = 'id_transfer';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['from_id', 'qty_transfer', 'to_id', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
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

    public function getTransferData($idBooking)
    {
        $result = $this->select('transfers.from_id, transfers.qty_transfer, transfers.created_at, data_booking.kd_buyer_booking,data_booking.no_booking, data_booking.desc, data_booking.no_order, data_booking.delivery')
            ->join('data_booking', 'transfers.to_id = data_booking.id_booking')
            ->where('from_id', $idBooking)
            ->findAll();
        return $result;
    }
}
