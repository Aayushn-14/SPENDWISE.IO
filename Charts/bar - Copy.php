<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spendwisedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Modify the SQL query to convert string dates and retrieve month names along with month-year values
// $sql = "SELECT DATE_FORMAT(STR_TO_DATE(Date, '%d/%m/%Y'), '%M %Y') AS MonthYear,
// SUM(Withdrawals) AS Withdrawals,
// SUM(Deposits) AS Deposits
// FROM data
// GROUP BY MonthYear;";
$sql = "SELECT DATE_FORMAT(STR_TO_DATE(Date, '%d/%m/%Y'), '%M %Y') AS MonthYear,
SUM(Withdrawals) AS Withdrawals,
SUM(Deposits) AS Deposits
FROM data
GROUP BY MonthYear
ORDER BY STR_TO_DATE(MonthYear, '%M %Y');";




$result = $conn->query($sql);

// Check if the query executed successfully
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch the results and store them in an associative array
$dataByMonthYear = [];
while ($row = $result->fetch_assoc()) {
    $dataByMonthYear[] = [
        "MonthYear" => $row["MonthYear"], // Change to MonthYear
        "Withdrawals" => $row["Withdrawals"],
        "Deposits" => $row["Deposits"]
    ];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<body>

<canvas id="myChart" style="width:100%;max-width:600px"></canvas>

<script>
const dataByMonthYear = <?php echo json_encode($dataByMonthYear); ?>;
const monthYearLabels = dataByMonthYear.map(item => item["MonthYear"]); // Use MonthYear here
const withdrawalsData = dataByMonthYear.map(item => item["Withdrawals"] || 0);
const depositsData = dataByMonthYear.map(item => item["Deposits"] || 0);

new Chart("myChart", {
  type: "line",
  data: {
    labels: monthYearLabels, // Use the MonthYear values for x-axis labels
    datasets: [
      {
        label: "Withdrawals",
        data: withdrawalsData,
        borderColor: "rgba(255, 99, 132, 1)", // Red color
        backgroundColor: "rgba(255, 99, 132, 0.2)", // Red color with opacity
      },
      {
        label: "Deposits",
        data: depositsData,
        borderColor: "rgba(75, 192, 192, 1)", // Blue color
        backgroundColor: "rgba(75, 192, 192, 0.2)", // Blue color with opacity
      }
    ],
  },
  options: {
    title: {
      display: true,
      text: "Withdrawals and Deposits by Month-Year",
    },
    scales: {
      yAxes: [
        {
          ticks: {
            beginAtZero: true,
          },
        },
      ],
    },
    plugins: {
      datalabels: {
        color: "black",
        font: {
          weight: 'bold'
        },
        formatter: function(context) {
          return context.dataset.label + ': $' + context.dataset.data[context.dataIndex];
        }
      }
    }
  }
});
</script>

</body>
</html>
