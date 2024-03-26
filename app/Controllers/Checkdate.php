<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Checkdate extends BaseController
{
    protected $dateHelper;
    public function __construct()
    {
        $this->dateHelper = new DateHelper(db_connect());
    }

    public function index()
    {
        // Get the number of working days between current date and November 30th, 2024
        $workingDays = $this->dateHelper->getWorkingDays('2024-03-22');

        // Output the result
        echo "Number of working days: $workingDays";
    }
}
