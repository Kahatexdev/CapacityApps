<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LiburModel; // Import the HolidayModel

class Checkdate extends BaseController
{
    public function generateWeeklyRanges()
    {
        // Set the start date to the first day of the current month
        $startDate = new \DateTime('first day of this month');

        // Load the HolidayModel
        $LiburModel = new LiburModel();

        // Get all holidays from the database
        $holidays = $LiburModel->findAll();

        // Initialize variable to keep track of the current month
        $currentMonth = null;

        // Initialize variable to keep track of the current month
        $currentMonth = null;

        
        // Loop for 10 years (520 weeks)
        for ($year = 0; $year < 10; $year++) {
            // Loop for 52 weeks (1 year)
            for ($i = 0; $i < 52; $i++) {
                // Calculate the start of the week
                $startOfWeek = clone $startDate;
                $startOfWeek->modify("$year year"); // Increment the year dynamically
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
                $HolidayDays = 7 - $numberOfDays;

                // Output the range along with the number of days
                echo "Week " . (($year * 52) + $i + 1) . ": $startOfWeekFormatted to $endOfWeekFormatted ($numberOfDays Hari Kerja)
                ";
                if($HolidayDays != 0){
                    echo "($HolidayDays Hari Libur)";
                }
                echo "<br>";
            }
        }
    }
}
