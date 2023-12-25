<?php
// Include your database connection file (conn.php)
include "conn.php";

// Fetch data from the database
$query = "SELECT * FROM DATA";
$result = mysqli_query($conn, $query);

// Initialize counters for different categories
$countAbove5000 = 0;
$countBetween2500And5000 = 0;
$countBetween1000And2500 = 0;
$countBelow1000 = 0;

while ($data = mysqli_fetch_array($result)) {
    $Credit = $data['Deposits'];
    $Debit = $data['Withdrawals'];

    // Categorize the data
    if ($Debit > 5000 || $Credit > 5000) {
        $countAbove5000++;
    } elseif (($Debit >= 2500 && $Debit <= 5000) || ($Credit >= 2500 && $Credit <= 5000)) {
        $countBetween2500And5000++;
    } elseif (($Debit >= 1000 && $Debit < 2500) || ($Credit >= 1000 && $Credit < 2500)) {
        $countBetween1000And2500++;
    } else {
        $countBelow1000++;
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bar Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<canvas id="barChart" ></canvas>

<script>
var ctx = document.getElementById('barChart').getContext('2d');

var chartData = {
    labels: ['Above 5000', 'Between 2500-5000', 'Between 1000-2500', 'Below 1000'],
    datasets: [
        {
            label: 'Data Count',
            data: [<?php echo $countAbove5000; ?>, <?php echo $countBetween2500And5000; ?>, <?php echo $countBetween1000And2500; ?>, <?php echo $countBelow1000; ?>],
            backgroundColor: ['#0d47a1', '#1976d2', '#2196f3', '#bbdefb'],
            borderColor: ['#0d47a1', '#1976d2', '#2196f3', '#bbdefb'],
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
                text: 'Data Count'
            }
        }
    }
};

var barChart = new Chart(ctx, {
    type: 'bar',
    data: chartData,
    options: chartOptions
});
</script>
<div id="tval.php" style="width: 225px; height: 125px;"></div>
</body>
</html>
