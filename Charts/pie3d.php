<?php
// Include your database connection file (conn.php)
include "conn.php";

// Fetch data from the database
$query = "SELECT * FROM DATA";
$result = mysqli_query($conn, $query);

// Initialize variables to store total deposits and withdrawals
$totalDeposits = 0;
$totalWithdrawals = 0;

while ($data = mysqli_fetch_array($result)) {
    $Credit = $data['Deposits'];
    $Debit = $data['Withdrawals'];

    // Update the total deposits and withdrawals
    $totalDeposits += $Credit;
    $totalWithdrawals += $Debit;
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", { packages: ["corechart"] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Transaction Type', 'Amount'],
                ['Deposits', <?php echo $totalDeposits; ?>],
                ['Withdrawals', <?php echo $totalWithdrawals; ?>],
            ]);

            var options = {
                title: 'Deposits vs. Withdrawals',
                is3D: true,
                pieSliceText: 'value-and-percentage'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
        }
    </script>
</head>
<body>
<div id="piechart_3d" style="width: 450px; height: 250px;"></div>
</body>
</html>
