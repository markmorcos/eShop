<?php
session_start();
unset($_SESSION["user_id"]);
$_SESSION["notice"] = "Logged out successfully";
header("Location: index.php");
?>
