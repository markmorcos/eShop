<?php include "header.php" ?>
<?php
if($current_user)
{
  $_SESSION["notice"] = "Already logged in";
  header("Location: index.php");
  die();
}
if(isset($_POST["register"]))
{
  $first_name = $_POST["first_name"];
  $last_name = $_POST["last_name"];
  $email = $_POST["email"];
  $password = crypt(sha1(md5($_POST["password"])), "st");
  if(!mysqli_query($con, "INSERT INTO users VALUES(NULL, '$first_name', '$last_name', '$email', '', '$password')"))
  {
    $_SESSION["alert"] = "This email is already in use";
  }
  else
  {
    $id = mysqli_insert_id($con);
    if(isset($_FILES["avatar"]["name"]))
    {
      @mkdir("avatars/$id/", 0777, true);
      $path = "avatars/$id/avatar." . pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
      $avatar = move_uploaded_file($_FILES["avatar"]["tmp_name"], $path);
		  @chmod($path, 0777);
    }
    mysqli_query($con, "UPDATE users SET avatar = '$path' WHERE id = $id");
    $_SESSION["notice"] = "Registered successfully";
    header("Location: login.php");
    die();
  }
}
?>
<?php if(isset($_SESSION["alert"])) { ?><div data-alert class="alert-box alert"><?= $_SESSION["alert"]; ?> <a href="#" class="close">&times;</a></div><?php } ?>
<?php if(isset($_SESSION["notice"])) { ?><div data-alert class="alert-box info"><?= $_SESSION["notice"]; ?> <a href="#" class="close">&times;</a></div><?php } ?>
<div class="row">
  <div class="small-6 push-3 columns">
  <h1>Register</h1>
  <form method="post" enctype="multipart/form-data" onsubmit="if($('#p1').val() != $('#p2').val()) { alert('Passwords must match'); return false; }">
    <input type="hidden" name="register" value="true">
    <div class="row">
      <div class="small-12 columns">
        <label>First Name <input type="text" name="first_name" placeholder="First Name" value="<?= isset($_POST["first_name"]) ? $_POST["first_name"] : ""; ?>" required></label>
        <label>Last Name <input type="text" name="last_name" placeholder="Last Name" value="<?= isset($_POST["last_name"]) ? $_POST["last_name"] : ""; ?>" required></label>
        <label>Email <input type="email" name="email" placeholder="Email" value="<?= isset($_POST["email"]) ? $_POST["email"] : ""; ?>" required></label>
        <label>Password <input id="p1" type="password" name="password" placeholder="Password" required></label>
        <label>Confirm Password <input id="p2" type="password" name="confirm_password" placeholder="Confirm Password" required></label>
        <label>Avatar <input type="file" name="avatar" placeholder="Avatar" required></label>
        <input type="submit" class="tiny expand success button" value="Register">
      </div>
    </div>
  </form>
  </div>
</div>
<?php include "footer.php" ?>
