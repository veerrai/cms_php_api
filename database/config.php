<?php
// $servername = "localhost";  
// $username = "root";      
// $password = "";      
// $dbname = "airport_airlines_terminal";   

// // Create a connection
// $conn = new mysqli($servername, $username, $password, $dbname);

 //Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// echo "Connected successfully";


?>


<?php
$host = "localhost";
$db_name = "airport_airlines_terminal";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}



?>
