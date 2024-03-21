<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportModel extends Model
{
    protected $table = 'aps_order_report';
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key
    protected $allowedFields = [
        // Define all the fields that are allowed to be filled during insertion or update
        'recordID', 'articleNo', 'delivery', 'qty', 'country', 'color', 'size', 'smv',
        'machinetypeid', 'processRoute', 'lcoDate', 'no_model'
    ];
}
