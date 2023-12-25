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
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Date', 'deposits', 'Withdrawals'],
                <?php
                foreach ($dataArray as $row) {
                    echo "['{$row[0]}', {$row[1]}, {$row[2]}],";
                }
                ?>
            ]);

            var options = {
                chart: {
                    title: 'The Apex',
                    
                },
                backgroundColor: '#f0f0f0',
                bars: 'vertical' // Required for Material Bar Charts.
            };

            var chart = new google.charts.Bar(document.getElementById('barchart_material'));

            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>
</head>
<body>
<div id="barchart_material" style="width: 90%; height: 380px;"></div>
</body>
</html>
