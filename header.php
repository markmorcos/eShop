<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "eshop");
if (mysqli_connect_errno()) die("Failed to connect to MySQL: " . mysqli_connect_error());
$id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 0;
$result = mysqli_query($con, "SELECT * FROM users WHERE id = $id");
$current_user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html class="no-js">
  <head>
    <title>eShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/foundation.css">
    <link rel="stylesheet" href="foundation-icons/foundation-icons.css">
    <link rel="stylesheet" href="css/app.css">
    <script src="js/vendor/modernizr.js"></script>
  </head>
  <body>
    <div class="contain-to-grid sticky">
    <nav class="top-bar" data-topbar role="navigation" data-options="sticky_on: large">
    <ul class="title-area">
      <li class="name">
        <h1><a href="index.php">eShop</a></h1>
      </li>
      <li class="toggle-topbar menu-icon"><a href="javascript:;"><span>Menu</span></a></li>
    </ul>
    <section class="top-bar-section">
      <ul class="right">
        <?php if($current_user): ?>
          <li class="divider hide-for-small"></li>
          <li><a href="cart.php"><i class="fi-shopping-cart"></i> Cart</a></li>
          <li class="divider hide-for-small"></li>
          <li><a href="purchases.php">Purchases</a></li>
          <li class="divider hide-for-small"></li>
          <li><a href="profile.php">Profile</a></li>
          <li class="divider hide-for-small"></li>
          <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="divider hide-for-small"></li>
          <li><a href="login.php">Login</a></li>
          <li class="divider hide-for-small"></li>
          <li><a href="register.php">Register</a></li>
        <?php endif ?>
      </ul>
    </section>
    </nav>
  </div>
  <?php if(isset($_SESSION["alert"])) { ?><div data-alert class="alert-box alert"><?php echo $_SESSION["alert"]; ?> <a href="#" class="close">&times;</a></div><?php } ?>
  <?php if(isset($_SESSION["notice"])) { ?><div data-alert class="alert-box info"><?php echo $_SESSION["notice"]; ?> <a href="#" class="close">&times;</a></div><?php } ?>
