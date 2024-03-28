<!DOCTYPE html>
<html>

<head>
    <title>Capacity Calendar</title>
    <!-- Tambahkan stylesheet atau link ke CDN CSS di sini -->
</head>

<body>

    <h1>Capacity Calendar</h1>

    <?php foreach ($weeklyData as $month => $weeks) : ?>
        <h2><?php echo $month; ?></h2>
        <table border="1">
            <tr>
                <th>Week</th>
                <th>Delivery</th>
                <th>Total Qty</th>
            </tr>
            <?php foreach ($weeks as $week => $deliveries) : ?>
                <?php foreach ($deliveries as $delivery => $totalQty) : ?>
                    <tr>
                        <td><?php echo $week; ?></td>
                        <td><?php echo $delivery; ?></td>
                        <td><?php echo $totalQty; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>

</body>

</html>