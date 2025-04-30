<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "sampaga_list";

//create conn
$conn = new mysqli($servername, $username, $password,$database);

//check
if ($conn->connect_error){
    die("Connection failed:". $conn->connect_error);
}


?>