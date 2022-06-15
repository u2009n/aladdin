<?php

 $dbhost = "localhost";
 $dbuser = "root";
 $dbpass = "";
 $db = "pimbase";
 $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);

 if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

function CloseCon($conn)
 {
 $conn -> close();
 }
   
?>