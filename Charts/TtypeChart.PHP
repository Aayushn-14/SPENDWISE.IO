<!DOCTYPE html>
<html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<body>

<canvas id="myChart" style="width:100%;max-width:600px"></canvas>

<script>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spendwisedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate the sum of withdrawals and deposits for each transaction 
$query_TO_ATM_withdrawals = "SELECT SUM(Withdrawals) AS TO_ATM_WITHDRAWALS_SUM FROM data WHERE Particulars LIKE '%TO ATM%' OR Particulars LIKE '%ATM%'";
$result_TO_ATM_withdrawals = $conn->query($query_TO_ATM_withdrawals);
$row_TO_ATM_withdrawals = $result_TO_ATM_withdrawals->fetch_assoc();
$toATMWithdrawals = $row_TO_ATM_withdrawals["TO_ATM_WITHDRAWALS_SUM"];

$query_UPI_IN_deposits = "SELECT SUM(Deposits) AS UPI_IN_DEPOSITS_SUM FROM data WHERE Particulars LIKE '%UPI/CR%' OR Particulars LIKE '%UPI IN%' ";
$result_UPI_IN_deposits = $conn->query($query_UPI_IN_deposits);
$row_UPI_IN_deposits = $result_UPI_IN_deposits->fetch_assoc();
$upiInDeposits = $row_UPI_IN_deposits["UPI_IN_DEPOSITS_SUM"];

$query_UPI_OUT_withdrawals = "SELECT SUM(Withdrawals) AS UPI_OUT_WITHDRAWALS_SUM FROM data WHERE Particulars LIKE '%UPIOUT%' OR Particulars LIKE '%UPI/DR%'";
$result_UPI_OUT_withdrawals = $conn->query($query_UPI_OUT_withdrawals);
$row_UPI_OUT_withdrawals = $result_UPI_OUT_withdrawals->fetch_assoc();
$upiOutWithdrawals = $row_UPI_OUT_withdrawals["UPI_OUT_WITHDRAWALS_SUM"];

$query_SBIINT_deposits = "SELECT SUM(Deposits) AS SBIINT_DEPOSITS_SUM FROM data WHERE Particulars LIKE '%SBIINT%'";
$result_SBIINT_deposits = $conn->query($query_SBIINT_deposits);
$row_SBIINT_deposits = $result_SBIINT_deposits->fetch_assoc();
$sbiIntDeposits = $row_SBIINT_deposits["SBIINT_DEPOSITS_SUM"];

$query_TO_CHECQUE_withdrawals = "SELECT SUM(Withdrawals) AS TO_CHECQUE_WITHDRAWALS_SUM FROM data WHERE Particulars LIKE 'TO CHECQUE%'";
$result_TO_CHECQUE_withdrawals = $conn->query($query_TO_CHECQUE_withdrawals);
$row_TO_CHECQUE_withdrawals = $result_TO_CHECQUE_withdrawals->fetch_assoc();
$toChequeWithdrawals = $row_TO_CHECQUE_withdrawals["TO_CHECQUE_WITHDRAWALS_SUM"];

$query_OTHERS_withdrawals = "SELECT SUM(Withdrawals) AS OTHERS_WITHDRAWALS_SUM FROM data WHERE 
                Particulars NOT LIKE '%TO ATM%' 
                AND Particulars NOT LIKE '%UPI IN%'
                AND Particulars NOT LIKE '%UPI/CR%'
                AND Particulars NOT LIKE '%ATM%'
                AND Particulars NOT LIKE '%UPI/DR%'
                AND Particulars NOT LIKE '%UPIOUT%' 
                AND Particulars NOT LIKE '%SBIINT%' 
                AND Particulars NOT LIKE '%TO CHECQUE%'";
$result_OTHERS_withdrawals = $conn->query($query_OTHERS_withdrawals);
$row_OTHERS_withdrawals = $result_OTHERS_withdrawals->fetch_assoc();
$othersWithdrawals = $row_OTHERS_withdrawals["OTHERS_WITHDRAWALS_SUM"];

$conn->close();
?>

const toATMWithdrawals = <?php echo json_encode($toATMWithdrawals); ?>;
const upiInDeposits = <?php echo json_encode($upiInDeposits); ?>;
const upiOutWithdrawals = <?php echo json_encode($upiOutWithdrawals); ?>;
const sbiIntDeposits = <?php echo json_encode($sbiIntDeposits); ?>;
const toChequeWithdrawals = <?php echo json_encode($toChequeWithdrawals); ?>;
const othersWithdrawals = <?php echo json_encode($othersWithdrawals); ?>;
const barColors = [
  "#00aba9",   
  "#b91d47",   
  "#2b5797",   
  "#e8c3b9",   
  "#1e7145",   
  "#ff5733"    
];

const chartData = [toATMWithdrawals, upiInDeposits, upiOutWithdrawals, sbiIntDeposits, toChequeWithdrawals, othersWithdrawals];
const chartLabels = ["TO ATM", "UPI IN", "UPI OUT", "SBIINT", "TO CHECQUE", "OTHERS"];

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
      text: "Withdrawals by Type"
    },
    plugins: {
      datalabels: {
        color: "white",
        font: {
          weight: 'bold'
        },
        formatter: function(context) {
          return context.chart.data.labels[context.dataIndex] + ': $' + context.dataset.data[context.dataIndex];
        }
      }
    }
  }
});
</script>

</body>
</html>
