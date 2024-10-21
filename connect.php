<?php
$connect = mysqli_connect("localhost","root","","LaptopIssuingSystem") or die("connection failed!");
if($connect){
    echo '';
}
else{
    echo "not connected!";
}
?>