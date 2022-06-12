<?php
//Replace xxxx with your parameters
$servername = '127.0.0.1:3306';
$username = 'xxxxx';
$password = 'xxxxx';
$dbname = 'xxxxx';
$tablename = 'xxxxx';           
//
// Create connection
$conn = new mysqli($servername,$username,$password,$dbname);

if ($conn->connect_error) {
 
 die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM $tablename";

$result = $conn->query($sql);

if ($result->num_rows >0) {
 
 
 while($row[] = $result->fetch_assoc()) {
 
 $tem = $row;
 
 $json = json_encode($tem);
 }
 
} else {
 echo "No Results Found.";
}
 echo $json;
$conn->close();
?>