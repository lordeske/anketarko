<?php
///Mihajlo Eskic


session_start();
echo 'Logout Successful';
session_destroy();
header("Location:index.php");