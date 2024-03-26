<?php
namespace App\Helpers;

use CodeIgniter\Database\ConnectionInterface;

class DateHelper
{
    protected $db;

    public function __construct(ConnectionInterface &$db)
    {
        $this->db = &$db;
    }

    public function getWorkingDays($targetDate)
    {
        // Current date
        $currentDate = date('Y-m-d');

        // Calculate number of days between current date and target date
        $interval = date_diff(date_create($currentDate), date_create($targetDate));
        $daysDifference = $interval->days;

        // Calculate the number of weekends between current date and target date
        $weekendDays = intval($daysDifference / 7) * 2;

        // Adjust for remaining days
        $remainingDays = $daysDifference % 7;
        if ($remainingDays > 0) {
            $startDay = date('N', strtotime($currentDate));
            $endDay = (date('N', strtotime($currentDate . " +$remainingDays day")) + $remainingDays) % 7;
            if ($endDay < $startDay) {
                $weekendDays += 2;
            } elseif ($endDay == $startDay) {
                $weekendDays += 1;
            }
        }

        // Fetch holidays from the database
        $holidays = $this->db->table('holidays')
                             ->where('date >=', $currentDate)
                             ->where('date <=', $targetDate)
                             ->get()
                             ->getResultArray();

        // Count the number of holidays
        $numHolidays = count($holidays);

        // Subtract holidays from the total number of days
        $totalDays = $daysDifference - $numHolidays - $weekendDays;

        return $totalDays;
    }
}
?>