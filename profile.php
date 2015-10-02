<?php include "header.php" ?>
<?php
if(isset($_POST["profile"]))
{
  $first_name = $_POST["first_name"];
  $last_name = $_POST["last_name"];
  $email = $_POST["email"];
  $password = crypt(sha1(md5($_POST["password"])), "st");
  $avatar = $current_user["avatar"];
  $id = $current_user["id"];
  if(isset($_FILES["avatar"]["name"]) && $_FILES["avatar"]["tmp_name"])
  {
    @mkdir("avatars/$id/", 0777, true);
    $path = "avatars/$id/avatar." . pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
    move_uploaded_file($_FILES["avatar"]["tmp_name"], $path);
    $avatar = $path;
  }
  mysqli_query($con, "UPDATE users SET first_name = '$first_name', last_name = '$last_name', avatar = '$avatar' WHERE id = $id");
  $_SESSION["notice"] = "Profile updated successfully";
  header("Location: profile.php");
  die();
}
?>
<div class="row">
  <div class="small-12 columns">
  <h1>Profile</h1>
  <img src="<?php echo $current_user["avatar"]; ?>">
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="profile" value="true">
    <div class="row">
      <div class="small-12 columns">
        <label>First Name <input type="text" name="first_name" placeholder="First Name" value="<?php echo $current_user["first_name"]; ?>"></label>
        <label>Last Name <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $current_user["last_name"]; ?>"></label>
        <label>Email <input type="email" name="email" placeholder="Email" value="<?php echo $current_user["email"]; ?>"></label>
        <label>Password <input type="password" name="password" placeholder="Password"></label>
        <label>Confirm Password <input type="password" name="confirm_password" placeholder="Confirm Password"></label>
        <label>Avatar <input type="file" name="avatar" placeholder="Avatar"></label>
        <input type="submit" class="tiny expand button" value="Update">
      </div>
    </div>
  </form>
  </div>
</div>
<?php include "footer.php" ?>
