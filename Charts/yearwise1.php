<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spendwisedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT DATE_FORMAT(STR_TO_DATE(Date, '%d/%m/%Y'), '%Y') AS Year,
SUM(Withdrawals) AS Withdrawals,
SUM(Deposits) AS Deposits
FROM data
GROUP BY Year
ORDER BY Year;";

$result = $conn->query($sql);

$dataByYear = [];
while ($row = $result->fetch_assoc()) {
    $dataByYear[] = [
        "Year" => $row["Year"],
        "Withdrawals" => $row["Withdrawals"],
        "Deposits" => $row["Deposits"]
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<body>

<canvas id="myChart" style="width:100%;max-width:600px"></canvas>

<script>
const dataByYear = <?php echo json_encode($dataByYear); ?>;
const yearLabels = dataByYear.map(item => item["Year"]);
const withdrawalsData = dataByYear.map(item => item["Withdrawals"] || 0);
const depositsData = dataByYear.map(item => item["Deposits"] || 0);

new Chart("myChart", {
  type: "bar", // Change to bar chart
  data: {
    labels: yearLabels,
    datasets: [
      {
        label: "Withdrawals",
        data: withdrawalsData,
        backgroundColor: "rgba(255, 99, 132, 0.6)", // Red color with opacity
      },
      {
        label: "Deposits",
        data: depositsData,
        backgroundColor: "rgba(75, 192, 192, 0.6)", // Blue color with opacity
      }
    ],
  },
  options: {
    title: {
      display: true,
      text: "Withdrawals and Deposits by Year",
    },
    scales: {
      xAxes: [{
        barPercentage: 0.8, // Adjust the width of the bars
      }],
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
