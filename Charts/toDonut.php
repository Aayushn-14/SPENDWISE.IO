<!DOCTYPE html>
<html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<body>
<div class="chart-container">
<canvas id="myChart" style="width:100%;max-width:600px"></canvas>

<style>
    .chart-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 600px;
      margin: 0 auto;
      margin-left: 0px;
    }

    #myChart {
      flex: 1;
    }

    .data-info {
      text-align: right;
      padding: 20px;
    }
    .summ p{
      font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', sans-serif;
    font-size: medium;
    color: #001524;
    }


  </style>

<script>
// PHP code to fetch data from the database and store
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spendwisedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT SUM(Deposits) AS TotalDeposits, SUM(Withdrawals) AS TotalWithdrawals FROM data";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $deposits = $row["TotalDeposits"];
    $withdrawals = $row["TotalWithdrawals"];
} else {
    $deposits = 0;
    $withdrawals = 0;
}

$conn->close();
?>


const deposits = <?php echo json_encode($deposits); ?>;
const withdrawals = <?php echo json_encode($withdrawals); ?>;
const barColors = [
  "#00aba9", // Depo
  "#b91d47"  // Withdr
];

const chartData = [deposits, withdrawals];
const chartLabels = ["Total Deposits", "Total Withdrawals"];

new Chart("myChart", {
  type: "doughnut",
  data: {
    labels: chartLabels,
    datasets: [{
      backgroundColor: barColors,
      data: chartData
    }]
  },
  options: {
    title: {
      display: true,
      text: "Total Debit And Credit Amount"
    },
    plugins: {
      datalabels: {
        formatter: (value, ctx) => {
          let sum = 0;
          let dataArr = ctx.chart.data.datasets[0].data;
          dataArr.map(data => {
            sum += data;
          });
          let percentage = ((value / sum) * 100).toFixed(2) + "%";
          return "₹" + value + " (" + percentage + ")";
        },
        color: "#fff",
      }
    }
  }
});
</script>
</div>
<div class="summ">
   <p>Total Deposits: <?php echo '₹' . $deposits; ?></p>
   <p>Total Withdrawals: <?php echo '₹' . $withdrawals; ?></p>
</div>

</body>
</html>
