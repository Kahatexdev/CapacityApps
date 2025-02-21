<?php

namespace App\Models;

use CodeIgniter\Model;

class DataCancelOrderModel extends Model
{
    protected $table            = 'data_cancel_order';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'idapsperstyle',
        'qty_cancel',
        'alasan',
    ];

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

    public function getDataCancel()
    {
        return $this->select('SUM(data_cancel_order.qty_cancel) AS qty_cancel, data_cancel_order.alasan, apsperstyle.mastermodel, apsperstyle.no_order, apsperstyle.delivery, apsperstyle.machinetypeid, data_model.kd_buyer_order, data_model.seam, data_model.description')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle=data_cancel_order.idapsperstyle', 'left')
            ->join('data_model', 'apsperstyle.mastermodel=data_model.no_model', 'left')
            ->groupBy('apsperstyle.mastermodel, apsperstyle.machinetypeid')
            ->findAll();
    }
    public function getDetailCancel($pdk)
    {
        return $this->select('SUM(data_cancel_order.qty_cancel) AS qty_cancel, data_cancel_order.alasan, apsperstyle.mastermodel, apsperstyle.size, apsperstyle.no_order, apsperstyle.delivery, apsperstyle.machinetypeid, data_model.kd_buyer_order, data_model.seam, data_model.description')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle=data_cancel_order.idapsperstyle', 'left')
            ->join('data_model', 'apsperstyle.mastermodel=data_model.no_model', 'left')
            ->where('apsperstyle.mastermodel', $pdk)
            ->groupBy('apsperstyle.machinetypeid, apsperstyle.size')
            ->findAll();
    }
}
