<?php
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
        ORDER BY created_at DESC
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
    $ifsc = $row['ifsc'];
    $accno = $row['Account Number'];
    $email = $row['Email'];
}
// Close the database connection
$conn->close();
// Step 4: Execute the query

?>