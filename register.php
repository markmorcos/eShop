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
  mysqli_query($con, "INSERT INTO users VALUES(NULL, '$first_name', '$last_name', '$email', '', '$password')");
  $id = mysqli_insert_id($con);
  $_SESSION["user_id"] = $id;
  if(isset($_FILES["avatar"]["name"]))
  {
    @mkdir("avatars/$id/", 0777, true);
    $path = "avatars/$id/avatar." . pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
    $avatar = move_uploaded_file($_FILES["avatar"]["tmp_name"], $path);
  }
  mysqli_query($con, "UPDATE users SET avatar = '$path' WHERE id = $id");
  $_SESSION["notice"] = "Logged in successfully";
  header("Location: profile.php");
  die();
}
?>
<?php if(isset($_SESSION["alert"])) { ?><div data-alert class="alert-box alert"><?php echo $_SESSION["alert"]; ?> <a href="#" class="close">&times;</a></div><?php } ?>
<?php if(isset($_SESSION["notice"])) { ?><div data-alert class="alert-box info"><?php echo $_SESSION["notice"]; ?> <a href="#" class="close">&times;</a></div><?php } ?>
<div class="row">
  <div class="small-12 columns">
  <h1>Register</h1>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="register" value="true">
    <div class="row">
      <div class="small-12 columns">
        <label>First Name <input type="text" name="first_name" placeholder="First Name" value="<?php echo isset($_POST["first_name"]) ? $_POST["first_name"] : ""; ?>"></label>
        <label>Last Name <input type="text" name="last_name" placeholder="Last Name" value="<?php echo isset($_POST["last_name"]) ? $_POST["last_name"] : ""; ?>"></label>
        <label>Email <input type="email" name="email" placeholder="Email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ""; ?>"></label>
        <label>Password <input type="password" name="password" placeholder="Password"></label>
        <label>Confirm Password <input type="password" name="confirm_password" placeholder="Confirm Password"></label>
        <label>Avatar <input type="file" name="avatar" placeholder="Avatar"></label>
        <input type="submit" class="tiny expand success button" value="Register">
      </div>
    </div>
  </form>
  </div>
</div>
<?php include "footer.php" ?>