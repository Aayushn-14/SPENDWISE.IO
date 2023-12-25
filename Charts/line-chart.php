<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>
<body>
    <canvas id="myChart" style="width:100%;max-width:600px"></canvas>

    <?php
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "spendwisedb";

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to retrieve data from the database
    $query = "SELECT Date, Withdrawals, Deposits FROM Data";
    $result = $conn->query($query);

    $dataPoints = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = $row["Date"];
            $withdrawals = $row["Withdrawals"];
            $deposits = $row["Deposits"];
            $dataPoints[] = array("date" => $date, "withdrawals" => $withdrawals, "deposits" => $deposits);
        }
    }

    // Close the database connection
    $conn->close();
    ?>

    <script>
        // JavaScript code to create the line chart
        const dataPoints = <?php echo json_encode($dataPoints); ?>;
        const labels = dataPoints.map(item => item.date);
        const withdrawalsData = dataPoints.map(item => item.withdrawals);
        const depositsData = dataPoints.map(item => item.deposits);

        new Chart("myChart", {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Deposits',
                        data: depositsData,
                        borderColor: "#00aba9",
                        fill: false
                    },
                    {
                        label: 'Withdrawals',
                        data: withdrawalsData,
                        borderColor: "#b91d47",
                        fill: false
                    }
                ]
            },
            options: {
                title: {
                    display: true,
                    text: "Deposits and Withdrawals Over Time"
                },
                scales: {
                    xAxes: [{
                        type: 'time',
                        time: {
                            unit: 'day'
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Date'
                        }
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Amount'
                        }
                    }]
                }
            }
        });
    </script>
</body>
</html>
