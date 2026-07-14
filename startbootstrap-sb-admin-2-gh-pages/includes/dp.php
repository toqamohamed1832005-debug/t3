<?php
$server="localhost";
$username="root";
$password="";
$databasename="sbadmin2";
$connect=new mysqli($server,$username,$password,$databasename);
if($connect->connect_error){
    die("connection error:" . $connect->connect_error);

}else{
    echo "connected...";
}
?>

