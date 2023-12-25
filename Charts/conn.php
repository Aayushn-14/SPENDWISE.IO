<?php
$conn= new mysqli("localhost", "root", "", "spendwisedb");
// echo "Connected :)";
if (!$conn)
{

   echo "Connection failed";

}
?>