<?php 
date_default_timezone_set('Asia/Kolkata');
$error = "Problem connecting";
$dbc=mysqli_connect('localhost','root','harish','i-bill') or die($error);
$date_present=date('d M y');
$time_present=strftime("%H:%M %p");
?>