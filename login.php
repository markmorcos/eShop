<?php include "header.php" ?>
<?php
if($current_user)
{
  $_SESSION["notice"] = "Already logged in";
  header("Location: index.php");
  die();
}
if(isset($_POST["login"]))
{
  $email = $_POST["email"];
  $password = crypt(sha1(md5($_POST["password"])), "st");
  $result = mysqli_query($con, "SELECT * FROM users WHERE email = '$email' AND password = '$password'");
  $current_user = mysqli_fetch_assoc($result);
  $_SESSION["user_id"] = $current_user["id"];
  if(!$current_user)
  {
    $_SESSION["login_alert"] = "Wrong username and password combination";
    header("Location: login.php");
    die();
  }
  $_SESSION["login_notice"] = "Logged in successfully";
  header("Location: profile.php");
  die();
}
?>
<?php if(isset($_SESSION["login_alert"])) { ?><div data-alert class="alert-box alert"><?php echo $_SESSION["login_alert"]; ?> <a href="#" class="close">&times;</a></div><?php } ?>
<?php if(isset($_SESSION["login_notice"])) { ?><div data-alert class="alert-box info"><?php echo $_SESSION["login_notice"]; ?> <a href="#" class="close">&times;</a></div><?php } ?>
<div class="row">
  <div class="small-12 columns">
  <h1>Login</h1>
  <form method="post">
    <input type="hidden" name="login" value="true">
    <div class="row">
      <div class="small-12 columns">
        <label>Email <input type="email" name="email" placeholder="Email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ""; ?>"></label>
        <label>Password <input type="password" name="password" placeholder="Password"></label>
        <input type="submit" class="tiny expand button" value="Login">
      </div>
    </div>
  </form>
  </div>
</div>
<?php include "footer.php" ?>