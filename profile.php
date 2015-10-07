<?php error_reporting(E_ALL); ini_set('display_errors', 1); ?>
<?php include "header.php" ?>
<?php
if(!$current_user)
{
  $_SESSION["notice"] = "You must be logged in to view this page";
  header("Location: login.php");
  die();
}
if(isset($_POST["profile"]))
{
  $first_name = $_POST["first_name"];
  $last_name = $_POST["last_name"];
  $empty_password = crypt(sha1(md5("")), "st");
  $password = $current_user["password"];
  $new_password = crypt(sha1(md5($_POST["password"])), "st");
  $confirm_password = crypt(sha1(md5($_POST["confirm_password"])), "st");
  if($new_password != $empty_password && $new_password == $confirm_password) $password = $new_password;
  $avatar = $current_user["avatar"];
  if(isset($_FILES["avatar"]["name"]) && $_FILES["avatar"]["name"])
  {
    mkdir("avatars/$id/", 0777, true);
    $path = "avatars/$id/avatar." . pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
    move_uploaded_file($_FILES["avatar"]["tmp_name"], $path);
		@chmod($path, 0777);
    $avatar = $path;
  }
  mysqli_query($con, "UPDATE users SET first_name = '$first_name', last_name = '$last_name', password = '$password', avatar = '$avatar' WHERE id = $id");
  $_SESSION["notice"] = "Profile updated successfully";
  header("Location: profile.php");
  die();
}
?>
<div class="row">
  <div class="small-6 push-3 columns">
  <h1>Profile</h1>
  <p><i class="fi-mail"></i> <a href="mailto:$current_user["email"]"><?= $current_user["email"]; ?></a></p>
  <form method="post" enctype="multipart/form-data" onsubmit="if($('#p1').val() != $('#p2').val()) { alert('Passwords must match'); return false; }">
    <input type="hidden" name="profile" value="true">
    <div class="row">
      <div class="small-12 columns">
        <label for="avatar">Avatar</label>
        <img src="<?= $current_user["avatar"]; ?>">
        <label><input id="avatar" type="file" name="avatar" placeholder="Avatar"></label>
        <label>First Name <input type="text" name="first_name" placeholder="First Name" value="<?= $current_user["first_name"]; ?>" required></label>
        <label>Last Name <input type="text" name="last_name" placeholder="Last Name" value="<?= $current_user["last_name"]; ?>" required></label>
        <label>Password <input id="p1" type="password" name="password" placeholder="Password"></label>
        <label>Confirm Password <input id="p2" type="password" name="confirm_password" placeholder="Confirm Password"></label>
        <input type="submit" class="tiny expand button" value="Update">
      </div>
    </div>
  </form>
  </div>
</div>
<?php include "footer.php" ?>
