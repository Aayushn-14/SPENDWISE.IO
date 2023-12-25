<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spendwisedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT DATE_FORMAT(STR_TO_DATE(Date, '%d/%m/%Y'), '%M %Y') AS MonthYear,
    SUM(Withdrawals) AS Withdrawals,
    SUM(Deposits) AS Deposits
    FROM data
    GROUP BY MonthYear
    ORDER BY STR_TO_DATE(CONCAT('01 ', MonthYear), '%d %M %Y') ASC;";

$result = $conn->query($sql);

$dataByMonthYear = [];
while ($row = $result->fetch_assoc()) {
    $dataByMonthYear[] = [
        "MonthYear" => $row["MonthYear"],
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

<canvas id="monthChart" style="width:100%;max-width:700px"></canvas>

<script>
const dataByMonthYear = <?php echo json_encode($dataByMonthYear); ?>;
const monthYearLabels = dataByMonthYear.map(item => item["MonthYear"]);
const withdrawalsData = dataByMonthYear.map(item => item["Withdrawals"] || 0);
const depositsData = dataByMonthYear.map(item => item["Deposits"] || 0);

new Chart("monthChart", {
  type: "bar",
  data: {
    labels: monthYearLabels,
    datasets: [
      {
        label: "Withdrawals",
        data: withdrawalsData,
        backgroundColor: "rgba(255, 99, 132, 0.6)",
      },
      {
        label: "Deposits",
        data: depositsData,
        backgroundColor: "rgba(75, 192, 192, 0.6)",
      }
    ],
  },
  options: {
    title: {
      display: true,
      text: "Withdrawals and Deposits by Month-Year",
    },
    scales: {
      xAxes: [{
        barPercentage: 0.8,
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
        },
        align: 'end',
        anchor: 'end',
        offset: 2
      }
    }
  }
});
</script>

</body>
</html>
