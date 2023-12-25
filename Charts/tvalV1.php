<?php
include "conn.php";
// Include your database connection file (conn.php)
$query = "SELECT * FROM DATA";
$result = mysqli_query($conn, $query);

// Initialize counters for each category
$countBelow500 = 0;
$countBetween500And1500 = 0;
$countBetween1500And3000 = 0;
$countBetween3000And5000 = 0;
$countAbove5000 = 0;

while ($data = mysqli_fetch_array($result)) {
    $Credit = $data['Deposits'];
    $Debit = $data['Withdrawals'];

    // Categorize the data
    if ($Debit < 500 && $Credit < 500) {
        $countBelow500++;
    } elseif (($Debit >= 500 && $Debit < 1500) || ($Credit >= 500 && $Credit < 1500)) {
        $countBetween500And1500++;
    } elseif (($Debit >= 1500 && $Debit < 3000) || ($Credit >= 1500 && $Credit < 3000)) {
        $countBetween1500And3000++;
    } elseif (($Debit >= 3000 && $Debit < 5000) || ($Credit >= 3000 && $Credit < 5000)) {
        $countBetween3000And5000++;
    } elseif ($Debit >= 5000 || $Credit >= 5000) {
        $countAbove5000++;
    }
}

// Close the database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaction Count Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="transactionChart" style="max-height: 400px; width: 100%; max-width: 600px;"></canvas>

    <script>
        var ctx = document.getElementById('transactionChart').getContext('2d');

        var chartData = {
            labels: ['Below 500', 'Between 500-1500', 'Between 1500-3000', 'Between 3000-5000', 'Above 5000'],
            datasets: [
                {
                    label: 'Transaction Count',
                    data: [<?php echo $countBelow500; ?>, <?php echo $countBetween500And1500; ?>, <?php echo $countBetween1500And3000; ?>, <?php echo $countBetween3000And5000; ?>, <?php echo $countAbove5000; ?>],
                    backgroundColor: ['#0d47a1', '#1565c0', '#1976d2', '#1e88e5', '#2196f3'],
                    borderColor: '#0d47a1',
                    borderWidth: 1
                }
            ]
        };

        var chartOptions = {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Transaction Count'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Transaction Categories'
                    }
                }
            }
        };

        var transactionChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: chartOptions
        });
    </script>
</body>
</html>
