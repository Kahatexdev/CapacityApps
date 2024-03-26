<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LiburModel; // Import the HolidayModel

class Checkdate extends BaseController
{
    public function generateWeeklyRanges()
    {
        // Specify the start date
        $startDate = new \DateTime('2024-01-01');

        // Load the HolidayModel
        $LiburModel = new LiburModel();

        // Get all holidays from the database
        $holidays = $LiburModel->findAll();

        // Initialize variable to keep track of the current month
        $currentMonth = null;

        // Loop for 52 weeks to generate weekly ranges
        for ($i = 0; $i < 52; $i++) {
            // Calculate the start of the week
            $startOfWeek = clone $startDate;
            $startOfWeek->modify("+$i week");
            $startOfWeek->modify('Monday this week');

            // Calculate the end of the week
            $endOfWeek = clone $startOfWeek;
            $endOfWeek->modify('Sunday this week');

            // Calculate the number of days in the week
            $numberOfDays = $startOfWeek->diff($endOfWeek)->days + 1;

            // Check if any holidays fall within this week
            foreach ($holidays as $holiday) {
                $holidayDate = new \DateTime($holiday['tanggal']);
                if ($holidayDate >= $startOfWeek && $holidayDate <= $endOfWeek) {
                    // Subtract the holiday from the total number of days
                    $numberOfDays--;
                }
            }

            // Get the month of the current week
            $currentMonthOfYear = $startOfWeek->format('F');

            // Output the month header if it's a new month
            if ($currentMonth !== $currentMonthOfYear) {
                echo "<h2>$currentMonthOfYear</h2>";
                $currentMonth = $currentMonthOfYear;
            }

            // Format dates to the desired format
            $startOfWeekFormatted = $startOfWeek->format('Y-m-d');
            $endOfWeekFormatted = $endOfWeek->format('Y-m-d');

            // Output the range along with the number of days
            echo "Week " . ($i + 1) . ": $startOfWeekFormatted to $endOfWeekFormatted ($numberOfDays days)<br>";
        }
    }
}