<!DOCTYPE html>
<html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<body>

<canvas id="myChart" style="width:100%;max-width:600px"></canvas>

<script>
const ctx = document.getElementById('myChart').getContext('2d');

// Make an AJAX request to fetch data from PHP
fetch('data.php')
  .then(response => response.json())
  .then(data => {
    const deposits = data.TotalDeposits;
    const withdrawals = data.TotalWithdrawals;
    const barColors = ["#00aba9", "#b91d47"];
    
    const chartData = [deposits, withdrawals];
    const chartLabels = ["Total Deposits", "Total Withdrawals"];
    
    new Chart(ctx, {
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
          text: "My Financial Summary"
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
              return "$" + value + " (" + percentage + ")";
            },
            color: "#fff",
          }
        }
      }
    });
  })
  .catch(error => {
    console.error('Error fetching data:', error);
  });

</script>

</body>
</html>
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
