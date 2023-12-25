<?php
// Rest of your code...
header("Content-Type: text/html");
// Step 1: Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spendwisedb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Step 2: Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 3: Construct your SQL query
$sql = "SELECT * FROM clients 
        ORDER BY date DESC
        LIMIT 1;";
$result = $conn->query($sql);

$name = "";
$accno = "";
$ifsc = "";

// Check if the query was successful and retrieve the data
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['Name'];
    $bname = $row['Bank Name'];
    $ifsc = $row['IFSC'];
    $accno = $row['Account Number'];
    $email = $row['Email'];
    $date = $row['Date'];
}

// Step 3: Construct and execute SQL query
$sql1 = "SELECT 
            MIN(date) AS first_date,
            MAX(date) AS last_date,
            SUM(Withdrawals) AS total_withdrawals,
            SUM(Deposits) AS total_deposits
        FROM 
            data";
$result = $conn->query($sql1);

// Step 4: Retrieve the query results and store in PHP variables
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstDate = $row['first_date'];
    $lastDate = $row['last_date'];
    $totalWithdrawals = $row['total_withdrawals'];
    $totalDeposits = $row['total_deposits'];
} else {
    // Handle no results or error scenario
    $firstDate = $lastDate = $totalWithdrawals = $totalDeposits = "N/A";
}

// Step 5: Close the database connection

$sql2 = "SELECT 
            MIN(Withdrawals) AS lowest_withdrawal,
            MAX(Withdrawals) AS highest_withdrawal,
            MIN(Deposits) AS lowest_deposit,
            MAX(Deposits) AS highest_deposit,
            AVG(Withdrawals) AS avg_withdrawal,
            AVG(Deposits) AS avg_deposit
            -- WHERE Withdrawals = (SELECT MAX(Withdrawals) FROM data)
            -- WHERE Deposits = (SELECT MAX(Deposits) FROM data)

        FROM 
            data";
$result = $conn->query($sql2);

// Step 4: Retrieve the query results and store in PHP variables
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lowestWithdrawal = $row['lowest_withdrawal'];
    $highestWithdrawal = $row['highest_withdrawal'];
    $lowestDeposit = $row['lowest_deposit'];
    $highestDeposit = $row['highest_deposit'];
    $avgWithdrawal = $row['avg_withdrawal'];
    $avgDeposit = $row['avg_deposit'];
} else {
    // Handle no results or error scenario
    $lowestWithdrawal = $highestWithdrawal = $lowestDeposit = $highestDeposit = $avgWithdrawal = $avgDeposit = "N/A";
}




$conn->close();






?>
<!DOCTYPE html>
<html>

<link rel="stylesheet" href="style-a4.css">




</head>

<body>
    <div class="a4">
       <div class="a4-container">
          <div class="heading"><h1>Your Financial Summary</h1>  </div>
          <div class="details">
             <div class="column">
                 <h5>Name : <?php echo $name; ?></h5>
                 <h5>Your Bank : <?php echo $bname; ?></h5>
                 <h5>IFSC : <?php echo $ifsc; ?></h5>
             </div>
            <div class="column">
                 <h5>Account Number : <?php echo $accno; ?></h5>
                 <h5>Email : <?php echo $email; ?></h5>
                 <h5>Date : <?php echo $date; ?></h5>
              </div>
            </div>   
            
         <iframe id="myIframe" src="toDonut.php" class="centered-iframe" frameborder="0" height="400" width="100%" ></iframe>
         <div id="tbox"style="height: 110px; margin-top: 160px;">
            <b>Did You Know??<b>
            <h5>Your total withdrawals during this period [from <?php echo $firstDate; ?> to <?php echo $lastDate; ?>] amount to Rs. <?php echo $totalWithdrawals; ?>,<br> while your total deposits stand at Rs.<?php echo $totalDeposits; ?>.</h5>
         </div>  


</div>
</div>     

   <div class="a4-container">
        <div class="heading">
            <h1>Your Financial Summary - Page 2</h1>
        </div>
        <iframe src="bar.php" class="centered-iframe" frameborder="0" height="550" width="100%" ></iframe>
   
        <div id="tbox" style="height: 220px; margin-top: 50px;">
            <b>These are four Extremes -<b>
            <h4>- The highest amount received is Rs. <?php echo $highestDeposit; ?> <br>
                - The highest amount withdrawn is Rs. <?php echo $highestWithdrawal; ?><br>
                - The lowest amount deposited is Rs. <?php echo $totalDeposits; ?><br>
                - The lowest amount withdrawn is Rs. <?php echo $lowestWithdrawal; ?><br><br>

                - Average Withdrawals : Rs. <?php echo $avgWithdrawal; ?> <br>
                - Average Deposits : Rs. <?php echo $avgDeposit; ?><br>  
            </h4>
         </div>  
          </div>
    
<!-- Page3 -->
    <div class="a4-container">
        <div class="heading">
            <h1>Your Financial Summary - Page 3</h1>
        </div>
        
        <iframe src="tvalV1.php" class="centered-iframe" frameborder="0" height="550"  width="100%"  ></iframe>
            
        <div id="tbox" style="height: 110px; margin-top: 160px;">
            <b>That's a fact -<b>
            <h5> 1. Women in India own 35% of total bank accounts, but only 20% 
                of total deposits.<br>
                2. UPI hit a record of 10 billion transactions worth ₹14 trillion (US$180 billion) in August 2023.
             </h5>
         </div>  

        <!-- Add your content for the second page here -->
      </div>
      
<!-- Page4 -->
<div class="a4-container">
        <div class="heading">
            <h1>Your Financial Summary - Page 4</h1>
        </div>
        <iframe src="TtypeChart.php" class="centered-iframe" frameborder="0" height="550" width="100%" ></iframe>
    
        <div id="tbox" style="height: 110px; margin-top: 160px;">
            <b><b>
            <h5>"Do not save what is left after spending, but spend what is left after saving." - Warren Buffett</h5>
         </div>  
 <!-- Add your content for the second page here -->
      </div>
      
<!-- Page5 -->
<div class="a4-container">
        <div class="heading">
            <h1>Your Financial Summary - Page 5</h1>
        </div>
        <iframe src="monthwise.php" class="centered-iframe" frameborder="0" height="550" width="100%" ></iframe>
               
        
        <div id="tbox" style="height: 90px; margin-top: 150px; ">
            <b><b>
            <h5>"Money, like emotions, is something you must control to keep your life on the right track." - Natasha Munson</h5>
            </div>  
            <div id="processing" style="display: none;"></div>
            <button id="generatePDF">Download</button>
       
        <!-- Add your content for the second page here -->
      </div>
      
<script>
    document.getElementById('generatePDF').addEventListener('click', function() {
        // Show the processing/loading content
        document.getElementById('processing').style.display = 'block';

        // Check if all iframes are loaded
        const iframes = document.querySelectorAll('iframe');
        let allLoaded = true;

        for (let i = 0; i < iframes.length; i++) {
            if (!iframes[i].contentWindow || !iframes[i].contentWindow.document || iframes[i].contentWindow.document.readyState !== 'complete') {
                allLoaded = false;
                break;
            }
        }

        if (allLoaded) {
            // All iframes are loaded; proceed to print
            window.print();

            // Hide the processing/loading content after printing
            document.getElementById('processing').style.display = 'none';
        } else {
            // Iframes are not fully loaded; wait for a while and try printing again
            setTimeout(function() {
                window.print();

                // Hide the processing/loading content after printing
                document.getElementById('processing').style.display = 'none';
            }, 3000); // You can adjust the delay time (in milliseconds) as needed
        }
    });
</script>
</body>
</html>