<!-- MasterCalendar.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Calendar</title>
    <style>
        /* Add your CSS styles here */
        .highlight {
            background-color: yellow;
        }
        table {
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Master Calendar</h1>
        <?php echo $calendar; ?>
    </div>
</body>
</html>
