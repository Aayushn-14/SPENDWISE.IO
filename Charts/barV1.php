<?php
// Include your database connection file (conn.php)
include "conn.php";

// Fetch data from the database
$query = "SELECT * FROM DATA";
$result = mysqli_query($conn, $query);

// Create an empty array to store the data
$dataArray = [];

while ($data = mysqli_fetch_array($result)) {
    $Month = $data['Date'];
    $Credit = $data['Deposits'];
    $Debit = $data['Withdrawals'];

    // Push data to the dataArray
    $dataArray[] = ["$Month", $Credit, $Debit];
}

// Close the database connection
mysqli_close($conn);
?>

<html>
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Date', 'Deposits', 'Withdrawals'],
                <?php
                foreach ($dataArray as $row) {
                    echo "['{$row[0]}', {$row[1]}, {$row[2]}],";
                }
                ?>
            ]);

            var options = {
                title: 'Monthly Expenses',
                curveType: 'function',
                legend: { position: 'bottom' },
                hAxis: {
                    title: 'Date'
                },
                vAxis: {
                    title: 'Amount'
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('line_chart'));

            chart.draw(data, options);
        }
    </script>
</head>
<body>
<div id="line_chart" style="width: 100%; height: 500px;"></div>
</body>
</html>
